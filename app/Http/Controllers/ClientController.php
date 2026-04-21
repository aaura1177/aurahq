<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ClientController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view clients', only: ['index', 'show']),
            new Middleware('permission:create clients', only: ['create', 'store']),
            new Middleware('permission:edit clients', only: ['edit', 'update']),
            new Middleware('permission:delete clients', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Client::query()
            ->withCount('projects')
            ->withSum('invoices', 'total_amount')
            ->latest();

        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->boolean('inactive')) {
            $query->where('is_active', false);
        } else {
            $query->where('is_active', true);
        }

        $clients = $query->paginate(25)->withQueryString();

        $paidByClient = Invoice::query()
            ->where('status', 'paid')
            ->selectRaw('client_id, sum(total_amount) as s')
            ->groupBy('client_id')
            ->pluck('s', 'client_id');

        return view('clients.index', [
            'clients' => $clients,
            'paidByClient' => $paidByClient,
            'filters' => $request->only(['search', 'inactive']),
        ]);
    }

    public function create(Request $request)
    {
        $lead = null;
        if ($request->filled('lead_id')) {
            $lead = Lead::find($request->lead_id);
        }

        return view('clients.create', compact('lead'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['created_by'] = auth()->id();
        $data['is_active'] = $request->boolean('is_active', true);
        Client::create($data);

        return redirect()->route('clients.index')->with('success', 'Client created.');
    }

    public function show(Client $client)
    {
        $client->load([
            'lead',
            'projects' => fn ($q) => $q->latest(),
            'invoices' => fn ($q) => $q->latest(),
        ]);

        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');
        $client->update($data);

        return redirect()->route('clients.show', $client)->with('success', 'Client updated.');
    }

    public function destroy(Client $client)
    {
        if ($client->projects()->exists()) {
            return back()->with('error', 'Cannot delete a client that still has projects.');
        }
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client deleted.');
    }

    private function validated(Request $request): array
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'lead_id' => 'nullable|exists:leads,id',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $out = $request->only(['name', 'contact_person', 'email', 'phone', 'company', 'lead_id', 'notes']);
        if ($request->has('is_active')) {
            $out['is_active'] = $request->boolean('is_active');
        }

        return $out;
    }
}
