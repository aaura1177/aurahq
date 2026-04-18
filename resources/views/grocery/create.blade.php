@extends('layouts.admin')
@section('header', 'Add Grocery Item')
@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-slate-100 p-6">
    <form action="{{ route('grocery.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-bold text-slate-700">Category Type</label>
            <select name="type" class="w-full mt-1 border rounded-lg px-4 py-2 bg-white" required>
                <option value="vegetables" {{ (request('type') == 'vegetables') ? 'selected' : '' }}>Vegetables</option>
                <option value="blinkit" {{ (request('type') == 'blinkit') ? 'selected' : '' }}>Blinkit</option>
                <option value="supermart" {{ (request('type') == 'supermart') ? 'selected' : '' }}>Supermart</option>
                <option value="others" {{ (request('type') == 'others') ? 'selected' : '' }}>Others</option>
                <option value="today" {{ (request('type') == 'today') ? 'selected' : '' }}>Today (Daily)</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-700">Item Name</label>
            <input type="text" name="item_name" class="w-full mt-1 border rounded-lg px-4 py-2" required>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700">Quantity</label>
                <input type="text" name="qty" class="w-full mt-1 border rounded-lg px-4 py-2" placeholder="e.g. 5kg, 2L" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700">Estimated Price (₹)</label>
                <input type="number" step="0.01" name="estimated_price" class="w-full mt-1 border rounded-lg px-4 py-2" value="0">
            </div>
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-700">Remark</label>
            <input type="text" name="remark" class="w-full mt-1 border rounded-lg px-4 py-2">
        </div>
        <div class="flex items-center gap-2 mt-2">
            <input type="checkbox" name="is_frequent" value="1" id="freq" class="rounded text-blue-600">
            <label for="freq" class="text-sm text-slate-700">Is this a frequent weekly item?</label>
        </div>
        <div class="pt-4">
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-bold hover:bg-blue-700">Add Item</button>
        </div>
    </form>
</div>
@endsection
