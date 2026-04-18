@extends('layouts.admin')
@section('header', $isSuperAdmin ? 'Add Report (for Employee)' : ($chooseSlot ? 'Report' : 'Report – ' . ($slot === 'morning' ? 'Morning' : 'Evening'))))
@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-slate-100 p-6">
    @if($chooseSlot)
    {{-- Choose morning or evening; show "Add evening" if morning already submitted for this day --}}
    <p class="text-sm text-slate-600 mb-4">Select which report to submit for {{ $date->format('d M Y') }}.</p>
    <div class="flex flex-col sm:flex-row gap-4">
        <a href="{{ route('daily-reports.create', ['slot' => 'morning', 'date' => $date->format('Y-m-d')] + ($isSuperAdmin && $targetUser ? ['user_id' => $targetUser->id] : [])) }}" class="flex-1 p-4 rounded-xl border-2 {{ $hasMorningForDate ? 'border-slate-200 bg-slate-50' : 'border-blue-200 bg-blue-50 hover:bg-blue-100' }} text-center">
            <span class="font-bold text-slate-800">Morning report</span>
            @if($hasMorningForDate)<span class="block text-xs text-slate-500 mt-1">Already submitted</span>@endif
        </a>
        <a href="{{ route('daily-reports.create', ['slot' => 'evening', 'date' => $date->format('Y-m-d')] + ($isSuperAdmin && $targetUser ? ['user_id' => $targetUser->id] : [])) }}" class="flex-1 p-4 rounded-xl border-2 {{ $hasEveningForDate ? 'border-slate-200 bg-slate-50' : 'border-blue-200 bg-blue-100 hover:bg-blue-200' }} text-center">
            <span class="font-bold text-slate-800">Evening report</span>
            @if($hasMorningForDate && !$hasEveningForDate)<span class="block text-xs text-green-600 font-medium mt-1">Add evening for this day</span>@endif
            @if($hasEveningForDate)<span class="block text-xs text-slate-500 mt-1">Already submitted</span>@endif
        </a>
    </div>
    @if($isSuperAdmin && $targetUser)
    <div class="mt-6 pt-4 border-t border-slate-200">
        <p class="text-xs font-bold text-slate-500 uppercase mb-2">Allow employee to submit at any time (one at a time)</p>
        <div class="flex flex-wrap gap-2">
            <form action="{{ route('daily-reports.allow-submission') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="user_id" value="{{ $targetUser->id }}">
                <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
                <input type="hidden" name="slot" value="morning">
                <button type="submit" class="text-sm px-3 py-1.5 rounded-lg bg-amber-100 text-amber-800 hover:bg-amber-200">Allow morning for this date</button>
            </form>
            <form action="{{ route('daily-reports.allow-submission') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="user_id" value="{{ $targetUser->id }}">
                <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
                <input type="hidden" name="slot" value="evening">
                <button type="submit" class="text-sm px-3 py-1.5 rounded-lg bg-amber-100 text-amber-800 hover:bg-amber-200">Allow evening for this date</button>
            </form>
        </div>
    </div>
    @endif
    <p class="mt-4"><a href="{{ route('daily-reports.index') }}" class="text-slate-600 hover:underline">← Back to list</a></p>
    @else
    <p class="text-sm text-slate-600 mb-4">Reporting window (IST): Morning till 11:00 AM · Evening till 5:15 PM (on present days only).</p>

    @if(!$canSubmitMorning && !$canSubmitEvening && !$isSuperAdmin)
    <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg text-amber-800 mb-4">
        You can only submit a <strong>{{ $slot }}</strong> report during the allowed time window in Indian time. Current time (IST): {{ now()->setTimezone('Asia/Kolkata')->format('h:i A') }}.
    </div>
    @endif

    @if($canSubmitMorning || $canSubmitEvening || $isSuperAdmin)
    <form action="{{ route('daily-reports.store') }}" method="POST">
        @csrf
        @if($isSuperAdmin)
        <div class="mb-6">
            <label for="user_id" class="block text-sm font-bold text-slate-700 mb-2">Employee</label>
            <select name="user_id" id="user_id" class="w-full border border-slate-200 rounded-lg px-4 py-2" required
                onchange="if(this.value) window.location='{{ route('daily-reports.create') }}?slot={{ $slot }}&date={{ $date->format('Y-m-d') }}&user_id='+this.value">
                @foreach($employees as $emp)
                <option value="{{ $emp->id }}" {{ old('user_id', $targetUser->id) == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                @endforeach
            </select>
            @error('user_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="mb-6">
            <label for="date" class="block text-sm font-bold text-slate-700 mb-2">Date</label>
            <input type="date" name="date" id="date" value="{{ old('date', $date->format('Y-m-d')) }}" class="w-full border border-slate-200 rounded-lg px-4 py-2" required>
            @error('date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        @else
        <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
        @endif
        @if($isSuperAdmin)
        <div class="mb-6">
            <label class="block text-sm font-bold text-slate-700 mb-2">Slot</label>
            <select name="slot" class="w-full border border-slate-200 rounded-lg px-4 py-2">
                <option value="morning" {{ $slot === 'morning' ? 'selected' : '' }}>Morning</option>
                <option value="evening" {{ $slot === 'evening' ? 'selected' : '' }}>Evening</option>
            </select>
        </div>
        @else
        <input type="hidden" name="slot" value="{{ $slot }}">
        @endif

        <div class="mb-6">
            <label class="block text-sm font-bold text-slate-700 mb-2">Tasks you are reporting on (optional – add a note per task)</label>
            <div class="border border-slate-200 rounded-lg p-4 max-h-64 overflow-y-auto space-y-4">
                @forelse($assignedTasks as $task)
                <div class="p-3 rounded-lg border border-slate-100 bg-slate-50/50 space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="task_ids[]" value="{{ $task->id }}" class="rounded text-blue-600 task-cb">
                        <span class="text-sm font-medium text-slate-700">{{ $task->title }}</span>
                    </label>
                    <input type="text" name="task_notes[{{ $task->id }}]" value="{{ old('task_notes.'.$task->id) }}" placeholder="Note for this task (optional)" class="task-note w-full text-sm border border-slate-200 rounded px-3 py-2 ml-6">
                </div>
                @empty
                <p class="text-sm text-slate-500">No assigned tasks. You can add an additional note below.</p>
                @endforelse
            </div>
        </div>

        <div class="mb-6">
            <label for="note" class="block text-sm font-bold text-slate-700 mb-2">Additional note (for others / general)</label>
            <textarea name="note" id="note" rows="4" class="w-full border border-slate-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Any other updates, blockers, or notes...">{{ old('note') }}</textarea>
            @error('note')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-4">
            <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg font-bold hover:bg-blue-700">Submit Report</button>
            <a href="{{ route('daily-reports.create') }}{{ $date ? '?date='.$date->format('Y-m-d') : '' }}{{ $isSuperAdmin && $targetUser ? ($date ? '&' : '?').'user_id='.$targetUser->id : '' }}" class="flex-1 bg-slate-100 text-slate-700 py-2 rounded-lg font-bold hover:bg-slate-200 text-center">Cancel (choose slot)</a>
        </div>
    </form>
    @if($isSuperAdmin && $targetUser && ($slot === 'morning' || $slot === 'evening'))
    <div class="mt-6 pt-4 border-t border-slate-200">
        <p class="text-xs font-bold text-slate-500 uppercase mb-2">Allow this employee to submit at any time for this date (one slot at a time)</p>
        <div class="flex flex-wrap gap-2">
            <form action="{{ route('daily-reports.allow-submission') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="user_id" value="{{ $targetUser->id }}">
                <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
                <input type="hidden" name="slot" value="morning">
                <button type="submit" class="text-sm px-3 py-1.5 rounded-lg {{ $overrideMorning ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800 hover:bg-amber-200' }}">{{ $overrideMorning ? 'Morning allowed ✓' : 'Allow morning for this date' }}</button>
            </form>
            <form action="{{ route('daily-reports.allow-submission') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="user_id" value="{{ $targetUser->id }}">
                <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
                <input type="hidden" name="slot" value="evening">
                <button type="submit" class="text-sm px-3 py-1.5 rounded-lg {{ $overrideEvening ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800 hover:bg-amber-200' }}">{{ $overrideEvening ? 'Evening allowed ✓' : 'Allow evening for this date' }}</button>
            </form>
        </div>
    </div>
    @endif
    @else
    <a href="{{ route('daily-reports.index') }}" class="text-blue-600 hover:underline">← Back to Daily Reports</a>
    @endif
    @endif
</div>
@endsection
