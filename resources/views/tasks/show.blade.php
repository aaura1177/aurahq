@extends('layouts.admin')
@section('header', 'Task Details')
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Task Info -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
            <div class="flex justify-between items-start mb-4">
                <div>
                     <span class="text-xs font-bold uppercase tracking-wider px-2 py-1 rounded bg-slate-100 text-slate-600 mb-2 inline-block">
                        {{ $task->category }}
                    </span>
                    <h2 class="text-2xl font-bold text-slate-800">{{ $task->title }}</h2>
                </div>
                 <div class="text-right">
                    <span class="block text-xs font-bold uppercase {{ $task->priority == 'critical' ? 'text-red-600' : 'text-slate-500' }}">{{ $task->priority }}</span>
                    <span class="block text-sm text-slate-600">Due: {{ $task->due_date ? $task->due_date->format('M d, Y') : 'N/A' }}</span>
                </div>
            </div>
            
            <div class="prose text-sm text-slate-600 mb-6">
                {{ $task->description ?? 'No detailed description.' }}
            </div>

            @if($task->media_path)
            <div class="mb-6">
                <p class="text-xs font-bold text-slate-500 uppercase mb-2">Attached Media:</p>
                <a href="{{ asset('storage/'.$task->media_path) }}" target="_blank" class="block bg-slate-50 p-2 border rounded hover:bg-slate-100">
                    <i class="fas fa-paperclip mr-2 text-blue-500"></i> View Attachment
                </a>
            </div>
            @endif

            <div class="flex items-center gap-4 pt-4 border-t border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-400 uppercase">Assigned To:</span>
                    <span class="font-bold text-slate-700">{{ $task->assignee->name ?? 'Unassigned' }}</span>
                </div>
                 <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-400 uppercase">Status:</span>
                    <span class="font-bold text-blue-600 uppercase">{{ str_replace('_', ' ', $task->status) }}</span>
                </div>
            </div>
        </div>
        
        <!-- Performance Review Card -->
        @if($task->status == 'reviewed')
        <div class="bg-green-50 p-6 rounded-xl border border-green-200">
            <h3 class="font-bold text-lg text-green-800 mb-2">Performance Review</h3>
            <div class="flex items-center gap-2 mb-3">
                <div class="flex text-yellow-500 text-lg">
                    @for($i=0; $i<$task->rating; $i++) <i class="fas fa-star"></i> @endfor
                </div>
                <span class="text-sm text-green-700 font-bold">({{ $task->rating }} / 5)</span>
            </div>
            <p class="text-sm text-green-800 bg-white p-3 rounded-lg border border-green-100 mb-3">
                "{{ $task->admin_remark ?? 'No textual feedback provided.' }}"
            </p>
            @if($task->admin_media_path)
            <div>
                <p class="text-xs font-bold text-green-700 uppercase mb-1">Feedback Attachment:</p>
                <a href="{{ asset('storage/'.$task->admin_media_path) }}" target="_blank" class="inline-block bg-white px-3 py-1 rounded border border-green-200 text-green-600 text-xs hover:bg-green-100">
                    <i class="fas fa-file-download mr-1"></i> Download Review Media
                </a>
            </div>
            @endif
        </div>
        @endif

        <!-- Reporting History -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
            <h3 class="font-bold text-lg text-slate-800 mb-4">Activity & Reports</h3>
            <div class="space-y-6">
                @forelse($task->reports as $report)
                <div class="flex gap-4 group" x-data="{ editing: false }">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">
                        {{ substr($report->user->name, 0, 1) }}
                    </div>
                    <div class="flex-grow">
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-sm text-slate-700">{{ $report->user->name }}</span>
                                <span class="text-xs text-slate-400">{{ $report->created_at->format('M d, h:i A') }}</span>
                                @if($report->time_note)
                                <span class="text-[10px] bg-slate-100 px-2 rounded text-slate-500">Time: {{ $report->time_note }}</span>
                                @endif
                            </div>
                            
                            @if(auth()->id() == $report->user_id || auth()->user()->hasRole(['super-admin', 'admin']))
                            <div class="hidden group-hover:flex gap-2">
                                <button @click="editing = !editing" class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded text-xs font-bold hover:bg-yellow-200">Edit</button>
                                <form action="{{ route('tasks.reports.destroy', $report->id) }}" method="POST" onsubmit="return confirm('Delete report?');">
                                    @csrf @method('DELETE')
                                    <button class="bg-red-100 text-red-700 px-2 py-0.5 rounded text-xs font-bold hover:bg-red-200">Delete</button>
                                </form>
                            </div>
                            @endif
                        </div>
                        
                        <div x-show="!editing">
                            <p class="text-sm text-slate-600 bg-slate-50 p-3 rounded-lg">{{ $report->remark }}</p>
                            @if($report->media_path)
                            <a href="{{ asset('storage/'.$report->media_path) }}" target="_blank" class="inline-block mt-2 text-xs text-blue-500 hover:underline">
                                <i class="fas fa-image mr-1"></i> View Attachment
                            </a>
                            @endif
                        </div>

                        <!-- Edit Mode -->
                        <form x-show="editing" action="{{ route('tasks.reports.update', $report->id) }}" method="POST" enctype="multipart/form-data" class="mt-2">
                            @csrf @method('PUT')
                            <textarea name="remark" class="w-full border rounded p-2 text-xs" rows="2">{{ $report->remark }}</textarea>
                            <input type="text" name="time_note" value="{{ $report->time_note }}" class="w-full border rounded p-1 text-xs mt-1" placeholder="Time note">
                            <div class="mt-2">
                                <label class="text-[10px] font-bold text-slate-500">Replace Media:</label>
                                <input type="file" name="media" class="w-full text-xs">
                            </div>
                            <div class="flex gap-2 mt-2">
                                <button class="text-xs bg-blue-600 text-white px-2 py-1 rounded">Save</button>
                                <button type="button" @click="editing = false" class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
                @empty
                <p class="text-slate-400 text-sm text-center">No reports submitted yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Actions Sidebar -->
    <div class="space-y-6">
        
        <!-- Admin Private Todos (New) -->
        @role(['super-admin', 'admin'])
        <div class="bg-purple-50 p-6 rounded-xl border border-purple-100">
            <h3 class="font-bold text-purple-800 mb-4 text-sm uppercase">Admin Private Todos</h3>
            
            <form action="{{ route('tasks.todos.store', $task->id) }}" method="POST" class="mb-4 flex gap-2">
                @csrf
                <input type="text" name="title" placeholder="Add private note/todo..." class="w-full text-xs border rounded p-2 focus:ring-purple-200" required>
                <button class="bg-purple-600 text-white px-3 py-1 rounded text-xs hover:bg-purple-700">Add</button>
            </form>

            <div class="space-y-2">
                @foreach($task->todos as $todo)
                <div class="flex items-center justify-between bg-white p-2 rounded border border-purple-100 group" x-data="{ editing: false }">
                    <!-- View Mode -->
                    <div class="flex items-center gap-2 flex-grow" x-show="!editing">
                        <form action="{{ route('tasks.todos.status', $todo->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="{{ $todo->status == 'done' ? 'pending' : 'done' }}">
                            <button class="text-purple-600 hover:text-purple-800">
                                <i class="fas {{ $todo->status == 'done' ? 'fa-check-square' : 'fa-square' }}"></i>
                            </button>
                        </form>
                        <span class="text-xs text-slate-700 {{ $todo->status == 'done' ? 'line-through text-slate-400' : '' }}">{{ $todo->title }}</span>
                    </div>
                    
                    <!-- Edit Mode -->
                    <form x-show="editing" action="{{ route('tasks.todos.update', $todo->id) }}" method="POST" class="flex-grow flex gap-1">
                        @csrf @method('PUT')
                        <input type="text" name="title" value="{{ $todo->title }}" class="w-full text-xs border rounded px-1">
                        <button class="text-green-600 text-xs"><i class="fas fa-save"></i></button>
                    </form>

                    <!-- Actions -->
                    <div class="flex gap-2 ml-2 opacity-0 group-hover:opacity-100 transition-opacity">
                         <button @click="editing = !editing" class="text-xs text-blue-400 hover:text-blue-600" x-show="!editing"><i class="fas fa-edit"></i></button>
                         <form action="{{ route('tasks.todos.destroy', $todo->id) }}" method="POST" onsubmit="return confirm('Delete todo?');">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-400 hover:text-red-600"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endrole

        <!-- Submit Report Form -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
            <h3 class="font-bold text-slate-700 mb-4">Submit Report</h3>
            <form action="{{ route('tasks.report.store', $task->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Status Update</label>
                    <select name="status" class="w-full border rounded p-2 text-sm">
                        <option value="">Keep current status</option>
                        <option value="in_progress">In Progress</option>
                        <option value="almost_complete">Almost Complete</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled (Record)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Time / Note</label>
                    <input type="text" name="time_note" class="w-full border rounded p-2 text-sm" placeholder="e.g. 2 hours">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Remark</label>
                    <textarea name="remark" class="w-full border rounded p-2 text-sm h-24" required></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Attach Media</label>
                    <input type="file" name="media" class="w-full text-xs">
                </div>
                <button class="w-full bg-blue-600 text-white py-2 rounded-lg font-bold text-sm hover:bg-blue-700">Submit Report</button>
            </form>
        </div>

        <!-- Admin Review (Only Admin) -->
        @role(['super-admin', 'admin'])
        <div class="bg-slate-50 p-6 rounded-xl border border-slate-200">
            <h3 class="font-bold text-slate-700 mb-4">Admin Review</h3>
            <form action="{{ route('tasks.review', $task->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf @method('PATCH')
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Rating</label>
                    <div class="flex gap-2">
                        @for($i=1; $i<=5; $i++)
                        <label class="cursor-pointer">
                            <input type="radio" name="rating" value="{{ $i }}" class="sr-only peer" {{ $task->rating == $i ? 'checked' : '' }}>
                            <span class="text-2xl text-slate-300 peer-checked:text-yellow-400 hover:text-yellow-300 transition">★</span>
                        </label>
                        @endfor
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Final Feedback</label>
                    <textarea name="admin_remark" class="w-full border rounded p-2 text-sm h-20 bg-white" placeholder="Feedback visible to employee">{{ $task->admin_remark }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Feedback Attachment</label>
                    <input type="file" name="admin_media" class="w-full text-xs">
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-red-500 uppercase mb-1">Private Note</label>
                    <textarea name="admin_private_note" class="w-full border rounded p-2 text-sm h-20 bg-white border-red-200 focus:ring-red-200" placeholder="Internal note...">{{ $task->admin_private_note }}</textarea>
                </div>

                <button class="w-full bg-slate-800 text-white py-2 rounded-lg font-bold text-sm hover:bg-slate-900">Save Review</button>
            </form>
            
            <div class="mt-4 pt-4 border-t border-slate-200">
                <a href="{{ route('tasks.edit', $task->id) }}" class="block text-center text-blue-600 text-sm font-bold hover:underline mb-2">Edit Task Details</a>
                <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Delete task?');">
                    @csrf @method('DELETE')
                    <button class="w-full text-red-500 text-sm hover:text-red-700">Delete Task</button>
                </form>
            </div>
        </div>
        @endrole
    </div>
</div>
@endsection