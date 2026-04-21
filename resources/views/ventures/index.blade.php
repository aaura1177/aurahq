@extends('layouts.admin')
@section('title', 'Ventures')
@section('header', 'Ventures')

@section('content')
<div class="space-y-6">
    <p class="text-slate-600 text-sm max-w-3xl">Overview of Aurateria portfolio ventures — status, partners, activity, and linked work.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($ventures as $v)
            <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden hover:shadow-md transition flex flex-col">
                <div class="p-5 border-b border-slate-100 flex justify-between items-start gap-3">
                    <div class="flex items-start gap-3 min-w-0">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 bg-slate-50" style="color: {{ $v->color }}">
                            <i class="fas {{ $v->icon }} text-xl"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-bold text-slate-900 text-lg leading-tight">{{ $v->name }}</h3>
                            <span class="inline-block mt-1 text-xs font-bold px-2 py-0.5 rounded
                                @if($v->status === 'active') bg-green-100 text-green-800
                                @elseif($v->status === 'paused') bg-amber-100 text-amber-800
                                @else bg-blue-100 text-blue-800 @endif">
                                {{ ucfirst($v->status) }}
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('ventures.show', $v) }}" class="text-sm font-semibold text-blue-600 hover:underline shrink-0">Open →</a>
                </div>
                <div class="p-5 flex-1 space-y-3 text-sm text-slate-600">
                    @if($v->description)
                        <p class="line-clamp-3">{{ $v->description }}</p>
                    @endif
                    <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                        @if($v->partner_name)
                            <span>Partner: <strong class="text-slate-700">{{ $v->partner_name }}</strong></span>
                        @endif
                        @if($v->partner_funded)
                            <span class="text-emerald-700 font-semibold">Partner-funded</span>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-4 text-sm border-t border-slate-100 pt-3">
                        <span><strong class="text-slate-800">{{ $v->open_projects_count }}</strong> open projects</span>
                        <span><strong class="text-slate-800">{{ $v->open_tasks_count }}</strong> open tasks</span>
                    </div>
                    @if($v->lastUpdate)
                        <p class="text-xs text-slate-500">
                            Last update <span class="text-slate-700 font-medium">{{ $v->lastUpdate->created_at->diffForHumans() }}</span>
                            — {{ Str::limit($v->lastUpdate->title, 48) }}
                        </p>
                    @else
                        <p class="text-xs text-slate-400">No updates yet.</p>
                    @endif
                </div>
                <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 flex justify-between items-center">
                    @can('create venture updates')
                    <a href="{{ route('ventures.show', $v) }}#add-update" class="text-sm font-semibold text-blue-600 hover:underline">+ Add update</a>
                    @else
                    <span></span>
                    @endcan
                    <a href="{{ route('ventures.show', $v) }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">Details →</a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
