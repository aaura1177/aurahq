@extends('layouts.admin')
@section('header', 'Edit Attendance')
@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-slate-100 p-6">
    <p class="text-slate-600 mb-4"><strong>{{ $attendance->user->name }}</strong> – {{ $attendance->date->format('l, d F Y') }}</p>
    <form action="{{ route('attendance.update', $attendance) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-6">
            <label class="block text-sm font-bold text-slate-700 mb-2">Status</label>
            <div class="flex gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="status" value="present" {{ old('status', $attendance->status) === 'present' ? 'checked' : '' }} class="text-green-600">
                    <span class="text-slate-700">Present</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="status" value="absent" {{ old('status', $attendance->status) === 'absent' ? 'checked' : '' }} class="text-red-600">
                    <span class="text-slate-700">Absent</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="status" value="off" {{ old('status', $attendance->status) === 'off' ? 'checked' : '' }} class="text-slate-600">
                    <span class="text-slate-700">Off</span>
                </label>
            </div>
            @error('status')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-6">
            <label for="notes" class="block text-sm font-bold text-slate-700 mb-2">Notes (optional)</label>
            <textarea name="notes" id="notes" rows="2" class="w-full border border-slate-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('notes', $attendance->notes) }}</textarea>
            @error('notes')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex gap-4">
            <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg font-bold hover:bg-blue-700">Update</button>
            <a href="{{ route('attendance.index', ['employee_id' => $attendance->user_id, 'start_date' => $attendance->date->format('Y-m-d'), 'end_date' => $attendance->date->format('Y-m-d')]) }}" class="flex-1 bg-slate-100 text-slate-700 py-2 rounded-lg font-bold hover:bg-slate-200 text-center">Cancel</a>
        </div>
    </form>
</div>
@endsection
