@extends('layouts.admin')
@section('title','Content')
@section('header','Content')
@section('content')
<x-ui.page-header title="Content" subtitle="Drafts, schedule, and performance">
    <x-ui.button variant="secondary" href="{{ route('content-topics.index') }}"><i class="fas fa-book"></i> Knowledge Bank</x-ui.button>
    <form action="{{ route('content-drafts.generate') }}" method="POST" class="inline">
        @csrf
        <x-ui.button type="submit"><i class="fas fa-wand-magic-sparkles"></i> Generate draft</x-ui.button>
    </form>
</x-ui.page-header>

@php $tabs=['all'=>'All','draft'=>'Drafts','approved'=>'Approved','scheduled'=>'Scheduled','posted'=>'Posted']; @endphp
<div class="flex gap-1 mb-6 bg-slate-100 p-1 rounded-lg w-fit flex-wrap">
    @foreach($tabs as $k=>$l)
        <a href="{{ route('content-drafts.index',['status'=>$k]) }}"
           class="px-4 py-1.5 rounded-md text-sm font-medium {{ $status===$k?'bg-white text-slate-900 shadow-sm':'text-slate-500 hover:text-slate-700' }}">
            {{ $l }} <span class="text-slate-400">{{ $counts[$k]??0 }}</span>
        </a>
    @endforeach
</div>

<div class="grid gap-4 md:grid-cols-2">
@forelse($drafts as $d)
    @php
        $statusColor = match($d->status) {
            'posted' => 'success',
            'scheduled' => 'warning',
            'approved' => 'brand',
            default => 'slate',
        };
    @endphp
    <x-ui.card>
        <div class="flex items-center justify-between mb-2">
            <div class="flex gap-2 flex-wrap">
                <x-ui.badge color="brand">{{ $d->platform }}</x-ui.badge>
                <x-ui.badge color="blue">{{ $d->content_type }}</x-ui.badge>
                <x-ui.badge :color="$statusColor">{{ $d->status }}</x-ui.badge>
            </div>
        </div>
        @if($d->hook)<p class="font-semibold text-slate-800 text-sm mb-1">{{ $d->hook }}</p>@endif
        <p class="text-sm text-slate-600 whitespace-pre-line line-clamp-6">{{ $d->body }}</p>
        @if($d->hashtags)<p class="text-xs text-brand-600 mt-2">{{ $d->hashtags }}</p>@endif
        <div class="flex items-center gap-2 mt-3 pt-3 border-t border-slate-100 flex-wrap">
            <button type="button" onclick="navigator.clipboard.writeText(this.dataset.copy); this.textContent='Copied'"
                data-copy="{{ e(($d->hook ? $d->hook."\n\n" : '').$d->body.($d->hashtags ? "\n\n".$d->hashtags : '')) }}"
                class="text-xs px-3 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg font-medium">Copy</button>
            @foreach(['draft'=>'Draft','approved'=>'Approve','scheduled'=>'Schedule','posted'=>'Posted'] as $s=>$lbl)
                @if($d->status !== $s)
                <form action="{{ route('content-drafts.status',$d->id) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="{{ $s }}">
                    <button type="submit" class="text-xs px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 rounded-lg">{{ $lbl }}</button>
                </form>
                @endif
            @endforeach
            <form action="{{ route('content-drafts.destroy',$d->id) }}" method="POST" class="inline ml-auto" onsubmit="return confirm('Delete?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
            </form>
        </div>
    </x-ui.card>
@empty
    <div class="md:col-span-2">
        <x-ui.empty-state icon="fa-feather" title="No drafts yet" message="Generate one from your topic bank.">
            <form action="{{ route('content-drafts.generate') }}" method="POST">@csrf
                <x-ui.button type="submit">Generate first draft</x-ui.button>
            </form>
        </x-ui.empty-state>
    </div>
@endforelse
</div>
@endsection
