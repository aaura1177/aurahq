@extends('layouts.admin')
@section('title', 'Invoices')
@section('header', 'Invoices')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <form method="GET" class="flex flex-wrap gap-2 items-end">
            <div>
                <label class="block text-xs text-slate-500 mb-1">Status</label>
                <select name="status" class="border rounded-lg px-3 py-2 text-sm bg-white">
                    <option value="">All</option>
                    @foreach(\App\Models\Invoice::STATUSES as $st)
                    <option value="{{ $st }}" {{ ($filters['status'] ?? '') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Client</label>
                <select name="client_id" class="border rounded-lg px-3 py-2 text-sm bg-white min-w-[160px]">
                    <option value="">All</option>
                    @foreach($clients as $c)
                    <option value="{{ $c->id }}" {{ ($filters['client_id'] ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">From</label>
                <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">To</label>
                <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="border rounded-lg px-3 py-2 text-sm">
            </div>
            <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-lg text-sm">Filter</button>
        </form>
        @can('create invoices')
        <a href="{{ route('invoices.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold">New invoice</a>
        @endcan
    </div>

    <div class="bg-white rounded-xl border border-slate-100 overflow-x-auto shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 font-bold border-b">
                <tr>
                    <th class="text-left px-4 py-3">Number</th>
                    <th class="text-left px-4 py-3">Client</th>
                    <th class="text-left px-4 py-3">Project</th>
                    <th class="text-right px-4 py-3">Total</th>
                    <th class="text-left px-4 py-3">Status</th>
                    <th class="text-left px-4 py-3">Issued</th>
                    <th class="text-left px-4 py-3">Due</th>
                    <th class="text-right px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($invoices as $inv)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-mono"><a href="{{ route('invoices.show', $inv) }}" class="text-blue-600 hover:underline">{{ $inv->invoice_number }}</a></td>
                    <td class="px-4 py-3">{{ $inv->client->name }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $inv->project?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-right">₹{{ number_format($inv->total_amount, 0) }}</td>
                    <td class="px-4 py-3">
                        @php $ic = $inv->status_color; @endphp
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full
                            @if($ic==='green') bg-green-100 text-green-800
                            @elseif($ic==='blue') bg-blue-100 text-blue-800
                            @elseif($ic==='red') bg-red-100 text-red-800
                            @else bg-slate-100 text-slate-700
                            @endif">{{ $inv->status }}</span>
                    </td>
                    <td class="px-4 py-3 text-slate-600">{{ $inv->issued_date?->format('M j, Y') ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $inv->due_date?->format('M j, Y') ?? '—' }}</td>
                    <td class="px-4 py-3 text-right">
                        @can('edit invoices')
                        <a href="{{ route('invoices.edit', $inv) }}" class="text-blue-600 hover:underline text-xs">Edit</a>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-4 py-12 text-center text-slate-400">No invoices.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($invoices->hasPages())
        <div class="px-4 py-3 border-t">{{ $invoices->links() }}</div>
        @endif
    </div>
</div>
@endsection
