@extends('layouts.admin')
@section('header', 'Daily Reports')
@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
        <form method="GET" action="{{ route('daily-reports.index') }}" class="flex flex-wrap items-end gap-4">
            @role('super-admin')
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Employee</label>
                <select name="employee_id" class="border border-slate-200 rounded-lg px-3 py-2 min-w-[200px]">
                    <option value="">All</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ $employeeId == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            @endrole
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">From</label>
                <input type="date" name="start_date" value="{{ $start->format('Y-m-d') }}" class="border border-slate-200 rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">To</label>
                <input type="date" name="end_date" value="{{ $end->format('Y-m-d') }}" class="border border-slate-200 rounded-lg px-3 py-2">
            </div>
            <button type="submit" class="bg-slate-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-slate-800">Apply</button>
        </form>
    </div>

    <div class="flex justify-between items-center flex-wrap gap-2">
        <p class="text-sm text-slate-600">Reporting windows (IST): Morning till 11:00 AM · Evening till 5:15 PM (on present days)</p>
        @can('create daily reports')
        <a href="{{ route('daily-reports.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">+ Report</a>
        @endcan
    </div>

    {{-- Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($reports as $report)
        <a href="{{ route('daily-reports.show', $report) }}" class="block bg-white rounded-xl shadow-sm border border-slate-100 p-4 hover:shadow-md hover:border-slate-200 transition">
            <div class="flex justify-between items-start mb-2">
                <h4 class="font-bold text-slate-800">{{ $report->user->name }}</h4>
                <span class="text-xs text-slate-500">{{ $report->date->format('d M Y') }}</span>
            </div>
            <div class="flex gap-2 flex-wrap">
                @if($report->morning_submitted_at)
                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Morning ✓</span>
                @else
                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-500">Morning —</span>
                @endif
                @if($report->evening_submitted_at)
                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Evening ✓</span>
                @else
                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-500">Evening —</span>
                @endif
            </div>
            @if($report->morning_note || $report->evening_note)
            <p class="mt-2 text-xs text-slate-600 line-clamp-2">{{ Str::limit($report->morning_note ?? $report->evening_note, 60) }}</p>
            @endif
        </a>
        @empty
        <div class="col-span-full bg-white rounded-xl border border-slate-100 p-8 text-center text-slate-500">
            No reports in this range. Use the button above to add a report (morning or evening within the time windows).
        </div>
        @endforelse
    </div>
</div>
@endsection
