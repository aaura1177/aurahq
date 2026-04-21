@extends('layouts.admin')
@section('header', 'New Transaction')
@section('content')
@php
    $expenseCats = \App\Models\Finance::EXPENSE_CATEGORIES;
    $incomeCats = \App\Models\Finance::INCOME_CATEGORIES;
@endphp
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-slate-100 p-6"
     x-data="{
        type: @json(old('type', 'given')),
        category: @json(old('category', '')),
        expenseCats: @json($expenseCats),
        incomeCats: @json($incomeCats),
        isRecurring: @json((bool) old('is_recurring')),
     }">
    <form action="{{ route('finance.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        
        <div>
            <label class="block text-sm font-bold text-slate-700">Select Contact</label>
            <select name="finance_contact_id" class="w-full mt-1 border rounded-lg px-4 py-2 bg-white" required>
                @foreach($contacts as $contact)
                    <option value="{{ $contact->id }}">{{ $contact->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
             <div>
                <label class="block text-sm font-bold text-slate-700">Transaction Date</label>
                <input type="datetime-local" name="transaction_date" class="w-full mt-1 border rounded-lg px-4 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700">Amount (₹)</label>
                <input type="number" step="0.01" name="amount" class="w-full mt-1 border rounded-lg px-4 py-2" required>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700">Type</label>
                <select name="type" x-model="type" class="w-full mt-1 border rounded-lg px-4 py-2">
                    <option value="given">Given (Debit)</option>
                    <option value="received">Received (Credit)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700">Method</label>
                <select name="method" class="w-full mt-1 border rounded-lg px-4 py-2">
                    <option value="cash">Cash</option>
                    <option value="upi">UPI</option>
                    <option value="bank">Bank Transfer</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700">Category</label>
            <select name="category" x-model="category" class="w-full mt-1 border rounded-lg px-4 py-2 bg-white">
                <option value="">Select category…</option>
                <template x-for="[key, label] in Object.entries(type === 'given' ? expenseCats : incomeCats)" :key="key">
                    <option :value="key" x-text="label"></option>
                </template>
            </select>
            @error('category')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700">Venture</label>
            <select name="venture" class="w-full mt-1 border rounded-lg px-4 py-2 bg-white">
                @foreach(\App\Models\Finance::VENTURES as $v)
                    <option value="{{ $v }}" {{ old('venture', 'aurateria') === $v ? 'selected' : '' }}>{{ \App\Models\Finance::ventureLabel($v) }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-3">
            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                <input type="checkbox" name="is_recurring" value="1" x-model="isRecurring" class="rounded border-slate-300">
                Recurring
            </label>
        </div>
        <div x-show="isRecurring" x-cloak>
            <label class="block text-sm font-bold text-slate-700">Day of month (1–31)</label>
            <input type="number" name="recurring_day" min="1" max="31" value="{{ old('recurring_day') }}" class="w-full mt-1 border rounded-lg px-4 py-2" placeholder="e.g. 5">
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700">Proof Screenshot</label>
            <input type="file" name="proof" class="w-full mt-1 border rounded-lg px-4 py-2 text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700">Remark</label>
            <textarea name="remark" class="w-full mt-1 border rounded-lg px-4 py-2" rows="2"></textarea>
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-bold hover:bg-blue-700">Save Transaction</button>
        </div>
    </form>
</div>
@endsection
