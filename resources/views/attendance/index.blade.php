@extends('layouts.admin')
@section('header', 'Attendance')
@section('content')
<div class="space-y-6">
    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
        <form method="GET" action="{{ route('attendance.index') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Employee</label>
                <select name="employee_id" class="border border-slate-200 rounded-lg px-3 py-2 min-w-[200px]">
                    <option value="" {{ !$employeeId ? 'selected' : '' }}>All</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ $employeeId == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
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

    @if($employeeId)
    {{-- Summary (single employee) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
            <p class="text-sm font-bold text-green-700 uppercase tracking-wider">Present</p>
            <p class="text-2xl font-bold text-green-800">{{ $totalPresent }}</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <p class="text-sm font-bold text-red-700 uppercase tracking-wider">Absent</p>
            <p class="text-2xl font-bold text-red-800">{{ $totalAbsent }}</p>
        </div>
        <div class="bg-slate-100 border border-slate-200 rounded-xl p-4">
            <p class="text-sm font-bold text-slate-700 uppercase tracking-wider">Off</p>
            <p class="text-2xl font-bold text-slate-800">{{ $totalOff }}</p>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex justify-between items-center flex-wrap gap-2">
            <h3 class="font-bold text-slate-700">Attendance {{ $selectedEmployee ? '• ' . $selectedEmployee->name : '• All employees' }} ({{ $start->format('d M Y') }}{{ $start->format('Y-m-d') !== $end->format('Y-m-d') ? ' – ' . $end->format('d M Y') : '' }})</h3>
            <div class="flex gap-2">
                @can('view attendance')
                <a href="{{ route('attendance.report') }}" class="bg-slate-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-slate-700">Period Report</a>
                @endcan
                @can('create attendance')
                <a href="{{ route('attendance.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">+ Mark Attendance</a>
                @endcan
            </div>
        </div>
        @if($employeeId)
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-600 text-sm font-semibold">
                    <tr>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Notes</th>
                        @if(auth()->user()->can('edit attendance') || auth()->user()->can('delete attendance'))
                        <th class="px-6 py-3 text-right">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php
                        $recordByDate = $records->keyBy(fn($r) => $r->date->format('Y-m-d'));
                    @endphp
                    @for($d = $start->copy(); $d->lte($end); $d->addDay())
                    @php
                        $dateStr = $d->format('Y-m-d');
                        $rec = $recordByDate->get($dateStr);
                        $status = $rec ? $rec->status : \App\Models\AttendanceRecord::defaultStatusForDate($d);
                    @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-3 text-slate-800">{{ $d->format('D, d M Y') }}</td>
                        <td class="px-6 py-3">
                            @if($status === 'present')
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Present</span>
                            @elseif($status === 'absent')
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Absent</span>
                            @else
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">Off</span>
                            @endif
                            @if(!$rec)
                            <span class="text-slate-400 text-xs ml-1">(default)</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-slate-600 text-sm">{{ $rec?->notes ?? '—' }}</td>
                        @if(auth()->user()->can('edit attendance') || auth()->user()->can('delete attendance'))
                        <td class="px-6 py-3 text-right">
                            @if($rec)
                            @can('edit attendance')
                            <a href="{{ route('attendance.edit', $rec) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</a>
                            @endcan
                            @can('delete attendance')
                            <form action="{{ route('attendance.destroy', $rec) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Remove this attendance record?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                            </form>
                            @endcan
                            @else
                            @can('create attendance')
                            <a href="{{ route('attendance.create', ['user_id' => $employeeId, 'date' => $dateStr]) }}" class="text-slate-500 hover:text-blue-600 text-sm">Mark</a>
                            @endcan
                            @endif
                        </td>
                        @endif
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        @else
        {{-- All employees view --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-600 font-semibold">
                    <tr>
                        <th class="px-4 py-3 sticky left-0 bg-slate-50 z-10">Employee</th>
                        @foreach($datesInRange as $d)
                        <th class="px-3 py-3 whitespace-nowrap">{{ $d->format('D, d') }}</th>
                        @endforeach
                        <th class="px-3 py-3 text-center">Present</th>
                        <th class="px-3 py-3 text-center">Absent</th>
                        <th class="px-3 py-3 text-center">Off</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($employees as $emp)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-800 sticky left-0 bg-white z-10">
                            <a href="{{ route('attendance.index', ['employee_id' => $emp->id, 'start_date' => $start->format('Y-m-d'), 'end_date' => $end->format('Y-m-d')]) }}" class="text-blue-600 hover:text-blue-800">{{ $emp->name }}</a>
                        </td>
                        @foreach($datesInRange as $d)
                        <td class="px-3 py-2">
                            @php $st = $attendanceGrid[$emp->id][$d->format('Y-m-d')] ?? 'off'; @endphp
                            @if($st === 'present')
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">P</span>
                            @elseif($st === 'absent')
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">A</span>
                            @else
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-600">O</span>
                            @endif
                        </td>
                        @endforeach
                        <td class="px-3 py-2 text-center font-semibold text-green-700">{{ $allSummaries[$emp->id]['present'] ?? 0 }}</td>
                        <td class="px-3 py-2 text-center font-semibold text-red-700">{{ $allSummaries[$emp->id]['absent'] ?? 0 }}</td>
                        <td class="px-3 py-2 text-center font-semibold text-slate-600">{{ $allSummaries[$emp->id]['off'] ?? 0 }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($employees->isEmpty())
        <div class="p-8 text-center text-slate-500">No employees found. Add users with the employee role.</div>
        @endif
        @endif
    </div>
</div>
@endsection
