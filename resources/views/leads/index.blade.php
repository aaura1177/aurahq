@extends('layouts.admin')
@section('title', 'All Leads')
@section('header', 'Lead Management')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex flex-wrap gap-3 text-sm text-slate-600">
            <span><strong class="text-slate-800">{{ $totalLeads }}</strong> total leads</span>
            <span class="hidden sm:inline">|</span>
            <span>Pipeline: <strong class="text-slate-800">₹{{ number_format($pipelineValue, 0) }}</strong></span>
            <span class="hidden sm:inline">|</span>
            <span>Conversion: <strong class="text-slate-800">{{ $conversionRate }}%</strong></span>
        </div>
        @can('create leads')
        <a href="{{ route('leads.create') }}" class="inline-flex items-center justify-center gap-2 bg-blue-600 text-white px-4 py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition text-sm">
            <i class="fas fa-plus"></i> Add Lead
        </a>
        @endcan
    </div>

    <form method="GET" action="{{ route('leads.index') }}" class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-3 items-end">
        <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">Search</label>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Business / contact..." class="w-full border rounded-lg p-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">City</label>
            <input type="text" name="city" value="{{ $filters['city'] ?? '' }}" placeholder="City" class="w-full border rounded-lg p-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">Stage</label>
            <select name="stage" class="w-full border rounded-lg p-2 text-sm bg-white">
                <option value="">All</option>
                @foreach($stages as $st)
                <option value="{{ $st }}" {{ ($filters['stage'] ?? '') === $st ? 'selected' : '' }}>{{ str_replace('_', ' ', ucfirst($st)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">Industry</label>
            <select name="industry" class="w-full border rounded-lg p-2 text-sm bg-white">
                <option value="">All</option>
                @foreach($industries as $ind)
                <option value="{{ $ind }}" {{ ($filters['industry'] ?? '') === $ind ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $ind)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">Source</label>
            <select name="source" class="w-full border rounded-lg p-2 text-sm bg-white">
                <option value="">All</option>
                @foreach($sources as $src)
                <option value="{{ $src }}" {{ ($filters['source'] ?? '') === $src ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $src)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">Assigned</label>
            <select name="assigned_to" class="w-full border rounded-lg p-2 text-sm bg-white">
                <option value="">Anyone</option>
                @foreach($users as $u)
                <option value="{{ $u->id }}" {{ ($filters['assigned_to'] ?? '') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-slate-800 text-white py-2 rounded-lg text-sm font-semibold hover:bg-slate-900">Filter</button>
            <a href="{{ route('leads.index') }}" class="px-3 py-2 text-sm text-slate-600 border rounded-lg hover:bg-slate-50">Reset</a>
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3">Business</th>
                        <th class="px-4 py-3">Contact</th>
                        <th class="px-4 py-3">Phone</th>
                        <th class="px-4 py-3">Stage</th>
                        <th class="px-4 py-3">Industry</th>
                        <th class="px-4 py-3 text-right">Est. Value</th>
                        <th class="px-4 py-3">Follow-up</th>
                        <th class="px-4 py-3">Assigned</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($leads as $lead)
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-4 py-3 font-semibold text-slate-800">
                            <a href="{{ route('leads.show', $lead) }}" class="text-blue-600 hover:underline">{{ $lead->business_name }}</a>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $lead->contact_person ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $lead->phone ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @php $c = $lead->stage_color; @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold
                                @if($c==='slate') bg-slate-100 text-slate-700
                                @elseif($c==='blue') bg-blue-100 text-blue-700
                                @elseif($c==='cyan') bg-cyan-100 text-cyan-800
                                @elseif($c==='purple') bg-purple-100 text-purple-700
                                @elseif($c==='orange') bg-orange-100 text-orange-800
                                @elseif($c==='green') bg-green-100 text-green-700
                                @elseif($c==='red') bg-red-100 text-red-700
                                @endif">{{ $lead->stage_label }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $lead->industry ? ucfirst(str_replace('_', ' ', $lead->industry)) : '—' }}</td>
                        <td class="px-4 py-3 text-right font-medium">{{ $lead->estimated_value !== null ? '₹'.number_format($lead->estimated_value, 0) : '—' }}</td>
                        <td class="px-4 py-3">
                            @if($lead->next_follow_up)
                                <span class="{{ $lead->isOverdue() ? 'text-red-600 font-bold' : 'text-slate-600' }}">{{ $lead->next_follow_up->format('M j, Y') }}</span>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $lead->assignee?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            @can('edit leads')
                            <a href="{{ route('leads.edit', $lead) }}" class="text-blue-600 hover:underline mr-2">Edit</a>
                            @endcan
                            @can('delete leads')
                            <form action="{{ route('leads.destroy', $lead) }}" method="POST" class="inline" onsubmit="return confirm('Delete this lead?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center text-slate-400">No leads match your filters.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leads->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">{{ $leads->links() }}</div>
        @endif
    </div>
</div>
@endsection
