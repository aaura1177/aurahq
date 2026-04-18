@extends('layouts.admin')
@section('header', 'Attendance Report')
@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
        <form method="GET" action="{{ route('attendance.report') }}" class="flex flex-wrap items-end gap-4">
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

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-700">Summary: {{ $start->format('d M Y') }} – {{ $end->format('d M Y') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-600 text-sm font-semibold">
                    <tr>
                        <th class="px-6 py-3">Employee</th>
                        <th class="px-6 py-3 text-center">Present</th>
                        <th class="px-6 py-3 text-center">Absent</th>
                        <th class="px-6 py-3 text-center">Off</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($summaries as $s)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $s['employee']->name }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded bg-green-100 text-green-800 font-semibold">{{ $s['present'] }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded bg-red-100 text-red-800 font-semibold">{{ $s['absent'] }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold">{{ $s['off'] }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if(empty($summaries))
        <div class="p-8 text-center text-slate-500">No employees with role "employee" found.</div>
        @endif
    </div>
    <div>
        <a href="{{ route('attendance.index') }}" class="text-slate-600 hover:text-slate-800 text-sm">← Back to Attendance</a>
    </div>
</div>
@endsection
