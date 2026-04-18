<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FinanceContact;
use Illuminate\Http\Request;

class FinanceContactApiController extends Controller
{
    public function index(Request $request)
    {
        $query = FinanceContact::withCount('finances')->latest();
        if (!$request->user()->hasRole('super-admin')) {
            $query->where('is_active', true);
        }
        return response()->json(['data' => $query->get()->map(fn ($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'phone' => $c->phone,
            'email' => $c->email,
            'is_active' => $c->is_active,
            'finances_count' => $c->finances_count,
        ])]);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255', 'phone' => 'nullable', 'email' => 'nullable|email']);
        $c = FinanceContact::create($request->only('name', 'phone', 'email'));
        return response()->json(['message' => 'Created', 'data' => ['id' => $c->id]], 201);
    }

    public function show(Request $request, FinanceContact $financeContact)
    {
        if (!$financeContact->is_active && !$request->user()->hasRole('super-admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $q = $financeContact->finances()->latest();
        if (!$request->user()->hasRole('super-admin')) {
            $q->where('is_active', true);
        }
        return response()->json([
            'data' => [
                'id' => $financeContact->id,
                'name' => $financeContact->name,
                'phone' => $financeContact->phone,
                'email' => $financeContact->email,
                'is_active' => $financeContact->is_active,
                'finances' => $q->get()->map(fn ($f) => [
                    'id' => $f->id,
                    'amount' => $f->amount,
                    'type' => $f->type,
                    'transaction_date' => $f->transaction_date?->format('Y-m-d'),
                ]),
            ],
        ]);
    }

    public function update(Request $request, FinanceContact $financeContact)
    {
        $request->validate(['name' => 'required|string|max:255', 'phone' => 'nullable', 'email' => 'nullable|email']);
        $financeContact->update($request->only('name', 'phone', 'email'));
        return response()->json(['message' => 'Updated']);
    }

    public function destroy(FinanceContact $financeContact)
    {
        if ($financeContact->finances()->exists()) {
            return response()->json(['message' => 'Contact has transactions'], 422);
        }
        $financeContact->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function toggle(FinanceContact $financeContact)
    {
        $financeContact->is_active = !$financeContact->is_active;
        $financeContact->save();
        return response()->json(['message' => 'OK', 'data' => ['is_active' => $financeContact->is_active]]);
    }
}
