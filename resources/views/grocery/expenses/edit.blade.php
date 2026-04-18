@extends('layouts.admin')
@section('header', 'Edit Variable Expense')
@section('content')
<div class="max-w-xl mx-auto bg-white rounded-xl shadow-sm border border-slate-100 p-6">
    <form action="{{ route('grocery-expenses.update', $groceryExpense->id) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-bold text-slate-700">Description</label>
            <input type="text" name="remark" value="{{ $groceryExpense->remark }}" class="w-full border rounded p-2" required>
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-700">Amount (₹)</label>
            <input type="number" name="amount" value="{{ $groceryExpense->amount }}" step="0.01" class="w-full border rounded p-2" required>
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">Update Expense</button>
    </form>
</div>
@endsection