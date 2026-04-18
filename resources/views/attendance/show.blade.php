@extends('layouts.admin')
@section('header', 'Attendance Record')
@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-slate-100 p-6">
    <dl class="space-y-4">
        <div>
            <dt class="text-sm font-bold text-slate-500 uppercase tracking-wider">Employee</dt>
            <dd class="mt-1 text-lg text-slate-800">{{ $attendance->user->name }}</dd>
        </div>
        <div>
            <dt class="text-sm font-bold text-slate-500 uppercase tracking-wider">Date</dt>
            <dd class="mt-1 text-lg text-slate-800">{{ $attendance->date->format('l, d F Y') }}</dd>
        </div>
        <div>
            <dt class="text-sm font-bold text-slate-500 uppercase tracking-wider">Status</dt>
            <dd class="mt-1">
                @if($attendance->status === 'present')
                <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Present</span>
                @elseif($attendance->status === 'absent')
                <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">Absent</span>
                @else
                <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-slate-100 text-slate-700">Off</span>
                @endif
            </dd>
        </div>
        @if($attendance->notes)
        <div>
            <dt class="text-sm font-bold text-slate-500 uppercase tracking-wider">Notes</dt>
            <dd class="mt-1 text-slate-700">{{ $attendance->notes }}</dd>
        </div>
        @endif
    </dl>
    @can('edit attendance')
    <div class="mt-6 pt-6 border-t border-slate-100">
        <a href="{{ route('attendance.edit', $attendance) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 inline-block">Edit</a>
    </div>
    @endcan
    <div class="mt-4">
        <a href="{{ route('attendance.index', ['employee_id' => $attendance->user_id]) }}" class="text-slate-600 hover:text-slate-800 text-sm">← Back to list</a>
    </div>
</div>
@endsection
