@extends('layouts.admin')
@section('header', 'Edit Report – ' . $dailyReport->user->name . ' · ' . $dailyReport->date->format('d M Y'))
@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-slate-100 p-6">
    <form action="{{ route('daily-reports.update', $dailyReport) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Morning --}}
        <div class="mb-8">
            <h4 class="font-bold text-slate-700 mb-3">Morning report</h4>
            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-600 mb-1">Tasks (add note per task)</label>
                <div class="border border-slate-200 rounded-lg p-3 max-h-64 overflow-y-auto space-y-4">
                    @foreach($assignedTasks as $task)
                    <div class="p-3 rounded-lg border border-slate-100 bg-slate-50/50 space-y-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="morning_task_ids[]" value="{{ $task->id }}" class="rounded text-blue-600"
                                {{ in_array($task->id, $dailyReport->morning_task_ids ?? []) ? 'checked' : '' }}>
                            <span class="text-sm font-medium text-slate-700">{{ $task->title }}</span>
                        </label>
                        <input type="text" name="morning_task_notes[{{ $task->id }}]" value="{{ old('morning_task_notes.'.$task->id, $dailyReport->getMorningTaskNote($task->id)) }}" placeholder="Note for this task" class="w-full text-sm border border-slate-200 rounded px-3 py-2 ml-6">
                    </div>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Additional note (for others)</label>
                <textarea name="morning_note" rows="3" class="w-full border border-slate-200 rounded-lg px-3 py-2" placeholder="Other updates, blockers...">{{ old('morning_note', $dailyReport->morning_note) }}</textarea>
            </div>
        </div>

        {{-- Evening --}}
        <div class="mb-6">
            <h4 class="font-bold text-slate-700 mb-3">Evening report</h4>
            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-600 mb-1">Tasks (add note per task)</label>
                <div class="border border-slate-200 rounded-lg p-3 max-h-64 overflow-y-auto space-y-4">
                    @foreach($assignedTasks as $task)
                    <div class="p-3 rounded-lg border border-slate-100 bg-slate-50/50 space-y-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="evening_task_ids[]" value="{{ $task->id }}" class="rounded text-blue-600"
                                {{ in_array($task->id, $dailyReport->evening_task_ids ?? []) ? 'checked' : '' }}>
                            <span class="text-sm font-medium text-slate-700">{{ $task->title }}</span>
                        </label>
                        <input type="text" name="evening_task_notes[{{ $task->id }}]" value="{{ old('evening_task_notes.'.$task->id, $dailyReport->getEveningTaskNote($task->id)) }}" placeholder="Note for this task" class="w-full text-sm border border-slate-200 rounded px-3 py-2 ml-6">
                    </div>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Additional note (for others)</label>
                <textarea name="evening_note" rows="3" class="w-full border border-slate-200 rounded-lg px-3 py-2" placeholder="Other updates, blockers...">{{ old('evening_note', $dailyReport->evening_note) }}</textarea>
            </div>
        </div>

        <input type="hidden" name="slot" value="both">

        <div class="flex gap-4">
            <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg font-bold hover:bg-blue-700">Update Report</button>
            <a href="{{ route('daily-reports.show', $dailyReport) }}" class="flex-1 bg-slate-100 text-slate-700 py-2 rounded-lg font-bold hover:bg-slate-200 text-center">Cancel</a>
        </div>
    </form>
</div>
@endsection
