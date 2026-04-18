@extends('layouts.admin')
@section('header', 'Edit Template')
@section('content')
<div class="max-w-xl mx-auto bg-white rounded-xl shadow-sm border border-slate-100 p-6">
    <form action="{{ route('grocery.templates.update', $template->id) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-bold text-slate-700">Target Category</label>
            <select name="type" class="w-full border rounded p-2 bg-white" required>
                <option value="today" {{ ($template->type ?? 'today') == 'today' ? 'selected' : '' }}>Today (Daily)</option>
                <option value="vegetables" {{ ($template->type ?? '') == 'vegetables' ? 'selected' : '' }}>Vegetables</option>
                <option value="blinkit" {{ ($template->type ?? '') == 'blinkit' ? 'selected' : '' }}>Blinkit</option>
                <option value="supermart" {{ ($template->type ?? '') == 'supermart' ? 'selected' : '' }}>Supermart</option>
                <option value="others" {{ ($template->type ?? '') == 'others' ? 'selected' : '' }}>Others</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-700">Item Name</label>
            <input type="text" name="item_name" value="{{ $template->item_name }}" class="w-full border rounded p-2" required>
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-700">Default Qty</label>
            <input type="text" name="qty" value="{{ $template->qty }}" class="w-full border rounded p-2" required>
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-700">Est. Price</label>
            <input type="number" name="estimated_price" value="{{ $template->estimated_price }}" class="w-full border rounded p-2">
        </div>
        <div class="flex gap-4">
            <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded">Update</button>
            <a href="{{ route('grocery.templates') }}" class="flex-1 bg-slate-100 text-slate-700 py-2 rounded text-center">Cancel</a>
        </div>
    </form>
</div>
@endsection
