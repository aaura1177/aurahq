@extends('layouts.admin')
@section('title', 'New invoice')
@section('header', 'New invoice')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded-xl shadow-sm border border-slate-100">
    <form action="{{ route('invoices.store') }}" method="POST" class="space-y-4">
        @csrf
        <p class="text-sm text-slate-500">Leave invoice number blank to auto-generate (INV-YYYYMM-###).</p>
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Invoice number (optional)</label>
            <input type="text" name="invoice_number" value="{{ old('invoice_number') }}" class="w-full border rounded-lg p-2.5 text-sm font-mono" placeholder="Auto">
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Client <span class="text-red-500">*</span></label>
            <select name="client_id" required class="w-full border rounded-lg p-2.5 text-sm bg-white">
                @foreach($clients as $c)
                <option value="{{ $c->id }}" {{ old('client_id', $prefillClient ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Project</label>
            <select name="project_id" class="w-full border rounded-lg p-2.5 text-sm bg-white">
                <option value="">— None —</option>
                @foreach($projects as $p)
                <option value="{{ $p->id }}" {{ old('project_id', $prefillProject ?? '') == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->client->name }})</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Amount (₹) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="amount" value="{{ old('amount') }}" required class="w-full border rounded-lg p-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Tax (₹)</label>
                <input type="number" step="0.01" min="0" name="tax_amount" value="{{ old('tax_amount', 0) }}" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Total (₹) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="total_amount" value="{{ old('total_amount') }}" required class="w-full border rounded-lg p-2.5 text-sm">
            </div>
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Status</label>
            <select name="status" class="w-full border rounded-lg p-2.5 text-sm bg-white">
                @foreach(\App\Models\Invoice::STATUSES as $st)
                <option value="{{ $st }}" {{ old('status', 'draft') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Issued</label>
                <input type="date" name="issued_date" value="{{ old('issued_date') }}" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Due</label>
                <input type="date" name="due_date" value="{{ old('due_date') }}" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Payment method</label>
                <input type="text" name="payment_method" value="{{ old('payment_method') }}" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Notes</label>
            <textarea name="notes" rows="2" class="w-full border rounded-lg p-2.5 text-sm">{{ old('notes') }}</textarea>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-bold">Save</button>
            <a href="{{ route('invoices.index') }}" class="px-6 py-2.5 border rounded-lg text-slate-600">Cancel</a>
        </div>
    </form>
</div>
@endsection
