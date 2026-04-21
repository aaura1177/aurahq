@extends('layouts.admin')
@section('header', 'Edit Task')
@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded-xl shadow-sm border border-slate-100">
    <form action="{{ route('tasks.update', $task->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf @method('PUT')
        
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Task Title</label>
            <input type="text" name="title" value="{{ $task->title }}" class="w-full border rounded-lg p-2.5" required>
        </div>

        @if($task->category == 'admin_personal')
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Task Type</label>
            <select name="frequency" class="w-full border rounded-lg p-2.5 bg-white">
                <option value="daily" {{ $task->frequency == 'daily' ? 'selected' : '' }}>Daily</option>
                <option value="weekly" {{ $task->frequency == 'weekly' ? 'selected' : '' }}>Weekly</option>
                <option value="top_five" {{ $task->frequency == 'top_five' ? 'selected' : '' }}>Top 5</option>
            </select>
        </div>
        @endif

        @if($task->category == 'employee_assignment')
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Assigned To</label>
            <select name="assigned_to" class="w-full border rounded-lg p-2.5 bg-white">
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ $task->assigned_to == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Description</label>
            <textarea name="description" class="w-full border rounded-lg p-2.5" rows="3">{{ $task->description }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Project <span class="text-slate-400 font-normal">(optional)</span></label>
            <select name="project_id" class="w-full border rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">— No project —</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ (string) old('project_id', $task->project_id) === (string) $project->id ? 'selected' : '' }}>
                        {{ $project->name }} ({{ $project->client->name }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Priority</label>
                <select name="priority" class="w-full border rounded-lg p-2.5 bg-white">
                    <option value="normal" {{ $task->priority == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="urgent" {{ $task->priority == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="critical" {{ $task->priority == 'critical' ? 'selected' : '' }}>Critical</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Due Date</label>
                <input type="date" name="due_date" value="{{ $task->due_date ? $task->due_date->format('Y-m-d') : '' }}" class="w-full border rounded-lg p-2.5">
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Attachment</label>
            <input type="file" name="media" class="w-full border rounded-lg p-2.5 text-sm">
            @if($task->media_path)
                <p class="text-xs text-green-600 mt-1">Current file: {{ basename($task->media_path) }}</p>
            @endif
        </div>

        <div class="flex gap-4 pt-4">
            <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition">Update Task</button>
            <a href="{{ $task->category == 'admin_personal' ? route('tasks.personal') : route('tasks.assignments') }}" class="flex-1 bg-slate-100 text-slate-700 py-3 rounded-lg font-bold text-center hover:bg-slate-200">Cancel</a>
        </div>
    </form>
</div>
@endsection
