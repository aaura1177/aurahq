<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class InvoiceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view invoices', only: ['index', 'show']),
            new Middleware('permission:create invoices', only: ['create', 'store']),
            new Middleware('permission:edit invoices', only: ['edit', 'update', 'updateStatus']),
            new Middleware('permission:delete invoices', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Invoice::with(['client', 'project'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->filled('from')) {
            $query->whereDate('issued_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('issued_date', '<=', $request->to);
        }

        $invoices = $query->paginate(25)->withQueryString();
        $clients = Client::active()->orderBy('name')->get();

        return view('invoices.index', [
            'invoices' => $invoices,
            'clients' => $clients,
            'filters' => $request->only(['status', 'client_id', 'from', 'to']),
        ]);
    }

    public function create(Request $request)
    {
        $clients = Client::active()->orderBy('name')->get();
        $projects = Project::active()->with('client')->orderBy('name')->get();
        $prefillClient = $request->get('client_id');
        $prefillProject = $request->get('project_id');

        return view('invoices.create', compact('clients', 'projects', 'prefillClient', 'prefillProject'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedInvoice($request);
        $data['created_by'] = auth()->id();
        if (empty($data['invoice_number'])) {
            unset($data['invoice_number']);
        }
        Invoice::create($data);

        return redirect()->route('invoices.index')->with('success', 'Invoice created.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['client', 'project', 'creator']);

        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $clients = Client::active()->orderBy('name')->get();
        $projects = Project::active()->with('client')->orderBy('name')->get();

        return view('invoices.edit', compact('invoice', 'clients', 'projects'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $invoice->update($this->validatedInvoice($request, false));

        return redirect()->route('invoices.index')->with('success', 'Invoice updated.');
    }

    public function updateStatus(Request $request, Invoice $invoice)
    {
        $request->validate(['status' => 'required|in:'.implode(',', Invoice::STATUSES)]);

        $invoice->status = $request->status;
        if ($request->status === 'paid') {
            $invoice->paid_date = $invoice->paid_date ?? now()->toDateString();
        } elseif ($request->status === 'sent' && ! $invoice->issued_date) {
            $invoice->issued_date = now()->toDateString();
        }
        if ($request->status !== 'paid') {
            $invoice->paid_date = null;
        }
        $invoice->save();

        return back()->with('success', 'Invoice status updated.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted.');
    }

    private function validatedInvoice(Request $request, bool $isCreate = true): array
    {
        $rules = [
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'invoice_number' => ($isCreate ? 'nullable' : 'required').'|string|max:50',
            'amount' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:'.implode(',', Invoice::STATUSES),
            'issued_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'paid_date' => 'nullable|date',
            'payment_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ];

        $request->validate($rules);

        $data = $request->only([
            'client_id', 'project_id', 'invoice_number', 'amount', 'tax_amount', 'total_amount',
            'status', 'issued_date', 'due_date', 'paid_date', 'payment_method', 'notes',
        ]);

        $data['tax_amount'] = $data['tax_amount'] ?? 0;

        return $data;
    }
}
