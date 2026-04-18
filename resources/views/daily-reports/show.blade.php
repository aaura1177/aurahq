@extends('layouts.admin')
@section('header', 'Daily Report – ' . $dailyReport->user->name . ' · ' . $dailyReport->date->format('d M Y'))
@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex justify-between items-center flex-wrap gap-2">
            <div>
                <h3 class="font-bold text-slate-800">{{ $dailyReport->user->name }}</h3>
                <p class="text-sm text-slate-500">{{ $dailyReport->date->format('l, d F Y') }}</p>
            </div>
            <div class="flex gap-2">
                @if(auth()->user()->hasRole('super-admin') || $canEditReport)
                <a href="{{ route('daily-reports.edit', $dailyReport) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">Edit</a>
                @endif
                @role('super-admin')
                <form action="{{ route('daily-reports.destroy', $dailyReport) }}" method="POST" onsubmit="return confirm('Delete this report?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-100 text-red-700 px-4 py-2 rounded-lg text-sm hover:bg-red-200">Delete</button>
                </form>
                @endrole
            </div>
        </div>

        <div class="p-6 space-y-6">
            {{-- Morning --}}
            <div>
                <h4 class="font-bold text-slate-700 mb-2 flex items-center gap-2">
                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Morning</span>
                    @if($dailyReport->morning_submitted_at)
                    <span class="text-xs font-normal text-slate-500">Submitted {{ $dailyReport->morning_submitted_at->setTimezone('Asia/Kolkata')->format('h:i A') }} IST</span>
                    @else
                    <span class="text-xs font-normal text-slate-400">Not submitted</span>
                    @endif
                </h4>
                @if($dailyReport->morning_task_ids && count($dailyReport->morning_task_ids) > 0)
                <p class="text-xs font-bold text-slate-500 uppercase mb-1">Tasks & notes</p>
                <ul class="text-slate-700 mb-2 space-y-1.5">
                    @foreach($dailyReport->morningTasks() as $t)
                    <li class="flex gap-2"><span class="font-medium shrink-0">{{ $t->title }}:</span> <span>{{ $dailyReport->getMorningTaskNote($t->id) ?: '—' }}</span></li>
                    @endforeach
                </ul>
                @endif
                <p class="text-xs font-bold text-slate-500 uppercase mt-2 mb-0.5">Additional note</p>
                <p class="text-slate-700 whitespace-pre-wrap">{{ $dailyReport->morning_note ?: '—' }}</p>
            </div>

            {{-- Evening --}}
            <div class="pt-4 border-t border-slate-100">
                <h4 class="font-bold text-slate-700 mb-2 flex items-center gap-2">
                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Evening</span>
                    @if($dailyReport->evening_submitted_at)
                    <span class="text-xs font-normal text-slate-500">Submitted {{ $dailyReport->evening_submitted_at->setTimezone('Asia/Kolkata')->format('h:i A') }} IST</span>
                    @else
                    <span class="text-xs font-normal text-slate-400">Not submitted</span>
                    @endif
                </h4>
                @if($dailyReport->evening_task_ids && count($dailyReport->evening_task_ids) > 0)
                <p class="text-xs font-bold text-slate-500 uppercase mb-1">Tasks & notes</p>
                <ul class="text-slate-700 mb-2 space-y-1.5">
                    @foreach($dailyReport->eveningTasks() as $t)
                    <li class="flex gap-2"><span class="font-medium shrink-0">{{ $t->title }}:</span> <span>{{ $dailyReport->getEveningTaskNote($t->id) ?: '—' }}</span></li>
                    @endforeach
                </ul>
                @endif
                <p class="text-xs font-bold text-slate-500 uppercase mt-2 mb-0.5">Additional note</p>
                <p class="text-slate-700 whitespace-pre-wrap">{{ $dailyReport->evening_note ?: '—' }}</p>
            </div>
        </div>
    </div>
    <a href="{{ route('daily-reports.index') }}" class="text-slate-600 hover:text-slate-800 text-sm">← Back to list</a>
</div>
@endsection
