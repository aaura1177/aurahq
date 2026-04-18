@extends('layouts.admin')
@section('header', 'Manage report access')
@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <p class="text-sm text-slate-600">Grant employees permission to submit reports outside time windows or to edit a report for any day for a limited time. Changes apply immediately.</p>

    @if(session('success'))
    <div class="p-4 rounded-lg bg-green-50 text-green-800 border border-green-200">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="p-4 rounded-lg bg-red-50 text-red-800 border border-red-200">{{ session('error') }}</div>
    @endif

    {{-- Grant edit access (any day, for X hours) --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <h3 class="font-bold text-slate-800 mb-2">Grant edit access</h3>
        <p class="text-sm text-slate-600 mb-4">Allow an employee to edit a report for a specific date for a time period (e.g. 1 hour). They can edit that report until the time expires.</p>
        <form action="{{ route('daily-reports.grant-edit') }}" method="POST" class="flex flex-wrap items-end gap-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Employee</label>
                <select name="user_id" required class="border border-slate-200 rounded-lg px-3 py-2 min-w-[180px]">
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Report date</label>
                <input type="date" name="date" required class="border border-slate-200 rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Duration (minutes)</label>
                <select name="duration_minutes" class="border border-slate-200 rounded-lg px-3 py-2">
                    <option value="30">30 min</option>
                    <option value="60" selected>1 hour</option>
                    <option value="120">2 hours</option>
                    <option value="180">3 hours</option>
                    <option value="360">6 hours</option>
                    <option value="480">8 hours</option>
                    <option value="1440">24 hours</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">Grant edit access</button>
        </form>
    </div>

    {{-- Allow submission at any time (morning/evening for a date) --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <h3 class="font-bold text-slate-800 mb-2">Allow submission at any time</h3>
        <p class="text-sm text-slate-600 mb-4">Let an employee submit morning or evening report outside the normal time window for a given date. Set morning and evening separately.</p>
        <form action="{{ route('daily-reports.allow-submission') }}" method="POST" class="flex flex-wrap items-end gap-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Employee</label>
                <select name="user_id" required class="border border-slate-200 rounded-lg px-3 py-2 min-w-[180px]">
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Date</label>
                <input type="date" name="date" required class="border border-slate-200 rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Slot</label>
                <select name="slot" class="border border-slate-200 rounded-lg px-3 py-2">
                    <option value="morning">Morning</option>
                    <option value="evening">Evening</option>
                </select>
            </div>
            <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-amber-700">Allow submission</button>
        </form>
    </div>

    {{-- Active edit grants --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <h3 class="font-bold text-slate-800 mb-2">Active edit grants</h3>
        <p class="text-sm text-slate-600 mb-4">Time-limited permissions to edit a report. Expired grants are hidden.</p>
        @if($editGrants->isEmpty())
        <p class="text-slate-500 text-sm">No active edit grants.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-left text-slate-600 font-medium">
                        <th class="py-2 pr-4">Employee</th>
                        <th class="py-2 pr-4">Report date</th>
                        <th class="py-2 pr-4">Expires at</th>
                        <th class="py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($editGrants as $grant)
                    <tr class="border-b border-slate-100">
                        <td class="py-2 pr-4">{{ $grant->user->name ?? '—' }}</td>
                        <td class="py-2 pr-4">{{ $grant->date->format('d M Y') }}</td>
                        <td class="py-2 pr-4">{{ $grant->expires_at->setTimezone('Asia/Kolkata')->format('d M Y H:i') }} IST</td>
                        <td class="py-2">
                            <form action="{{ route('daily-reports.revoke-edit-grant') }}" method="POST" class="inline" onsubmit="return confirm('Revoke this edit grant?');">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $grant->user_id }}">
                                <input type="hidden" name="date" value="{{ $grant->date->format('Y-m-d') }}">
                                <button type="submit" class="text-red-600 hover:underline text-xs">Revoke</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Submission overrides --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <h3 class="font-bold text-slate-800 mb-2">Submission overrides</h3>
        <p class="text-sm text-slate-600 mb-4">Employees who can submit morning or evening report at any time for the listed date.</p>
        @if($submissionOverrides->isEmpty())
        <p class="text-slate-500 text-sm">No submission overrides.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-left text-slate-600 font-medium">
                        <th class="py-2 pr-4">Employee</th>
                        <th class="py-2 pr-4">Date</th>
                        <th class="py-2 pr-4">Morning</th>
                        <th class="py-2 pr-4">Evening</th>
                        <th class="py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($submissionOverrides as $override)
                    <tr class="border-b border-slate-100">
                        <td class="py-2 pr-4">{{ $override->user->name ?? '—' }}</td>
                        <td class="py-2 pr-4">{{ $override->date->format('d M Y') }}</td>
                        <td class="py-2 pr-4">
                            @if($override->allow_morning)
                            <span class="text-green-600">Allowed</span>
                            <form action="{{ route('daily-reports.revoke-submission-override') }}" method="POST" class="inline ml-1">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $override->user_id }}">
                                <input type="hidden" name="date" value="{{ $override->date->format('Y-m-d') }}">
                                <input type="hidden" name="slot" value="morning">
                                <button type="submit" class="text-red-600 hover:underline text-xs">Revoke</button>
                            </form>
                            @else — @endif
                        </td>
                        <td class="py-2 pr-4">
                            @if($override->allow_evening)
                            <span class="text-green-600">Allowed</span>
                            <form action="{{ route('daily-reports.revoke-submission-override') }}" method="POST" class="inline ml-1">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $override->user_id }}">
                                <input type="hidden" name="date" value="{{ $override->date->format('Y-m-d') }}">
                                <input type="hidden" name="slot" value="evening">
                                <button type="submit" class="text-red-600 hover:underline text-xs">Revoke</button>
                            </form>
                            @else — @endif
                        </td>
                        <td class="py-2"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <p><a href="{{ route('daily-reports.index') }}" class="text-slate-600 hover:underline text-sm">← Back to Daily Reports</a></p>
</div>
@endsection
