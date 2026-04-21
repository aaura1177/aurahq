@extends('layouts.admin')
@section('title', $venture->name)
@section('header', $venture->name)

@section('content')
<div class="space-y-8 max-w-5xl">
    <div class="flex flex-wrap gap-4 items-start justify-between">
        <div class="flex items-start gap-4">
            <div class="w-14 h-14 rounded-xl flex items-center justify-center bg-slate-50 shrink-0" style="color: {{ $venture->color }}">
                <i class="fas {{ $venture->icon }} text-2xl"></i>
            </div>
            <div>
                <p class="text-sm text-slate-500">Venture</p>
                <span class="inline-block mt-1 text-xs font-bold px-2 py-0.5 rounded
                    @if($venture->status === 'active') bg-green-100 text-green-800
                    @elseif($venture->status === 'paused') bg-amber-100 text-amber-800
                    @else bg-blue-100 text-blue-800 @endif">
                    {{ ucfirst($venture->status) }}
                </span>
                @if($venture->partner_name)
                    <p class="text-sm text-slate-600 mt-2">Partner: <strong>{{ $venture->partner_name }}</strong>
                        @if($venture->partner_funded)<span class="text-emerald-700"> · Funded</span>@endif
                    </p>
                @endif
                @if($venture->description)
                    <p class="text-slate-700 mt-3">{{ $venture->description }}</p>
                @endif
            </div>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
            <div class="bg-white border border-slate-100 rounded-lg px-4 py-3 shadow-sm">
                <p class="text-xs text-slate-500 uppercase">Open projects</p>
                <p class="text-xl font-bold text-slate-900">{{ $openProjectsCount }}</p>
            </div>
            <div class="bg-white border border-slate-100 rounded-lg px-4 py-3 shadow-sm">
                <p class="text-xs text-slate-500 uppercase">Open tasks</p>
                <p class="text-xl font-bold text-slate-900">{{ $openTasksCount }}</p>
            </div>
            <div class="bg-white border border-slate-100 rounded-lg px-4 py-3 shadow-sm">
                <p class="text-xs text-slate-500 uppercase">Finance in</p>
                <p class="text-xl font-bold text-green-700">₹{{ number_format($financeReceived, 0) }}</p>
            </div>
            <div class="bg-white border border-slate-100 rounded-lg px-4 py-3 shadow-sm">
                <p class="text-xs text-slate-500 uppercase">Finance out</p>
                <p class="text-xl font-bold text-red-700">₹{{ number_format($financeGiven, 0) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 flex flex-wrap justify-between items-center gap-2">
        <p class="text-sm text-slate-700"><strong>Net (all-time, tagged transactions):</strong>
            <span class="font-bold {{ $financeNet >= 0 ? 'text-green-700' : 'text-red-700' }}">₹{{ number_format($financeNet, 0) }}</span>
        </p>
        <p class="text-xs text-slate-500">Sums from finance records where venture = <code class="bg-white px-1 rounded">{{ $venture->slug }}</code></p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div>
            <h3 class="font-bold text-slate-800 mb-4">Linked projects</h3>
            <div class="bg-white rounded-xl border border-slate-100 divide-y divide-slate-100">
                @forelse($venture->projects as $project)
                    <a href="{{ route('projects.show', $project) }}" class="block p-4 hover:bg-slate-50 transition">
                        <div class="flex justify-between gap-2">
                            <span class="font-semibold text-slate-900">{{ $project->name }}</span>
                            <span class="text-xs font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-700">{{ $project->status }}</span>
                        </div>
                        <p class="text-sm text-slate-500 mt-1">{{ $project->client?->name ?? 'Client' }}</p>
                    </a>
                @empty
                    <p class="p-6 text-sm text-slate-500">No projects tagged with this venture yet.</p>
                @endforelse
            </div>
        </div>

        <div id="add-update">
            @can('create venture updates')
            <h3 class="font-bold text-slate-800 mb-4">Post an update</h3>
            <form action="{{ route('ventures.updates.store', $venture) }}" method="post" class="bg-white rounded-xl border border-slate-100 p-5 space-y-4 shadow-sm">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded-lg px-3 py-2" required maxlength="255">
                    @error('title')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Type</label>
                    <select name="type" class="w-full border rounded-lg px-3 py-2 bg-white">
                        @foreach(\App\Models\VentureUpdate::TYPES as $t)
                            <option value="{{ $t }}" {{ old('type', 'update') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Content</label>
                    <textarea name="content" rows="4" class="w-full border rounded-lg px-3 py-2" placeholder="Details…">{{ old('content') }}</textarea>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-bold hover:bg-blue-700">Publish update</button>
            </form>
            @endcan

            <h3 class="font-bold text-slate-800 mb-4 mt-10">Timeline</h3>
            <div class="space-y-4">
                @forelse($venture->updates as $u)
                    <div class="relative pl-8 border-l-2 border-slate-200 pb-6 last:pb-0">
                        <span class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-white border-2 border-slate-300"></span>
                        <div class="bg-white rounded-xl border border-slate-100 p-4 shadow-sm">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <i class="fas {{ $u->type_icon }} {{ $u->type_color }}"></i>
                                <span class="text-xs font-bold uppercase text-slate-400">{{ $u->type }}</span>
                                <span class="text-xs text-slate-400">{{ $u->created_at->diffForHumans() }}</span>
                            </div>
                            <h4 class="font-bold text-slate-900">{{ $u->title }}</h4>
                            @if($u->content)
                                <p class="text-sm text-slate-600 mt-2 whitespace-pre-wrap">{{ $u->content }}</p>
                            @endif
                            <p class="text-xs text-slate-400 mt-2">— {{ $u->user?->name ?? 'User' }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No updates yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
