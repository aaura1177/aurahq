@extends('layouts.admin')
@section('title', 'Edit Revenue Target')
@section('header', 'Edit Revenue Target')

@section('content')
<div class="max-w-lg mx-auto bg-white rounded-xl border border-slate-100 shadow-sm p-6">
    <form action="{{ route('revenue-targets.update', $target) }}" method="post" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Month</label>
            <input type="month" name="month" value="{{ old('month', $target->month->format('Y-m')) }}" class="w-full border rounded-lg px-3 py-2" required>
            @error('month')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Target amount (₹)</label>
            <input type="number" step="0.01" min="0" name="target_amount" value="{{ old('target_amount', $target->target_amount) }}" class="w-full border rounded-lg px-3 py-2" required>
            @error('target_amount')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Notes (optional)</label>
            <textarea name="notes" rows="2" class="w-full border rounded-lg px-3 py-2">{{ old('notes', $target->notes) }}</textarea>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg font-bold hover:bg-blue-700">Update</button>
            <a href="{{ route('revenue-targets.index') }}" class="flex-1 bg-slate-100 text-slate-700 py-2 rounded-lg font-bold text-center hover:bg-slate-200">Cancel</a>
        </div>
    </form>
</div>
@endsection
