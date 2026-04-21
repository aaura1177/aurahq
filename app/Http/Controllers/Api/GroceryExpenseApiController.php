<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GroceryExpense;
use App\Support\ApiJson;
use Illuminate\Http\Request;

class GroceryExpenseApiController extends Controller
{
    public function index(Request $request)
    {
        $q = GroceryExpense::query()->latest();
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $q->whereBetween('date', [$request->start_date, $request->end_date]);
        }
        $paginator = $q->paginate(25);

        return ApiJson::paginated($paginator, fn ($e) => [
            'id' => $e->id,
            'amount' => $e->amount,
            'remark' => $e->remark,
            'date' => $e->date,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['amount' => 'required|numeric', 'remark' => 'required|string', 'date' => 'required|date']);
        $e = GroceryExpense::create($request->only('amount', 'remark', 'date'));

        return ApiJson::created(['id' => $e->id], 'Grocery expense created successfully');
    }

    public function update(Request $request, GroceryExpense $groceryExpense)
    {
        $request->validate(['amount' => 'required|numeric', 'remark' => 'required|string']);
        $groceryExpense->update($request->only('amount', 'remark'));

        return ApiJson::ok(['id' => $groceryExpense->id], 'Updated');
    }

    public function destroy(GroceryExpense $groceryExpense)
    {
        $groceryExpense->delete();

        return ApiJson::noContent();
    }
}
