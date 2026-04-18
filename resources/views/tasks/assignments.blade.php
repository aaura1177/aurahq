@extends('layouts.admin')
@section('title', 'Assignments')
@section('header', 'Task Assignments')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
    <div class="flex items-center gap-2 w-full md:w-auto">
        <!-- Filters -->
        <form action="{{ route('tasks.assignments') }}" method="GET" class="flex gap-2 flex-wrap">
            @if(request('employee_id')) <input type="hidden" name="employee_id" value="{{ request('employee_id') }}"> @endif
            
            <select name="state" class="border rounded-lg px-3 py-2 text-sm bg-white" onchange="this.form.submit()">
                <option value="active" {{ request('state') != 'disabled' ? 'selected' : '' }}>State: Active</option>
                <option value="disabled" {{ request('state') == 'disabled' ? 'selected' : '' }}>State: Disabled</option>
            </select>

            <select name="priority" class="border rounded-lg px-3 py-2 text-sm bg-white" onchange="this.form.submit()">
                <option value="">Priority: All</option>
                <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                <option value="critical" {{ request('priority') == 'critical' ? 'selected' : '' }}>Critical</option>
            </select>
            <select name="status" class="border rounded-lg px-3 py-2 text-sm bg-white" onchange="this.form.submit()">
                <option value="">Status: All</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="almost_complete" {{ request('status') == 'almost_complete' ? 'selected' : '' }}>Almost Complete</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
            </select>
        </form>
    </div>
    @can('create tasks')
    <a href="{{ route('tasks.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow hover:bg-blue-700">+ Assign Task</a>
    @endcan
</div>

<!-- Employee Avatars Row -->
@role(['super-admin', 'admin'])
<div class="flex gap-4 overflow-x-auto pb-4 mb-2">
    <a href="{{ route('tasks.assignments') }}" class="flex flex-col items-center gap-2 cursor-pointer {{ !request('employee_id') ? 'opacity-100' : 'opacity-50 hover:opacity-100' }}">
        <div class="w-12 h-12 rounded-full bg-slate-200 border-2 border-slate-300 flex items-center justify-center text-slate-600 font-bold">All</div>
        <span class="text-xs font-bold text-slate-600">All</span>
    </a>
    @foreach($employees as $emp)
    <a href="{{ route('tasks.assignments', array_merge(request()->query(), ['employee_id' => $emp->id])) }}" class="flex flex-col items-center gap-2 cursor-pointer {{ request('employee_id') == $emp->id ? 'opacity-100' : 'opacity-50 hover:opacity-100' }}">
        <div class="w-12 h-12 rounded-full bg-blue-100 border-2 border-blue-200 flex items-center justify-center text-blue-700 font-bold text-lg">
            {{ substr($emp->name, 0, 1) }}
        </div>
        <span class="text-xs font-bold text-slate-600">{{ $emp->name }}</span>
    </a>
    @endforeach
</div>
@endrole

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($assignedTasks as $task)
    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 relative hover:shadow-md transition {{ !$task->is_active ? 'opacity-60 bg-gray-50' : '' }}">
        <div class="flex justify-between items-start mb-2">
            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded 
                {{ $task->status == 'completed' ? 'bg-green-100 text-green-700' : 
                  ($task->status == 'cancelled' ? 'bg-red-100 text-red-700' : 
                  ($task->status == 'almost_complete' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700')) }}">
                {{ str_replace('_', ' ', $task->status) }}
            </span>
            <span class="text-[10px] font-bold uppercase px-2 py-1 rounded
                {{ $task->priority == 'critical' ? 'bg-red-100 text-red-700' : ($task->priority == 'urgent' ? 'bg-orange-100 text-orange-700' : 'bg-slate-100 text-slate-600') }}">
                {{ $task->priority }}
            </span>
        </div>
        
        @if($task->rating)
        <div class="flex text-yellow-400 text-xs mb-2">
            @for($i=0; $i<$task->rating; $i++) <i class="fas fa-star"></i> @endfor
        </div>
        @endif

        <h3 class="font-bold text-slate-800 mb-1 line-clamp-1">
            <a href="{{ route('tasks.show', $task->id) }}" class="hover:text-blue-600 hover:underline">
                {{ $task->title }}
            </a>
            @if(!$task->is_active)<span class="text-xs text-red-500 font-normal ml-2">(Disabled)</span>@endif
        </h3>
        <p class="text-sm text-slate-500 mb-3 line-clamp-2">{{ $task->description ?? 'No details provided.' }}</p>

        <div class="flex items-center gap-2 mb-4 pt-3 border-t border-slate-50">
            <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-xs font-bold text-slate-600">
                {{ substr($task->assignee->name ?? 'U', 0, 1) }}
            </div>
            <div class="text-xs">
                <p class="font-semibold text-slate-700">{{ $task->assignee->name ?? 'Unassigned' }}</p>
                <p class="text-slate-400">Due: {{ $task->due_date ? $task->due_date->format('M d') : 'None' }}</p>
            </div>
        </div>

        <div class="flex justify-between items-center mt-2">
            <a href="{{ route('tasks.show', $task->id) }}" class="text-sm font-bold text-blue-600 hover:text-blue-800">View Details &rarr;</a>
            
            @role(['super-admin', 'admin'])
            <div class="flex gap-2">
                 <form action="{{ route('tasks.toggle', $task->id) }}" method="POST">
                    @csrf @method('PATCH')
                    <button class="text-gray-400 hover:text-orange-500" title="{{ $task->is_active ? 'Disable' : 'Enable' }}">
                        <i class="fas {{ $task->is_active ? 'fa-ban' : 'fa-check-circle' }}"></i>
                    </button>
                </form>
                <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Delete task?');">
                    @csrf @method('DELETE')
                    <button class="text-gray-400 hover:text-red-600" title="Delete"><i class="fas fa-trash"></i></button>
                </form>
            </div>
            @endrole
        </div>
    </div>
    @empty
    <div class="col-span-full bg-white p-10 rounded-xl border border-slate-100 text-center text-slate-400">
        <i class="fas fa-tasks text-4xl mb-3 opacity-20"></i>
        <p>No active assignments found.</p>
    </div>
    @endforelse
</div>
@endsection
