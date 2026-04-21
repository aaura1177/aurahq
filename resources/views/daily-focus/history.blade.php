@extends('layouts.admin')
@section('title', 'My Day — History')
@section('header', 'My Day — Last 30 days')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center flex-wrap gap-3">
        <p class="text-sm text-slate-600">Review your 3-task completion pattern.</p>
        <a href="{{ route('daily-focus.today') }}" class="text-sm font-semibold text-blue-600 hover:underline">← Back to today</a>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                <tr>
                    <th class="px-4 py-3 font-semibold">Date</th>
                    <th class="px-4 py-3 font-semibold">Done</th>
                    <th class="px-4 py-3 font-semibold">Energy</th>
                    <th class="px-4 py-3 font-semibold">Summary</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($focuses as $f)
                    @php
                        $c = $f->completed_count;
                        $rowClass = $c === 3 ? 'bg-green-50/80' : ($c >= 1 ? 'bg-amber-50/50' : 'bg-red-50/40');
                    @endphp
                    <tr class="{{ $rowClass }} hover:opacity-95">
                        <td class="px-4 py-3 font-medium text-slate-900 whitespace-nowrap">{{ $f->date->format('M j, Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="font-bold {{ $c === 3 ? 'text-green-700' : ($c >= 1 ? 'text-amber-700' : 'text-red-700') }}">{{ $c }}/3</span>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $f->energy_level ? ucfirst($f->energy_level) : '—' }}</td>
                        <td class="px-4 py-3 text-slate-600 max-w-md truncate" title="{{ $f->wins }}">
                            {{ Str::limit(collect([$f->task_1_title, $f->task_2_title, $f->task_3_title])->filter()->implode(' · ') ?: ($f->wins ?? ''), 64) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-slate-500">No entries in the last 30 days yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
