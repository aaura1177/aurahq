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
    $slots = [];
    foreach ([1, 2, 3] as $s) {
        $slots[$s] = [
            'title' => $focus->{"task_{$s}_title"},
            'id' => $focus->{"task_{$s}_id"},
            'done' => (bool) $focus->{"task_{$s}_completed"},
            'has' => (bool) ($focus->{"task_{$s}_title"} || $focus->{"task_{$s}_id"}),
        ];
    }
@endphp

<div class="max-w-3xl mx-auto space-y-10 pb-16"
     id="my-day"
     data-update-url="{{ route('daily-focus.update', $focus) }}"
     data-csrf="{{ csrf_token() }}">

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
        <div class="flex items-start justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Your 3 tasks today</h2>
                <p class="text-xs text-slate-400 mt-1">
                    Drag to set priority — top = highest.
                    @if(($personalTaskCount ?? $availableTasks->count()) > 0)
                        · {{ $personalTaskCount ?? $availableTasks->count() }} from
                        <a href="{{ route('tasks.personal', ['filter' => 'all']) }}" class="text-blue-600 hover:underline">My Tasks</a>
                    @endif
                </p>
            </div>
            <a href="{{ route('tasks.personal', ['filter' => 'all']) }}" class="text-xs font-medium text-blue-600 hover:underline shrink-0">Manage tasks →</a>
        </div>

        <div id="focus-slots" class="space-y-4">
            @foreach([1, 2, 3] as $slot)
                @php $s = $slots[$slot]; @endphp
                <div class="focus-slot rounded-xl border border-slate-200 bg-slate-50/50 p-4 sm:p-5"
                     data-slot="{{ $slot }}"
                     draggable="true">
                    <div class="flex items-start gap-3">
                        <button type="button"
                            class="drag-handle mt-1 shrink-0 w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-white cursor-grab active:cursor-grabbing"
                            title="Drag to reorder"
                            aria-label="Drag to reorder">
                            <i class="fas fa-grip-vertical"></i>
                        </button>

                        <form method="post" action="{{ route('daily-focus.update', $focus) }}" class="shrink-0 mt-1">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="task_{{ $slot }}_completed" value="{{ $s['done'] ? '0' : '1' }}">
                            <button type="submit"
                                class="w-6 h-6 rounded-md border-2 flex items-center justify-center transition
                                    {{ $s['done'] ? 'bg-amber-500 border-amber-500 text-white' : 'border-slate-300 bg-white hover:border-amber-400' }}"
                                title="{{ $s['done'] ? 'Mark incomplete' : 'Mark complete' }}">
                                @if($s['done'])<i class="fas fa-check text-xs"></i>@endif
                            </button>
                        </form>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xs font-mono font-bold text-slate-400">#{{ $slot }}</span>
                                @if($slot === 1)
                                    <span data-highest-badge="1" class="text-[10px] uppercase tracking-wide font-semibold text-amber-700 bg-amber-50 border border-amber-100 px-1.5 py-0.5 rounded">Highest</span>
                                @endif
                            </div>

                            {{-- View mode --}}
                            <div class="slot-view {{ $s['has'] ? '' : 'hidden' }}">
                                <p class="text-lg text-slate-800 font-medium leading-snug {{ $s['done'] ? 'line-through text-slate-400' : '' }}">
                                    {{ $s['title'] ?: 'Linked task' }}
                                </p>
                                <div class="flex flex-wrap gap-3 mt-2">
                                    <button type="button" class="slot-edit-btn text-xs font-medium text-blue-600 hover:text-blue-800 underline">
                                        Edit
                                    </button>
                                    <form method="post" action="{{ route('daily-focus.update', $focus) }}" class="inline"
                                          onsubmit="return confirm('Clear this task slot?');">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="task_{{ $slot }}_title" value="">
                                        <input type="hidden" name="task_{{ $slot }}_id" value="">
                                        <input type="hidden" name="task_{{ $slot }}_completed" value="0">
                                        <button type="submit" class="text-xs font-medium text-slate-400 hover:text-red-600 underline">Clear</button>
                                    </form>
                                </div>
                            </div>

                            {{-- Edit / add mode (always available; open by default when empty) --}}
                            <div class="slot-edit space-y-3 {{ $s['has'] ? 'hidden' : '' }}">
                                <form method="post" action="{{ route('daily-focus.update', $focus) }}" class="space-y-2">
                                    @csrf
                                    @method('PUT')
                                    <label class="block text-xs font-medium text-slate-500">Link a task</label>
                                    <div class="flex flex-col sm:flex-row gap-2">
                                        <select name="task_{{ $slot }}_id" required
                                            class="flex-1 border border-slate-200 rounded-lg px-3 py-2 text-sm bg-white">
                                            <option value="" disabled {{ $s['id'] ? '' : 'selected' }}>Choose from My Tasks…</option>
                                            @forelse($availableTasks as $t)
                                                <option value="{{ $t->id }}" {{ (int) $s['id'] === (int) $t->id ? 'selected' : '' }}>
                                                    {{ $t->title }}
                                                    · {{ str_replace('_', ' ', $t->frequency) }}
                                                    @if($t->priority !== 'normal') · {{ $t->priority }}@endif
                                                </option>
                                            @empty
                                                <option value="" disabled>No personal tasks — create one or use custom below</option>
                                            @endforelse
                                        </select>
                                        <button type="submit"
                                            class="sm:w-auto w-full px-3 py-1.5 rounded-lg bg-slate-800 text-white text-sm font-semibold hover:bg-slate-900 disabled:opacity-40"
                                            @disabled($availableTasks->isEmpty())>
                                            Link
                                        </button>
                                    </div>
                                    @if($availableTasks->isEmpty())
                                        <p class="text-xs text-amber-600">
                                            No linkable personal tasks.
                                            <a href="{{ route('tasks.create', ['context' => 'top_five']) }}" class="underline font-medium">Add a task</a>
                                            or type a custom focus below.
                                        </p>
                                    @endif
                                </form>

                                <div class="relative flex items-center gap-3 text-xs text-slate-400">
                                    <div class="flex-1 border-t border-slate-200"></div>
                                    <span>or</span>
                                    <div class="flex-1 border-t border-slate-200"></div>
                                </div>

                                <form method="post" action="{{ route('daily-focus.update', $focus) }}" class="space-y-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="task_{{ $slot }}_id" value="">
                                    <label class="block text-xs font-medium text-slate-500">Custom focus</label>
                                    <div class="flex flex-col sm:flex-row gap-2">
                                        <input type="text"
                                            name="task_{{ $slot }}_title"
                                            required
                                            maxlength="255"
                                            class="flex-1 border border-slate-200 rounded-lg px-3 py-2 text-sm"
                                            placeholder="What must get done?"
                                            value="{{ $s['title'] && ! $s['id'] ? $s['title'] : '' }}">
                                        <button type="submit" class="sm:w-auto w-full px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm font-semibold hover:bg-slate-50">
                                            Save
                                        </button>
                                    </div>
                                </form>

                                @if($s['has'])
                                    <button type="button" class="slot-cancel-btn text-xs text-slate-400 hover:text-slate-600 underline">Cancel</button>
                                @endif
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

    <section class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm" x-data="{ energy: @json(old('energy_level', $focus->energy_level)) }">
        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6">End of day</h2>
        <form method="post" action="{{ route('daily-focus.update', $focus) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <p class="text-sm font-medium text-slate-700 mb-2">Energy</p>
                <div class="flex flex-wrap gap-2">
                    @foreach(\App\Models\DailyFocus::ENERGY_LEVELS as $lvl)
                        <button type="button"
                            x-on:click="energy = '{{ $lvl }}'"
                            x-bind:class="energy === '{{ $lvl }}' ? 'bg-slate-800 text-white border-slate-800' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300'"
                            class="px-4 py-2 rounded-lg border text-sm font-medium transition">
                            {{ ucfirst($lvl) }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" name="energy_level" x-bind:value="energy" value="{{ old('energy_level', $focus->energy_level) }}">
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

            <button type="submit" class="hq-btn hq-btn-secondary">Save reflection</button>
        </form>
    </section>
</div>

<script>
(function () {
    const root = document.getElementById('my-day');
    const list = document.getElementById('focus-slots');
    if (!root || !list) return;

    const updateUrl = root.dataset.updateUrl;
    const csrf = root.dataset.csrf;
    let dragEl = null;

    Array.from(list.children).forEach(function (el) {
        el.dataset.originalSlot = el.dataset.slot;
    });

    list.querySelectorAll('.focus-slot').forEach(function (slot) {
        const view = slot.querySelector('.slot-view');
        const edit = slot.querySelector('.slot-edit');
        const editBtn = slot.querySelector('.slot-edit-btn');
        const cancelBtn = slot.querySelector('.slot-cancel-btn');

        if (editBtn && view && edit) {
            editBtn.addEventListener('click', function () {
                view.classList.add('hidden');
                edit.classList.remove('hidden');
            });
        }
        if (cancelBtn && view && edit) {
            cancelBtn.addEventListener('click', function () {
                edit.classList.add('hidden');
                view.classList.remove('hidden');
            });
        }

        slot.setAttribute('draggable', 'false');
        slot.addEventListener('mousedown', function (e) {
            slot.setAttribute('draggable', e.target.closest('.drag-handle') ? 'true' : 'false');
        });

        slot.addEventListener('dragstart', function (e) {
            if (!e.target.closest('.drag-handle') && e.target !== slot) {
                // allow if drag started while draggable=true from handle mousedown
            }
            dragEl = slot;
            slot.classList.add('opacity-50', 'ring-2', 'ring-amber-300');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', slot.dataset.originalSlot);
        });
        slot.addEventListener('dragend', function () {
            slot.classList.remove('opacity-50', 'ring-2', 'ring-amber-300');
            slot.setAttribute('draggable', 'false');
            list.querySelectorAll('.focus-slot').forEach(function (s) {
                s.classList.remove('border-amber-400');
            });
            dragEl = null;
        });
        slot.addEventListener('dragover', function (e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            if (dragEl && dragEl !== slot) slot.classList.add('border-amber-400');
        });
        slot.addEventListener('dragleave', function () {
            slot.classList.remove('border-amber-400');
        });
        slot.addEventListener('drop', function (e) {
            e.preventDefault();
            slot.classList.remove('border-amber-400');
            if (!dragEl || dragEl === slot) return;

            const children = Array.from(list.children);
            const from = children.indexOf(dragEl);
            const to = children.indexOf(slot);
            if (from < to) list.insertBefore(dragEl, slot.nextSibling);
            else list.insertBefore(dragEl, slot);

            persistOrder();
        });
    });

    async function persistOrder() {
        const permutation = Array.from(list.children).map(function (el) {
            return parseInt(el.dataset.originalSlot, 10);
        });
        try {
            await fetch(updateUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ order: permutation }),
            });
        } catch (err) {
            console.error(err);
        }
        window.location.reload();
    }
})();
</script>
@endsection
