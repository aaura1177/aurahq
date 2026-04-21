@extends('layouts.admin')
@section('title', 'Clients')
@section('header', 'Clients')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <form method="GET" class="flex flex-wrap gap-2 items-center">
            <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search name, company, email..." class="border rounded-lg px-3 py-2 text-sm w-full max-w-md">
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="inactive" value="1" {{ !empty($filters['inactive']) ? 'checked' : '' }} onchange="this.form.submit()"> Show inactive
            </label>
            <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-lg text-sm">Search</button>
        </form>
        @can('create clients')
        <a href="{{ route('clients.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700">Add client</a>
        @endcan
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 font-bold border-b">
                <tr>
                    <th class="text-left px-4 py-3">Name</th>
                    <th class="text-left px-4 py-3">Company</th>
                    <th class="text-right px-4 py-3">Projects</th>
                    <th class="text-right px-4 py-3">Total invoiced</th>
                    <th class="text-right px-4 py-3">Total paid</th>
                    <th class="text-right px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($clients as $client)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-semibold text-slate-800">
                        <a href="{{ route('clients.show', $client) }}" class="text-blue-600 hover:underline">{{ $client->name }}</a>
                    </td>
                    <td class="px-4 py-3 text-slate-600">{{ $client->company ?? '—' }}</td>
                    <td class="px-4 py-3 text-right">{{ $client->projects_count }}</td>
                    <td class="px-4 py-3 text-right">₹{{ number_format($client->invoices_sum_total_amount ?? 0, 0) }}</td>
                    <td class="px-4 py-3 text-right text-green-700 font-medium">₹{{ number_format($paidByClient[$client->id] ?? 0, 0) }}</td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        @can('edit clients')
                        <a href="{{ route('clients.edit', $client) }}" class="text-blue-600 hover:underline text-sm">Edit</a>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-slate-400">No clients yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($clients->hasPages())
        <div class="px-4 py-3 border-t">{{ $clients->links() }}</div>
        @endif
    </div>
</div>
@endsection
