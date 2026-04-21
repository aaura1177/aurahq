@extends('layouts.admin')
@section('title', $invoice->invoice_number)
@section('header', 'Invoice')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm flex flex-wrap justify-between gap-4">
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase">Invoice</p>
            <h2 class="text-2xl font-mono font-bold text-slate-900">{{ $invoice->invoice_number }}</h2>
            <p class="text-slate-600 mt-2">{{ $invoice->client->name }}</p>
            @if($invoice->project)<p class="text-sm text-slate-500">Project: {{ $invoice->project->name }}</p>@endif
        </div>
        <div class="text-right">
            @php $ic = $invoice->status_color; @endphp
            <span class="inline-block text-sm font-bold px-3 py-1 rounded-full
                @if($ic==='green') bg-green-100 text-green-800
                @elseif($ic==='blue') bg-blue-100 text-blue-800
                @elseif($ic==='red') bg-red-100 text-red-800
                @else bg-slate-100 text-slate-700
                @endif">{{ $invoice->status }}</span>
            <p class="text-2xl font-bold text-slate-900 mt-4">₹{{ number_format($invoice->total_amount, 2) }}</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm text-sm space-y-2">
        <div class="flex justify-between"><span class="text-slate-500">Amount</span><span>₹{{ number_format($invoice->amount, 2) }}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">Tax</span><span>₹{{ number_format($invoice->tax_amount, 2) }}</span></div>
        <div class="flex justify-between border-t pt-2 font-bold"><span>Total</span><span>₹{{ number_format($invoice->total_amount, 2) }}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">Issued</span><span>{{ $invoice->issued_date?->format('M j, Y') ?? '—' }}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">Due</span><span>{{ $invoice->due_date?->format('M j, Y') ?? '—' }}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">Paid</span><span>{{ $invoice->paid_date?->format('M j, Y') ?? '—' }}</span></div>
        @if($invoice->notes)
        <p class="pt-4 text-slate-700 whitespace-pre-wrap">{{ $invoice->notes }}</p>
        @endif
    </div>

    @can('edit invoices')
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('invoices.edit', $invoice) }}" class="bg-slate-800 text-white px-4 py-2 rounded-lg text-sm">Edit</a>
        <form action="{{ route('invoices.status', $invoice) }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="sent">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">Mark sent</button>
        </form>
        <form action="{{ route('invoices.status', $invoice) }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="paid">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm">Mark paid</button>
        </form>
    </div>
    @endcan
</div>
@endsection
