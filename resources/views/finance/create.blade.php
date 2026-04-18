@extends('layouts.admin')
@section('header', 'New Transaction')
@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-slate-100 p-6">
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
                <select name="type" class="w-full mt-1 border rounded-lg px-4 py-2">
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
