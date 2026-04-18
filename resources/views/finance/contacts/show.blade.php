@extends('layouts.admin')
@section('header', $financeContact->name . ' - History')
@section('content')
<div class="flex gap-4 mb-6">
    <div class="bg-white p-4 rounded-lg shadow-sm border flex-1">
        <p class="text-xs text-slate-500 uppercase font-bold">Total Given (Debit)</p>
        <p class="text-2xl font-bold text-red-600">₹{{ number_format($financeContact->totalGiven(), 2) }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg shadow-sm border flex-1">
        <p class="text-xs text-slate-500 uppercase font-bold">Total Received (Credit)</p>
        <p class="text-2xl font-bold text-green-600">₹{{ number_format($financeContact->totalReceived(), 2) }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg shadow-sm border flex-1">
        <p class="text-xs text-slate-500 uppercase font-bold">Net Balance</p>
        @php $net = $financeContact->netBalance(); @endphp
        <p class="text-2xl font-bold {{ $net < 0 ? 'text-red-600' : 'text-green-600' }}">
            ₹{{ number_format(abs($net), 2) }} {{ $net < 0 ? '(Dr)' : '(Cr)' }}
        </p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-4 border-b border-slate-100 flex justify-between items-center">
        <h3 class="font-bold text-slate-700">Transaction History</h3>
        <a href="{{ route('finance.create') }}?contact={{ $financeContact->id }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">+ Add New</a>
    </div>
    <table class="w-full text-sm text-left">
        <thead class="bg-slate-50 text-slate-500 font-medium border-b border-slate-100">
            <tr>
                <th class="px-6 py-4">Date</th>
                <th class="px-6 py-4">Type</th>
                <th class="px-6 py-4">Amount</th>
                <th class="px-6 py-4">Method</th>
                <th class="px-6 py-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($transactions as $t)
            <tr class="hover:bg-slate-50">
                <td class="px-6 py-4 text-slate-600">{{ $t->transaction_date->format('M d, Y h:i A') }}</td>
                <td class="px-6 py-4">
                    @if($t->type == 'given')
                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">GIVEN</span>
                    @else
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">RECEIVED</span>
                    @endif
                </td>
                <td class="px-6 py-4 font-bold text-slate-800">₹{{ number_format($t->amount, 2) }}</td>
                <td class="px-6 py-4 capitalize text-slate-600">{{ $t->method }}</td>
                <td class="px-6 py-4 text-right">
                    <a href="{{ route('finance.show', $t->id) }}" class="text-blue-600 hover:underline text-xs mr-2">View Details</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-slate-500">No transactions found for this contact.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
