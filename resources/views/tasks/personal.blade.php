@extends('layouts.admin')
@section('title', 'My Tasks')
@section('header', 'My Personal Tasks')

@section('content')
<div class="flex justify-between mb-6 flex-wrap gap-4">
    <!-- Filter Buttons (Matches Original Prototype) -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-2 w-full md:w-auto">
        <a href="{{ route('tasks.personal', ['filter' => 'daily']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium text-center shadow-sm {{ $filter == 'daily' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
           Daily
        </a>
        <a href="{{ route('tasks.personal', ['filter' => 'weekly']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium text-center shadow-sm {{ $filter == 'weekly' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
           Weekly
        </a>
        <a href="{{ route('tasks.personal', ['filter' => 'top_five']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium text-center shadow-sm {{ $filter == 'top_five' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
           Top 5
        </a>
        <a href="{{ route('tasks.personal', ['filter' => 'urgent']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium text-center shadow-sm {{ $filter == 'urgent' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
           Urgent
        </a>
        <a href="{{ route('tasks.personal', ['filter' => 'all']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium text-center shadow-sm {{ $filter == 'all' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
           All Active
        </a>
    </div>

    <div class="flex items-center gap-2">
        <!-- Status Filter -->
        <form action="{{ route('tasks.personal') }}" method="GET" class="flex gap-2">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <select name="status" class="border rounded-lg px-3 py-2 text-sm bg-white" onchange="this.form.submit()">
                <option value="">Status: Any</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active (Enabled)</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive (Disabled)</option>
            </select>
        </form>

        @can('create tasks')
        <!-- Pass current filter as context for smart pre-filling -->
        <a href="{{ route('tasks.create', ['context' => $filter]) }}" class="bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-medium shadow hover:bg-slate-900 whitespace-nowrap">+ Add Task</a>
        @endcan
    </div>
</div>

@if(session('error'))
<div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg border border-red-200">
    {{ session('error') }}
</div>
@endif

<div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
    <h3 class="font-bold text-lg text-slate-800 mb-4">{{ ucfirst(str_replace('_', ' ', $filter)) }} Tasks ({{ count($adminTasks) }})</h3>
    <div class="space-y-3">
        @forelse($adminTasks as $task)
        <div class="flex items-center justify-between p-4 border border-slate-100 rounded-lg hover:border-blue-300 transition group cursor-pointer bg-white {{ !$task->is_active ? 'bg-gray-50 opacity-75' : '' }}">
            <div class="flex items-center gap-4">
                <!-- Disable/Enable Toggle -->
                <div class="w-6 h-6 rounded-full border-2 border-slate-300 group-hover:border-blue-500 flex items-center justify-center">
                    <form action="{{ route('tasks.toggle', $task->id) }}" method="POST" class="flex items-center justify-center w-full h-full">
                        @csrf @method('PATCH')
                        <button class="w-3 h-3 bg-transparent {{ $task->is_active ? 'group-hover:bg-blue-500' : 'bg-red-400' }} rounded-full transition-colors" title="Toggle Active/Inactive"></button>
                    </form>
                </div>
                <div>
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
            <div class="flex gap-2 items-center">
                @if($task->priority == 'urgent' || $task->priority == 'critical')
                    <span class="px-2 py-1 bg-red-100 text-red-600 rounded text-xs font-bold uppercase">{{ $task->priority }}</span>
                @endif
                <span class="px-2 py-1 bg-gray-100 text-slate-600 rounded text-xs uppercase font-bold">{{ $task->status }}</span>
                
                <a href="{{ route('tasks.edit', $task->id) }}" class="text-slate-300 hover:text-blue-500"><i class="fas fa-edit"></i></a>
                <a href="{{ route('tasks.show', $task->id) }}" class="text-slate-300 hover:text-blue-500"><i class="fas fa-eye"></i></a>
                
                <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Delete?');">
                    @csrf @method('DELETE')
                    <button class="text-slate-300 hover:text-red-500"><i class="fas fa-trash"></i></button>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center py-10 text-slate-400">
            <p>No tasks found for "{{ $filter }}" filter.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection