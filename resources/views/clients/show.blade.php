@extends('layouts.admin')
@section('title', $client->name)
@section('header', 'Client')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap justify-between gap-4 items-start">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">{{ $client->name }}</h2>
            @if($client->company)<p class="text-slate-600">{{ $client->company }}</p>@endif
            @if($client->lead)
            <p class="text-sm text-slate-500 mt-2">From lead: <a href="{{ route('leads.show', $client->lead) }}" class="text-blue-600 hover:underline">{{ $client->lead->business_name }}</a></p>
            @endif
        </div>
        <div class="flex gap-2">
            @can('create projects')
            <a href="{{ route('projects.create', ['client_id' => $client->id]) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold">New project</a>
            @endcan
            @can('edit clients')
            <a href="{{ route('clients.edit', $client) }}" class="border border-slate-200 px-4 py-2 rounded-lg text-sm">Edit</a>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
            <p class="text-xs font-bold text-slate-400 uppercase">Contact</p>
            <p class="text-slate-800 mt-1">{{ $client->contact_person ?? '—' }}</p>
            <p class="text-sm text-slate-600">{{ $client->phone ?? '' }} {{ $client->email ? ' · '.$client->email : '' }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
            <p class="text-xs font-bold text-slate-400 uppercase">Projects</p>
            <p class="text-2xl font-bold text-slate-900">{{ $client->projects->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
            <p class="text-xs font-bold text-slate-400 uppercase">Notes</p>
            <p class="text-sm text-slate-700 whitespace-pre-wrap">{{ $client->notes ?: '—' }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 font-bold text-slate-800">Projects</div>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-slate-100">
                @forelse($client->projects as $project)
                <tr>
                    <td class="px-4 py-3"><a href="{{ route('projects.show', $project) }}" class="font-semibold text-blue-600 hover:underline">{{ $project->name }}</a></td>
                    <td class="px-4 py-3"><span class="text-xs px-2 py-0.5 rounded-full bg-slate-100">{{ $project->status }}</span></td>
                    <td class="px-4 py-3 text-right">₹{{ $project->budget ? number_format($project->budget, 0) : '—' }}</td>
                </tr>
                @empty
                <tr><td class="px-4 py-8 text-center text-slate-400">No projects yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 font-bold text-slate-800">Invoices</div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                <tr>
                    <th class="text-left px-4 py-2">Number</th>
                    <th class="text-right px-4 py-2">Total</th>
                    <th class="text-left px-4 py-2">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($client->invoices as $inv)
                <tr>
                    <td class="px-4 py-3"><a href="{{ route('invoices.show', $inv) }}" class="text-blue-600 hover:underline">{{ $inv->invoice_number }}</a></td>
                    <td class="px-4 py-3 text-right">₹{{ number_format($inv->total_amount, 0) }}</td>
                    <td class="px-4 py-3">{{ $inv->status }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-4 py-8 text-center text-slate-400">No invoices.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
