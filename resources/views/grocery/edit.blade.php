@extends('layouts.admin')
@section('header', 'Edit Grocery Item')
@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-slate-100 p-6">
    <form action="{{ route('grocery.update', $grocery->id) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-bold text-slate-700">Category</label>
            <select name="type" class="w-full mt-1 border rounded p-2">
                <option value="vegetables" {{ $grocery->type == 'vegetables' ? 'selected' : '' }}>Vegetables</option>
                <option value="blinkit" {{ $grocery->type == 'blinkit' ? 'selected' : '' }}>Blinkit</option>
                <option value="supermart" {{ $grocery->type == 'supermart' ? 'selected' : '' }}>Supermart</option>
                <option value="others" {{ $grocery->type == 'others' ? 'selected' : '' }}>Others</option>
                <option value="today" {{ $grocery->type == 'today' ? 'selected' : '' }}>Today (Daily)</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-700">Item Name</label>
            <input type="text" name="item_name" value="{{ $grocery->item_name }}" class="w-full mt-1 border rounded p-2" required>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700">Quantity</label>
                <input type="text" name="qty" value="{{ $grocery->qty }}" class="w-full mt-1 border rounded p-2" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700">Est. Price</label>
                <input type="number" name="estimated_price" value="{{ $grocery->estimated_price }}" class="w-full mt-1 border rounded p-2">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700">Buy Price / Actual Cost</label>
                <input type="number" name="actual_cost" value="{{ $grocery->actual_cost }}" class="w-full mt-1 border rounded p-2">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700">Status</label>
                <select name="status" class="w-full mt-1 border rounded p-2">
                    <option value="pending" {{ $grocery->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="purchased" {{ $grocery->status == 'purchased' ? 'selected' : '' }}>Purchased</option>
                    <option value="unavailable" {{ $grocery->status == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                </select>
            </div>
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">Update Item</button>
    </form>
</div>
@endsection
