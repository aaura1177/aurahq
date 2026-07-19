@extends('layouts.admin')
@section('title', 'Knowledge Bank')
@section('header', 'Knowledge Bank')

@section('content')
@php
    $activeTopics = $topics->where('is_active', true)->values();
    $typeColors = ['technical' => 'blue', 'win' => 'success', 'founder' => 'brand'];
@endphp

<div class="space-y-6 max-w-4xl" x-data="{ showAdd: false, filter: 'all' }">
    <x-ui.page-header title="Knowledge Bank" subtitle="Topics that feed content generation">
        <x-ui.button variant="secondary" href="{{ route('content-drafts.index') }}">
            <i class="fas fa-feather"></i> Content drafts
        </x-ui.button>
        <x-ui.button type="button" @click="showAdd = !showAdd">
            <i class="fas fa-plus"></i> <span x-text="showAdd ? 'Cancel' : 'Add topic'"></span>
        </x-ui.button>
    </x-ui.page-header>

    <div x-show="showAdd" x-cloak class="overflow-hidden">
        <x-ui.card title="New topic">
            <form method="post" action="{{ route('content-topics.store') }}" class="space-y-3">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-slate-500 mb-1">Title</label>
                        <input type="text" name="title" required value="{{ old('title') }}"
                               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-400"
                               placeholder="Topic title">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Type</label>
                        <select name="content_type" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-400">
                            <option value="technical" @selected(old('content_type', 'technical') === 'technical')>Technical</option>
                            <option value="win" @selected(old('content_type') === 'win')>Win</option>
                            <option value="founder" @selected(old('content_type') === 'founder')>Founder</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Angle / hook</label>
                    <textarea name="angle" rows="2"
                              class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-400"
                              placeholder="The specific point to make…">{{ old('angle') }}</textarea>
                </div>
                <x-ui.button type="submit">Save topic</x-ui.button>
            </form>
        </x-ui.card>
    </div>

    @if($activeTopics->isNotEmpty())
        <div class="flex gap-1 bg-slate-100 p-1 rounded-lg w-fit flex-wrap">
            @foreach(['all' => 'All', 'technical' => 'Technical', 'win' => 'Win', 'founder' => 'Founder'] as $key => $label)
                <button type="button" @click="filter = '{{ $key }}'"
                        :class="filter === '{{ $key }}' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                        class="px-3 py-1.5 rounded-md text-sm font-medium transition">
                    {{ $label }}
                    <span class="text-slate-400">
                        {{ $key === 'all' ? $activeTopics->count() : $activeTopics->where('content_type', $key)->count() }}
                    </span>
                </button>
            @endforeach
        </div>
    @endif

    @if($activeTopics->isEmpty())
        <x-ui.empty-state icon="fa-book" title="No topics yet" message="Add topics here — they feed draft generation.">
            <x-ui.button type="button" @click="showAdd = true">Add first topic</x-ui.button>
        </x-ui.empty-state>
    @else
        <div class="space-y-2">
            @foreach($activeTopics as $topic)
                <div x-show="filter === 'all' || filter === '{{ $topic->content_type }}'"
                     x-data="{ editing: false }"
                     class="bg-white rounded-xl border border-slate-200 shadow-sm">
                    <div class="p-4" x-show="!editing">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-slate-800">{{ $topic->title }}</p>
                                @if($topic->angle)
                                    <p class="text-sm text-slate-500 mt-1">{{ $topic->angle }}</p>
                                @endif
                                <div class="flex flex-wrap items-center gap-2 mt-2">
                                    <x-ui.badge :color="$typeColors[$topic->content_type] ?? 'slate'">{{ $topic->content_type }}</x-ui.badge>
                                    <x-ui.badge :color="$topic->status === 'available' ? 'success' : 'slate'">{{ $topic->status }}</x-ui.badge>
                                    @if($topic->used_at)
                                        <span class="text-xs text-slate-400">used {{ $topic->used_at->diffForHumans() }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <button type="button" @click="editing = true"
                                        class="text-xs px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium">
                                    Edit
                                </button>
                                @if($topic->status === 'used')
                                    <form method="post" action="{{ route('content-topics.recycle', $topic) }}">
                                        @csrf
                                        <button type="submit" class="text-xs px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium">
                                            Recycle
                                        </button>
                                    </form>
                                @endif
                                <form method="post" action="{{ route('content-topics.destroy', $topic) }}" onsubmit="return confirm('Archive this topic?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs px-2.5 py-1.5 rounded-lg border border-red-100 text-red-600 hover:bg-red-50 font-medium">
                                        Archive
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="p-4" x-show="editing" x-cloak>
                        <form method="post" action="{{ route('content-topics.update', $topic) }}" class="space-y-3">
                            @csrf
                            @method('PATCH')
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-medium text-slate-500 mb-1">Title</label>
                                    <input type="text" name="title" required value="{{ $topic->title }}"
                                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">Type</label>
                                    <select name="content_type" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-400">
                                        <option value="technical" @selected($topic->content_type === 'technical')>Technical</option>
                                        <option value="win" @selected($topic->content_type === 'win')>Win</option>
                                        <option value="founder" @selected($topic->content_type === 'founder')>Founder</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Angle / hook</label>
                                <textarea name="angle" rows="2"
                                          class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-400">{{ $topic->angle }}</textarea>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-ui.button type="submit">Save</x-ui.button>
                                <x-ui.button type="button" variant="secondary" @click="editing = false">Cancel</x-ui.button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
