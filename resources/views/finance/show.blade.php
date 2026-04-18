@extends('layouts.admin')
@section('header', 'Transaction Details')
@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex justify-between">
        <div>
            <h3 class="text-lg font-bold text-slate-800">Transaction #{{ $finance->id }}</h3>
            <p class="text-slate-500">{{ $finance->transaction_date->format('F d, Y h:i A') }}</p>
        </div>
        <div class="text-right">
            @if($finance->type == 'given')
                <span class="block text-2xl font-bold text-red-600">- ₹{{ number_format($finance->amount, 2) }}</span>
                <span class="text-xs font-bold text-red-600 bg-red-100 px-2 py-1 rounded">GIVEN / DEBIT</span>
            @else
                <span class="block text-2xl font-bold text-green-600">+ ₹{{ number_format($finance->amount, 2) }}</span>
                <span class="text-xs font-bold text-green-600 bg-green-100 px-2 py-1 rounded">RECEIVED / CREDIT</span>
            @endif
        </div>
    </div>
    
    <div class="p-6 grid grid-cols-2 gap-6">
        <div>
            <p class="text-sm text-slate-500 font-bold uppercase mb-1">Contact</p>
            <p class="text-lg">{{ $finance->contact->name }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500 font-bold uppercase mb-1">Payment Method</p>
            <p class="text-lg capitalize">{{ $finance->method }}</p>
        </div>
        <div class="col-span-2">
            <p class="text-sm text-slate-500 font-bold uppercase mb-1">Remark</p>
            <p class="text-slate-700 bg-slate-50 p-3 rounded">{{ $finance->remark ?? 'No remarks provided.' }}</p>
        </div>
        <div class="col-span-2">
            <p class="text-sm text-slate-500 font-bold uppercase mb-1">Proof of Payment</p>
            @if($finance->proof_path)
                <img src="{{ asset('storage/' . $finance->proof_path) }}" class="max-w-full h-auto rounded border" alt="Proof">
            @else
                <p class="text-slate-400 italic">No proof uploaded.</p>
            @endif
        </div>
        <div class="col-span-2 text-xs text-slate-400 pt-4 border-t">
            Created by: {{ $finance->creator->name ?? 'Unknown' }} on {{ $finance->created_at->format('M d, Y') }}
        </div>
    </div>
    <div class="bg-slate-50 p-4 flex justify-end gap-2">
        <a href="{{ route('finance.index') }}" class="px-4 py-2 text-slate-600 hover:text-slate-800">Back</a>
        <a href="{{ route('finance.edit', $finance->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Edit</a>
    </div>
</div>
@endsection
