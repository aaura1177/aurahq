<?php
namespace App\Http\Controllers;
use App\Models\GroceryExpense;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class GroceryExpenseController extends Controller implements HasMiddleware
{
    public static function middleware(): array {
        return [new Middleware('permission:edit grocery expenses', only: ['edit', 'update', 'destroy'])];
    }

    public function edit(GroceryExpense $groceryExpense) {
        return view('grocery.expenses.edit', compact('groceryExpense'));
    }

    public function update(Request $request, GroceryExpense $groceryExpense) {
        $request->validate(['amount' => 'required|numeric', 'remark' => 'required']);
        $groceryExpense->update($request->only('amount', 'remark'));
        return redirect()->route('grocery.index', ['type' => 'today'])->with('success', 'Expense updated.');
    }

    public function destroy(GroceryExpense $groceryExpense) {
        $groceryExpense->delete();
        return redirect()->route('grocery.index', ['type' => 'today'])->with('success', 'Expense deleted.');
    }
}
