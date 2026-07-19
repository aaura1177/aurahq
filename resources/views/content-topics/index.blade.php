@extends('layouts.admin')
@section('title', 'Content Topics')
@section('header', 'Content Topics')

@section('content')
<div class="space-y-6 max-w-4xl">
    <p class="text-sm text-slate-600">Pool of content ideas for LinkedIn / marketing. Agents pull the next available topic via API.</p>

    <form method="post" action="{{ route('content-topics.store') }}" class="bg-white rounded-xl border border-slate-200 p-5 space-y-3">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-slate-500 mb-1">Title</label>
                <input type="text" name="title" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm" placeholder="Topic title">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Type</label>
                <select name="content_type" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm bg-white">
                    <option value="technical">Technical</option>
                    <option value="win">Win</option>
                    <option value="founder">Founder</option>
                </select>
            </div>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Angle / hook</label>
            <textarea name="angle" rows="2" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm" placeholder="The specific point to make…"></textarea>
        </div>
        <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-800 text-white text-sm font-semibold hover:bg-slate-900">Add topic</button>
    </form>

    <div class="bg-white rounded-xl border border-slate-200 divide-y divide-slate-100">
        @forelse($topics->where('is_active', true) as $topic)
            <div class="p-4 flex flex-wrap items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-medium text-slate-800">{{ $topic->title }}</p>
                    @if($topic->angle)
                        <p class="text-sm text-slate-500 mt-1">{{ $topic->angle }}</p>
                    @endif
                    <p class="text-xs text-slate-400 mt-2">
                        <span class="uppercase font-bold">{{ $topic->content_type }}</span>
                        · {{ $topic->status }}
                        @if($topic->used_at) · used {{ $topic->used_at->diffForHumans() }} @endif
                    </p>
                </div>
                <div class="flex gap-2 shrink-0">
                    @if($topic->status === 'used')
                        <form method="post" action="{{ route('content-topics.recycle', $topic) }}">
                            @csrf
                            <button class="text-xs px-2 py-1 rounded border border-slate-200 text-slate-600 hover:bg-slate-50">Reuse</button>
                        </form>
                    @endif
                    <form method="post" action="{{ route('content-topics.destroy', $topic) }}" onsubmit="return confirm('Archive this topic?');">
                        @csrf @method('DELETE')
                        <button class="text-xs px-2 py-1 rounded border border-red-100 text-red-600 hover:bg-red-50">Archive</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="p-8 text-center text-slate-400 text-sm">No topics yet — add one above.</p>
        @endforelse
    </div>
</div>
@endsection
