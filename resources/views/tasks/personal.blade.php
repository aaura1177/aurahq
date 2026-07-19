@extends('layouts.admin')
@section('title', 'My Tasks')
@section('header', 'My Personal Tasks')

@section('content')
@php
    $filterCounts = $filterCounts ?? ['daily' => 0, 'weekly' => 0, 'top_five' => 0, 'urgent' => 0, 'all' => 0];
    $filterLabels = [
        'daily' => 'Daily',
        'weekly' => 'Weekly',
        'top_five' => 'Top 5',
        'urgent' => 'Urgent',
        'all' => 'All Active',
    ];
@endphp
<div class="flex justify-between mb-6 flex-wrap gap-4">
    <div class="grid grid-cols-2 md:grid-cols-5 gap-2 w-full md:w-auto">
        @foreach($filterLabels as $key => $label)
            <a href="{{ route('tasks.personal', ['filter' => $key]) }}"
               class="px-3 py-1.5 rounded-lg text-sm font-medium text-center shadow-sm {{ $filter == $key ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                {{ $label }}
                <span class="{{ $filter == $key ? 'text-blue-100' : 'text-slate-400' }} font-normal">({{ $filterCounts[$key] ?? 0 }})</span>
            </a>
        @endforeach
    </div>

    <div class="flex items-center gap-2">
        <form action="{{ route('tasks.personal') }}" method="GET" class="flex gap-2">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <select name="status" class="border rounded-lg px-3 py-1.5 text-sm bg-white" onchange="this.form.submit()">
                <option value="">Status: Any</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active (Enabled)</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive (Disabled)</option>
            </select>
        </form>

        @can('create tasks')
        <a href="{{ route('tasks.create', ['context' => $filter === 'all' ? 'daily' : $filter]) }}" class="hq-btn hq-btn-primary">+ Add Task</a>
        @endcan
    </div>
</div>

@if(session('error'))
<div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg border border-red-200">
    {{ session('error') }}
</div>
@endif

<div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100"
     id="personal-tasks"
     data-reorder-url="{{ route('tasks.reorder') }}"
     data-csrf="{{ csrf_token() }}">
    <div class="flex items-center justify-between mb-4 gap-3 flex-wrap">
        <h3 class="font-bold text-lg text-slate-800">{{ $filterLabels[$filter] ?? ucfirst(str_replace('_', ' ', $filter)) }} ({{ count($adminTasks) }})</h3>
        <div class="flex items-center gap-3 text-xs text-slate-400">
            <a href="{{ route('daily-focus.today') }}" class="text-blue-600 hover:underline font-medium">Open My Day →</a>
            <span>Drag ⋮⋮ to set priority — top = critical</span>
        </div>
    </div>
    <div id="task-list" class="space-y-3">
        @forelse($adminTasks as $task)
        <div class="task-row flex items-center justify-between p-4 border border-slate-100 rounded-lg hover:border-blue-300 transition group bg-white {{ !$task->is_active ? 'bg-gray-50 opacity-75' : '' }}"
             data-id="{{ $task->id }}"
             draggable="false">
            <div class="flex items-center gap-3 min-w-0">
                <button type="button"
                    class="drag-handle shrink-0 w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-50 cursor-grab active:cursor-grabbing"
                    title="Drag to set priority"
                    aria-label="Drag to reorder">
                    <i class="fas fa-grip-vertical"></i>
                </button>
                <div class="w-6 h-6 rounded-full border-2 border-slate-300 group-hover:border-blue-500 flex items-center justify-center shrink-0">
                    <form action="{{ route('tasks.toggle', $task->id) }}" method="POST" class="flex items-center justify-center w-full h-full">
                        @csrf @method('PATCH')
                        <button class="w-3 h-3 bg-transparent {{ $task->is_active ? 'group-hover:bg-blue-500' : 'bg-red-400' }} rounded-full transition-colors" title="Toggle Active/Inactive"></button>
                    </form>
                </div>
                <div class="min-w-0">
                    <p class="font-medium text-slate-800 {{ $task->status == 'completed' ? 'line-through text-slate-400' : '' }}">
                        {{ $task->title }}
                        @if(!$task->is_active) <span class="text-xs text-red-500 font-bold ml-2">(Inactive)</span> @endif
                    </p>
                    <p class="text-xs text-slate-400">
                         Type: <span class="uppercase font-bold text-xs">{{ str_replace('_', ' ', $task->frequency) }}</span>
                         @if($task->due_date) | Due: {{ $task->due_date->format('M d') }} @endif
                    </p>
                </div>
            </div>
            <div class="flex gap-2 items-center shrink-0">
                <span class="priority-badge px-2 py-1 rounded text-xs font-bold uppercase
                    {{ $task->priority == 'critical' ? 'bg-red-100 text-red-600' : ($task->priority == 'urgent' ? 'bg-orange-100 text-orange-600' : 'bg-gray-100 text-slate-600') }}">
                    {{ $task->priority }}
                </span>
                <span class="px-2 py-1 bg-gray-100 text-slate-600 rounded text-xs uppercase font-bold">{{ $task->status }}</span>

                <a href="{{ route('tasks.edit', $task->id) }}" class="text-slate-300 hover:text-blue-500" title="Edit"><i class="fas fa-edit"></i></a>
                <a href="{{ route('tasks.show', $task->id) }}" class="text-slate-300 hover:text-blue-500" title="View"><i class="fas fa-eye"></i></a>

                <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Delete?');">
                    @csrf @method('DELETE')
                    <button class="text-slate-300 hover:text-red-500" title="Delete"><i class="fas fa-trash"></i></button>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center py-10 px-4">
            <p class="text-slate-600 font-medium mb-2">No tasks in “{{ $filterLabels[$filter] ?? $filter }}”.</p>
            @if(($filterCounts['all'] ?? 0) > 0 && $filter !== 'all')
                <p class="text-sm text-slate-500 mb-4">
                    You have <strong>{{ $filterCounts['all'] }}</strong> active personal task(s) overall
                    @if(($filterCounts['top_five'] ?? 0) > 0)
                        — <a href="{{ route('tasks.personal', ['filter' => 'top_five']) }}" class="text-blue-600 hover:underline font-medium">{{ $filterCounts['top_five'] }} in Top 5</a>
                    @endif
                    @if(($filterCounts['daily'] ?? 0) > 0 && $filter !== 'daily')
                        · <a href="{{ route('tasks.personal', ['filter' => 'daily']) }}" class="text-blue-600 hover:underline">{{ $filterCounts['daily'] }} daily</a>
                    @endif
                    @if(($filterCounts['weekly'] ?? 0) > 0 && $filter !== 'weekly')
                        · <a href="{{ route('tasks.personal', ['filter' => 'weekly']) }}" class="text-blue-600 hover:underline">{{ $filterCounts['weekly'] }} weekly</a>
                    @endif
                    .
                </p>
                <a href="{{ route('tasks.personal', ['filter' => 'all']) }}" class="inline-flex px-3 py-1.5 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-900">
                    View All Active
                </a>
            @else
                <p class="text-sm text-slate-500 mb-4">Create a personal task, then pick it on My Day as one of today’s 3 focuses.</p>
                @can('create tasks')
                <a href="{{ route('tasks.create', ['context' => $filter === 'all' ? 'daily' : $filter]) }}" class="inline-flex px-3 py-1.5 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-900">
                    + Add Task
                </a>
                @endcan
            @endif
        </div>
        @endforelse
    </div>
</div>

@if($adminTasks->isNotEmpty())
<script>
(function () {
    const root = document.getElementById('personal-tasks');
    const list = document.getElementById('task-list');
    if (!root || !list) return;

    const reorderUrl = root.dataset.reorderUrl;
    const csrf = root.dataset.csrf;
    let dragEl = null;

    function paintPriorityBadges() {
        Array.from(list.querySelectorAll('.task-row')).forEach(function (row, index) {
            const badge = row.querySelector('.priority-badge');
            if (!badge) return;
            const priority = index === 0 ? 'critical' : (index === 1 ? 'urgent' : 'normal');
            badge.textContent = priority;
            badge.className = 'priority-badge px-2 py-1 rounded text-xs font-bold uppercase ' +
                (priority === 'critical' ? 'bg-red-100 text-red-600' :
                 priority === 'urgent' ? 'bg-orange-100 text-orange-600' : 'bg-gray-100 text-slate-600');
        });
    }

    list.querySelectorAll('.task-row').forEach(function (row) {
        row.addEventListener('mousedown', function (e) {
            row.setAttribute('draggable', e.target.closest('.drag-handle') ? 'true' : 'false');
        });
        row.addEventListener('dragstart', function (e) {
            dragEl = row;
            row.classList.add('opacity-50', 'ring-2', 'ring-blue-300');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', row.dataset.id);
        });
        row.addEventListener('dragend', function () {
            row.classList.remove('opacity-50', 'ring-2', 'ring-blue-300');
            row.setAttribute('draggable', 'false');
            dragEl = null;
        });
        row.addEventListener('dragover', function (e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
        });
        row.addEventListener('drop', function (e) {
            e.preventDefault();
            if (!dragEl || dragEl === row) return;
            const children = Array.from(list.querySelectorAll('.task-row'));
            const from = children.indexOf(dragEl);
            const to = children.indexOf(row);
            if (from < to) list.insertBefore(dragEl, row.nextSibling);
            else list.insertBefore(dragEl, row);
            paintPriorityBadges();
            persistOrder();
        });
    });

    async function persistOrder() {
        const ids = Array.from(list.querySelectorAll('.task-row')).map(function (el) {
            return parseInt(el.dataset.id, 10);
        });
        try {
            await fetch(reorderUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ ids: ids }),
            });
        } catch (err) {
            console.error(err);
        }
    }
})();
</script>
@endif
@endsection
