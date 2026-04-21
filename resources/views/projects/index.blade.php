@extends('layouts.admin')
@section('title', 'Projects')
@section('header', 'Projects')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <form method="GET" class="flex flex-wrap gap-2 items-end">
            <div>
                <label class="block text-xs text-slate-500 mb-1">Status</label>
                <select name="status" class="border rounded-lg px-3 py-2 text-sm bg-white">
                    <option value="">All</option>
                    @foreach(\App\Models\Project::STATUSES as $st)
                    <option value="{{ $st }}" {{ ($filters['status'] ?? '') === $st ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $st)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Venture</label>
                <select name="venture" class="border rounded-lg px-3 py-2 text-sm bg-white">
                    <option value="">All</option>
                    @foreach(\App\Models\Project::VENTURES as $v)
                    <option value="{{ $v }}" {{ ($filters['venture'] ?? '') === $v ? 'selected' : '' }}>{{ ucfirst($v) }}</option>
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
            <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-lg text-sm">Filter</button>
        </form>
        @can('create projects')
        <a href="{{ route('projects.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold">New project</a>
        @endcan
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($projects as $project)
        <a href="{{ route('projects.show', $project) }}" class="block bg-white p-5 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition text-left">
            <div class="flex justify-between items-start gap-2 mb-2">
                <h3 class="font-bold text-slate-900 text-lg">{{ $project->name }}</h3>
                @php $sc = $project->status_color; @endphp
                <span class="text-xs font-bold px-2 py-0.5 rounded-full
                    @if($sc==='green') bg-green-100 text-green-800
                    @elseif($sc==='amber') bg-amber-100 text-amber-900
                    @elseif($sc==='blue') bg-blue-100 text-blue-800
                    @elseif($sc==='red') bg-red-100 text-red-800
                    @else bg-slate-100 text-slate-700
                    @endif">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</span>
            </div>
            <p class="text-sm text-slate-500 mb-3">{{ $project->client->name }}</p>
            <div class="flex flex-wrap gap-2 mb-3">
                <span class="text-xs bg-indigo-50 text-indigo-800 px-2 py-0.5 rounded">{{ $project->venture }}</span>
                @if($project->budget)
                <span class="text-xs text-slate-600">Budget ₹{{ number_format($project->budget, 0) }}</span>
                @endif
            </div>
            <div class="mb-1 flex justify-between text-xs text-slate-500">
                <span>Milestones</span>
                <span>{{ $project->milestone_progress }}%</span>
            </div>
            <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-green-500 rounded-full" style="width: {{ min(100, $project->milestone_progress) }}%"></div>
            </div>
        </a>
        @empty
        <p class="text-slate-400 col-span-full text-center py-12">No projects match filters.</p>
        @endforelse
    </div>
</div>
@endsection
