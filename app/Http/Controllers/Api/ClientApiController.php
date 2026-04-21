<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiJson;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientApiController extends Controller
{
    public function index(Request $request)
    {
        $q = Client::query()->withCount('projects')->latest();
        if ($request->filled('search')) {
            $q->search($request->search);
        }
        $clients = $q->paginate(25);

        return ApiJson::paginated($clients);
    }

    public function show(Client $client)
    {
        $client->load(['lead', 'projects', 'invoices']);

        return ApiJson::ok($client);
    }

    public function store(Request $request)
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
        $data = $request->only(['name', 'contact_person', 'email', 'phone', 'company', 'lead_id', 'notes']);
        $data['created_by'] = $request->user()->id;
        $data['is_active'] = $request->boolean('is_active', true);
        $client = Client::create($data);

        return ApiJson::created($client, 'Client created successfully');
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'lead_id' => 'nullable|exists:leads,id',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        $client->update(array_merge(
            $request->only(['name', 'contact_person', 'email', 'phone', 'company', 'lead_id', 'notes']),
            ['is_active' => $request->has('is_active') ? $request->boolean('is_active') : $client->is_active]
        ));

        return ApiJson::ok($client->fresh(), 'Updated');
    }

    public function destroy(Client $client)
    {
        if ($client->projects()->exists()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => ['client' => ['Cannot delete a client that has projects.']],
            ], 422);
        }
        $client->delete();

        return ApiJson::noContent();
    }
}
