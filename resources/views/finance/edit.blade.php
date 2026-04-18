@extends('layouts.admin')
@section('header', 'Edit Transaction')
@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-slate-100 p-6">
    <form action="{{ route('finance.update', $finance->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')
        
        <div>
            <label class="block text-sm font-bold text-slate-700">Select Contact</label>
            <select name="finance_contact_id" class="w-full mt-1 border rounded-lg px-4 py-2 bg-white" required>
                @foreach($contacts as $contact)
                    <option value="{{ $contact->id }}" {{ $finance->finance_contact_id == $contact->id ? 'selected' : '' }}>
                        {{ $contact->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
             <div>
                <label class="block text-sm font-bold text-slate-700">Transaction Date</label>
                <input type="datetime-local" name="transaction_date" value="{{ $finance->transaction_date->format('Y-m-d\TH:i') }}" class="w-full mt-1 border rounded-lg px-4 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700">Amount (₹)</label>
                <input type="number" step="0.01" name="amount" value="{{ $finance->amount }}" class="w-full mt-1 border rounded-lg px-4 py-2" required>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700">Type</label>
                <select name="type" class="w-full mt-1 border rounded-lg px-4 py-2">
                    <option value="given" {{ $finance->type == 'given' ? 'selected' : '' }}>Given (Debit)</option>
                    <option value="received" {{ $finance->type == 'received' ? 'selected' : '' }}>Received (Credit)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700">Method</label>
                <select name="method" class="w-full mt-1 border rounded-lg px-4 py-2">
                    <option value="cash" {{ $finance->method == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="upi" {{ $finance->method == 'upi' ? 'selected' : '' }}>UPI</option>
                    <option value="bank" {{ $finance->method == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700">Proof Screenshot (Optional)</label>
            <input type="file" name="proof" class="w-full mt-1 border rounded-lg px-4 py-2 text-sm text-slate-500">
            @if($finance->proof_path)
                <p class="text-xs text-green-600 mt-1">Current file: {{ basename($finance->proof_path) }}</p>
            @endif
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700">Remark</label>
            <textarea name="remark" class="w-full mt-1 border rounded-lg px-4 py-2" rows="2">{{ $finance->remark }}</textarea>
        </div>

        <div class="pt-4 flex gap-4">
            <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg font-bold hover:bg-blue-700">Update Transaction</button>
            <a href="{{ route('finance.index') }}" class="flex-1 bg-slate-100 text-slate-700 py-2 rounded-lg font-bold hover:bg-slate-200 text-center">Cancel</a>
        </div>
    </form>
</div>
@endsection