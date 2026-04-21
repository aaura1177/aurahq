@extends('layouts.admin')
@section('title', $project->name)
@section('header', 'Project')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap justify-between gap-4">
        <div>
            <p class="text-sm text-slate-500"><a href="{{ route('clients.show', $project->client) }}" class="text-blue-600 hover:underline">{{ $project->client->name }}</a></p>
            <h2 class="text-2xl font-bold text-slate-900">{{ $project->name }}</h2>
            <div class="flex flex-wrap gap-2 mt-2">
                <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-800">{{ $project->venture }}</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</span>
            </div>
        </div>
        <div class="flex gap-2">
            @can('create invoices')
            <a href="{{ route('invoices.create', ['client_id' => $project->client_id, 'project_id' => $project->id]) }}" class="bg-amber-600 text-white px-4 py-2 rounded-lg text-sm font-semibold">New invoice</a>
            @endcan
            @can('edit projects')
            <a href="{{ route('projects.edit', $project) }}" class="border border-slate-200 px-4 py-2 rounded-lg text-sm">Edit</a>
            @endcan
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm">
        <p class="text-slate-700 whitespace-pre-wrap">{{ $project->description ?: 'No description.' }}</p>
        <dl class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 text-sm">
            <div><dt class="text-slate-400 font-bold uppercase text-xs">Budget</dt><dd>₹{{ $project->budget ? number_format($project->budget, 0) : '—' }}</dd></div>
            <div><dt class="text-slate-400 font-bold uppercase text-xs">Progress</dt><dd>{{ $project->milestone_progress }}%</dd></div>
            <div><dt class="text-slate-400 font-bold uppercase text-xs">Start</dt><dd>{{ $project->start_date?->format('M j, Y') ?? '—' }}</dd></div>
            <div><dt class="text-slate-400 font-bold uppercase text-xs">Due</dt><dd>{{ $project->expected_end_date?->format('M j, Y') ?? '—' }}</dd></div>
        </dl>
    </div>

    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6">
        <h3 class="font-bold text-slate-800 mb-4">Milestones</h3>
        <ul class="space-y-3">
            @foreach($project->milestones as $m)
            <li class="flex flex-wrap items-start justify-between gap-4 border border-slate-100 rounded-lg p-3 {{ $m->is_completed ? 'bg-green-50/50' : '' }}">
                <div>
                    <p class="font-semibold text-slate-800">{{ $m->title }}</p>
                    @if($m->description)<p class="text-sm text-slate-600">{{ $m->description }}</p>@endif
                    <p class="text-xs text-slate-400 mt-1">Due {{ $m->due_date?->format('M j, Y') ?? '—' }} @if($m->is_overdue)<span class="text-red-600 font-bold">Overdue</span>@endif</p>
                </div>
                @can('edit projects')
                <form action="{{ route('milestones.complete', $m) }}" method="POST" class="shrink-0">
                    @csrf
                    @method('PATCH')
                    @if($m->completed_at)
                    <input type="hidden" name="completed" value="0">
                    <button type="submit" class="text-xs text-slate-600 underline">Mark incomplete</button>
                    @else
                    <input type="hidden" name="completed" value="1">
                    <button type="submit" class="text-xs bg-green-600 text-white px-3 py-1 rounded-lg">Complete</button>
                    @endif
                </form>
                @endcan
            </li>
            @endforeach
        </ul>
        @can('edit projects')
        <form action="{{ route('projects.milestones.store', $project) }}" method="POST" class="mt-6 flex flex-wrap gap-2 items-end border-t border-slate-100 pt-4">
            @csrf
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-slate-500 mb-1">Add milestone</label>
                <input type="text" name="title" placeholder="Title" required class="w-full border rounded-lg p-2 text-sm">
            </div>
            <div>
                <input type="date" name="due_date" class="border rounded-lg p-2 text-sm">
            </div>
            <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-lg text-sm">Add</button>
        </form>
        @endcan
    </div>

    <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b font-bold">Linked tasks</div>
        <ul class="divide-y divide-slate-100">
            @forelse($project->tasks as $t)
            <li class="px-4 py-3 flex justify-between items-center">
                <a href="{{ route('tasks.show', $t) }}" class="text-blue-600 hover:underline font-medium">{{ $t->title }}</a>
                <span class="text-xs text-slate-500">{{ $t->status }}</span>
            </li>
            @empty
            <li class="px-4 py-8 text-center text-slate-400">No tasks linked. Assign a project when creating or editing a task.</li>
            @endforelse
        </ul>
    </div>

    <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b font-bold">Invoices</div>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-slate-100">
                @forelse($project->invoices as $inv)
                <tr>
                    <td class="px-4 py-3"><a href="{{ route('invoices.show', $inv) }}" class="text-blue-600 hover:underline">{{ $inv->invoice_number }}</a></td>
                    <td class="px-4 py-3 text-right">₹{{ number_format($inv->total_amount, 0) }}</td>
                    <td class="px-4 py-3">{{ $inv->status }}</td>
                </tr>
                @empty
                <tr><td class="px-4 py-8 text-center text-slate-400">No invoices for this project.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
