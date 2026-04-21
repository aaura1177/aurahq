<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Support\ApiJson;
use Illuminate\Http\Request;

class InvoiceApiController extends Controller
{
    public function index(Request $request)
    {
        $q = Invoice::with(['client', 'project'])->latest();
        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }
        if ($request->filled('client_id')) {
            $q->where('client_id', $request->client_id);
        }

        $invoices = $q->paginate(25);

        return ApiJson::paginated($invoices);
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['client', 'project', 'creator']);

        return ApiJson::ok($invoice);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;
        if (empty($data['invoice_number'])) {
            unset($data['invoice_number']);
        }
        $invoice = Invoice::create($data);

        return ApiJson::created($invoice, 'Invoice created successfully');
    }

    public function update(Request $request, Invoice $invoice)
    {
        $invoice->update($this->validated($request, false));

        return ApiJson::ok($invoice->fresh(), 'Updated');
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

        return ApiJson::ok($invoice, 'Updated');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return ApiJson::noContent();
    }

    private function validated(Request $request, bool $isCreate = true): array
    {
        $request->validate([
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
        ]);
        $data = $request->only([
            'client_id', 'project_id', 'invoice_number', 'amount', 'tax_amount', 'total_amount',
            'status', 'issued_date', 'due_date', 'paid_date', 'payment_method', 'notes',
        ]);
        $data['tax_amount'] = $data['tax_amount'] ?? 0;

        return $data;
    }
}
