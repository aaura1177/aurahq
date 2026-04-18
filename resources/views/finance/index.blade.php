@extends('layouts.admin')
@section('title', 'Finance')
@section('header', 'All Transactions')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-4 border-b border-slate-100 flex justify-between items-center">
        <h3 class="font-bold text-slate-700">Recent Transactions</h3>
        @can('create finance')
        <a href="{{ route('finance.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">+ Add Transaction</a>
        @endcan
    </div>
    <table class="w-full text-sm text-left">
        <thead class="bg-slate-50 text-slate-500 font-medium border-b border-slate-100">
            <tr>
                <th class="px-6 py-4">Date</th>
                <th class="px-6 py-4">Contact</th>
                <th class="px-6 py-4">Amount</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($finances as $finance)
            <tr class="hover:bg-slate-50">
                <td class="px-6 py-4 text-slate-500">{{ $finance->transaction_date->format('M d, Y') }}</td>
                <td class="px-6 py-4 font-medium text-slate-800">{{ $finance->contact->name ?? 'Unknown' }}</td>
                <td class="px-6 py-4 font-bold text-slate-800">₹{{ number_format($finance->amount, 2) }}</td>
                <td class="px-6 py-4">
                    @if(!$finance->is_active)
                        <span class="bg-slate-200 text-slate-600 px-2 py-1 rounded text-xs font-bold">Disabled</span>
                    @elseif($finance->type == 'given')
                        <span class="text-red-600 font-bold bg-red-100 px-2 py-1 rounded text-xs">DEBIT</span>
                    @else
                        <span class="text-green-600 font-bold bg-green-100 px-2 py-1 rounded text-xs">CREDIT</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('finance.show', $finance->id) }}" class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-bold">View</a>
                        
                        @can('edit finance')
                        <form action="{{ route('finance.toggle', $finance->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <button class="px-2 py-1 rounded text-xs font-bold {{ $finance->is_active ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700' }}">
                                {{ $finance->is_active ? 'Disable' : 'Enable' }}
                            </button>
                        </form>
                        @endcan

                        @can('delete finance')
                        <form action="{{ route('finance.destroy', $finance->id) }}" method="POST" onsubmit="return confirm('Delete?');">
                            @csrf @method('DELETE')
                            <button class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">Del</button>
                        </form>
                        @endcan
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4">{{ $finances->links() }}</div>
</div>
@endsection
