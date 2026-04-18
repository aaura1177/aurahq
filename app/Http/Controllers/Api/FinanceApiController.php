<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Finance;
use App\Models\FinanceContact;
use Illuminate\Http\Request;

class FinanceApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Finance::with('contact')->latest();
        if (!$request->user()->hasRole('super-admin')) {
            $query->where('is_active', true);
        }
        $perPage = $request->query('per_page', 15);
        $finances = $query->paginate($perPage);
        $finances->getCollection()->transform(fn ($f) => [
            'id' => $f->id,
            'finance_contact_id' => $f->finance_contact_id,
            'contact' => $f->contact ? ['id' => $f->contact->id, 'name' => $f->contact->name] : null,
            'amount' => $f->amount,
            'type' => $f->type,
            'method' => $f->method,
            'transaction_date' => $f->transaction_date?->format('Y-m-d'),
            'notes' => $f->notes,
            'is_active' => $f->is_active,
        ]);
        return response()->json($finances);
    }

    public function show(Request $request, Finance $finance)
    {
        if (!$finance->is_active && !$request->user()->hasRole('super-admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $finance->load('contact');
        return response()->json(['data' => [
            'id' => $finance->id,
            'contact' => $finance->contact,
            'amount' => $finance->amount,
            'type' => $finance->type,
            'method' => $finance->method,
            'transaction_date' => $finance->transaction_date?->format('Y-m-d'),
            'notes' => $finance->notes,
        ]]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'finance_contact_id' => 'required|exists:finance_contacts,id',
            'amount' => 'required|numeric',
            'type' => 'required|in:given,received',
            'method' => 'required|string|max:100',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);
        $data = $request->only('finance_contact_id', 'amount', 'type', 'method', 'transaction_date', 'notes');
        $data['created_by'] = $request->user()->id;
        $finance = Finance::create($data);
        return response()->json(['message' => 'Created', 'data' => ['id' => $finance->id]], 201);
    }

    public function update(Request $request, Finance $finance)
    {
        $request->validate([
            'finance_contact_id' => 'sometimes|exists:finance_contacts,id',
            'amount' => 'sometimes|numeric',
            'type' => 'sometimes|in:given,received',
            'method' => 'sometimes|string|max:100',
            'transaction_date' => 'sometimes|date',
            'notes' => 'nullable|string',
        ]);
        $finance->update($request->only('finance_contact_id', 'amount', 'type', 'method', 'transaction_date', 'notes'));
        return response()->json(['message' => 'Updated', 'data' => ['id' => $finance->id]]);
    }

    public function destroy(Finance $finance)
    {
        $finance->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function toggle(Finance $finance)
    {
        $finance->is_active = !$finance->is_active;
        $finance->save();
        return response()->json(['data' => ['is_active' => $finance->is_active]]);
    }

    public function contacts()
    {
        $contacts = FinanceContact::where('is_active', true)->get(['id', 'name', 'phone', 'email']);
        return response()->json(['data' => $contacts]);
    }
}
