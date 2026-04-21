@extends('layouts.admin')
@section('title', 'My Day')
@section('header', 'My Day')

@section('content')
@php
    $trackStyles = [
        'aurateria' => 'border-l-4 border-l-green-500 bg-green-50/40',
        'main_client' => 'border-l-4 border-l-blue-500 bg-blue-50/40',
        'partner' => 'border-l-4 border-l-purple-500 bg-purple-50/40',
        'break' => 'border-l-4 border-l-slate-300 bg-slate-50',
        'other' => 'border-l-4 border-l-slate-200 bg-white',
    ];
    $slotHas = [];
    foreach ([1, 2, 3] as $s) {
        $slotHas[$s] = (bool) ($focus->{"task_{$s}_title"} || $focus->{"task_{$s}_id"});
    }
@endphp

<div class="max-w-3xl mx-auto space-y-10 pb-16"
     x-data="myDayFocus({
        focusId: {{ $focus->id }},
        updateUrl: @json(route('daily-focus.update', $focus)),
        csrf: document.querySelector('meta[name=csrf-token]').content,
        editing: {
            1: {{ $slotHas[1] ? 'false' : 'true' }},
            2: {{ $slotHas[2] ? 'false' : 'true' }},
            3: {{ $slotHas[3] ? 'false' : 'true' }},
        },
     })">

    <div class="text-center sm:text-left">
        <p class="text-slate-500 text-sm">Good morning, <span class="text-slate-800 font-semibold">{{ auth()->user()->name }}</span></p>
        <h1 class="text-2xl font-semibold text-slate-800 mt-1 tracking-tight">{{ now()->format('l, F j, Y') }}</h1>
        <div class="mt-4 flex flex-wrap items-center justify-center sm:justify-start gap-3">
            @if($streak > 0)
                <span class="inline-flex items-center gap-1.5 text-lg font-bold text-orange-600 bg-orange-50 border border-orange-100 px-3 py-1 rounded-full">
                    <span>🔥</span> {{ $streak }}-day streak
                </span>
            @else
                <span class="text-sm text-slate-500">Start your streak today — complete all 3 tasks.</span>
            @endif
            <a href="{{ route('daily-focus.history') }}" class="text-sm font-medium text-slate-500 hover:text-slate-800 underline">History</a>
        </div>
    </div>

    @if($yesterday)
    <details class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm text-sm text-slate-600">
        <summary class="cursor-pointer font-semibold text-slate-800 outline-none">Yesterday: {{ $yesterday->completed_count }}/3 completed</summary>
        <div class="mt-3 space-y-2 border-t border-slate-100 pt-3">
            <p><span class="text-slate-400">Tasks:</span>
                {{ collect([$yesterday->task_1_title, $yesterday->task_2_title, $yesterday->task_3_title])->filter()->implode(' · ') ?: '—' }}
            </p>
            @if($yesterday->tomorrow_focus)
                <p class="text-slate-700"><span class="text-slate-400">You noted for today:</span> {{ $yesterday->tomorrow_focus }}</p>
            @endif
        </div>
    </details>
    @endif

    <section class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm">
        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6">Your 3 tasks today</h2>
        <div class="space-y-8">
            @foreach([1,2,3] as $slot)
                @php
                    $title = $focus->{"task_{$slot}_title"};
                    $tid = $focus->{"task_{$slot}_id"};
                    $done = $focus->{"task_{$slot}_completed"};
                @endphp
                <div class="border-b border-slate-100 last:border-0 pb-8 last:pb-0">
                    <div class="flex items-start gap-4">
                        <label class="flex items-center gap-3 shrink-0 mt-1 cursor-pointer">
                            <input type="checkbox"
                                class="w-6 h-6 rounded-md border-slate-300 text-amber-500 focus:ring-amber-400 transition"
                                {{ $done ? 'checked' : '' }}
                                @change="patchTask({{ $slot }}, $event.target.checked)">
                            <span class="text-slate-400 text-sm font-mono w-4">{{ $slot }}.</span>
                        </label>
                        <div class="flex-1 min-w-0 space-y-3">
                            <div x-show="!editing[{{ $slot }}] && @json($slotHas[$slot])" x-cloak>
                                <div>
                                    <p class="text-lg text-slate-800 font-medium leading-snug">{{ $title ?: 'Linked task' }}</p>
                                    <button type="button" @click="editing[{{ $slot }}] = true" class="text-xs text-slate-400 hover:text-slate-600 underline mt-1">Change</button>
                                </div>
                            </div>
                            <div x-show="editing[{{ $slot }}] || !@json($slotHas[$slot])" x-cloak class="space-y-2">
                                <select
                                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm bg-white"
                                    data-slot="{{ $slot }}"
                                    @change="const v = $event.target.value; if(v){ pickTask({{ $slot }}, v); }">
                                    <option value="">Link a task from your list…</option>
                                    @foreach($availableTasks as $t)
                                        <option value="{{ $t->id }}" {{ (int)$tid === (int)$t->id ? 'selected' : '' }}>{{ $t->title }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-slate-400">Or type a custom focus:</p>
                                <input type="text"
                                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm"
                                    placeholder="Custom task"
                                    value="{{ $title && !$tid ? $title : '' }}"
                                    @keydown.enter.prevent="$event.target.blur()"
                                    @blur="const v = $event.target.value.trim(); if(v){ customTask({{ $slot }}, v); }">
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section>
        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Time blocks <span class="font-normal text-slate-400 normal-case">(reference)</span></h2>
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm divide-y divide-slate-100">
            @foreach($timeBlocks as $block)
                @php $st = $trackStyles[$block['track'] ?? 'other'] ?? $trackStyles['other']; @endphp
                <div class="flex flex-wrap gap-2 px-4 py-3 text-sm {{ $st }}">
                    <span class="font-mono text-slate-500 shrink-0 w-36">{{ $block['start'] }}–{{ $block['end'] }}</span>
                    <span class="text-slate-800">{{ $block['label'] }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm"
        x-data="{ energy: @json(old('energy_level', $focus->energy_level)) }">
        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6">End of day</h2>
        <form method="post" action="{{ route('daily-focus.update', $focus) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <p class="text-sm font-medium text-slate-700 mb-2">Energy</p>
                <div class="flex flex-wrap gap-2">
                    @foreach(\App\Models\DailyFocus::ENERGY_LEVELS as $lvl)
                        <button type="button"
                            @click="energy = '{{ $lvl }}'"
                            :class="energy === '{{ $lvl }}' ? 'bg-slate-800 text-white border-slate-800' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300'"
                            class="px-4 py-2 rounded-lg border text-sm font-medium transition">
                            {{ ucfirst($lvl) }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" name="energy_level" :value="energy">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">What went well today</label>
                <textarea name="wins" rows="3" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-slate-300 focus:border-slate-300" placeholder="Wins…">{{ old('wins', $focus->wins) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Notes for tomorrow</label>
                <textarea name="tomorrow_focus" rows="3" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-slate-300 focus:border-slate-300" placeholder="Tomorrow focus…">{{ old('tomorrow_focus', $focus->tomorrow_focus) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Other notes</label>
                <textarea name="end_of_day_note" rows="3" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-slate-300 focus:border-slate-300" placeholder="Anything else…">{{ old('end_of_day_note', $focus->end_of_day_note) }}</textarea>
            </div>

            <button type="submit" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-800 text-white text-sm font-semibold hover:bg-slate-900 transition shadow-sm">
                Save reflection
            </button>
        </form>
    </section>
</div>

<script>
function myDayFocus(cfg) {
    return {
        editing: cfg.editing,
        async patchTask(slot, completed) {
            const body = {};
            body['task_' + slot + '_completed'] = completed;
            await this.sendPatch(body);
        },
        async pickTask(slot, taskId) {
            const body = {};
            body['task_' + slot + '_id'] = parseInt(taskId, 10);
            body['task_' + slot + '_title'] = null;
            await this.sendPatch(body);
            window.location.reload();
        },
        async customTask(slot, title) {
            const body = {};
            body['task_' + slot + '_id'] = null;
            body['task_' + slot + '_title'] = title;
            await this.sendPatch(body);
            window.location.reload();
        },
        async sendPatch(body) {
            await fetch(cfg.updateUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': cfg.csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(body),
            });
        },
    };
}
</script>
@endsection
