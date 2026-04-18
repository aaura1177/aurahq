<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GroceryExpense;
use Illuminate\Http\Request;

class GroceryExpenseApiController extends Controller
{
    public function index(Request $request)
    {
        $q = GroceryExpense::query()->latest();
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $q->whereBetween('date', [$request->start_date, $request->end_date]);
        }
        return response()->json(['data' => $q->get()->map(fn ($e) => [
            'id' => $e->id,
            'amount' => $e->amount,
            'remark' => $e->remark,
            'date' => $e->date,
        ])]);
    }

    public function store(Request $request)
    {
        $request->validate(['amount' => 'required|numeric', 'remark' => 'required|string', 'date' => 'required|date']);
        $e = GroceryExpense::create($request->only('amount', 'remark', 'date'));
        return response()->json(['message' => 'Created', 'data' => ['id' => $e->id]], 201);
    }

    public function update(Request $request, GroceryExpense $groceryExpense)
    {
        $request->validate(['amount' => 'required|numeric', 'remark' => 'required|string']);
        $groceryExpense->update($request->only('amount', 'remark'));
        return response()->json(['message' => 'Updated']);
    }

    public function destroy(GroceryExpense $groceryExpense)
    {
        $groceryExpense->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
