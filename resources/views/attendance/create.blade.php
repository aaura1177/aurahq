@extends('layouts.admin')
@section('header', 'Mark Attendance')
@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-slate-100 p-6">
    <form action="{{ route('attendance.store') }}" method="POST">
        @csrf
        <div class="mb-6">
            <label for="user_id" class="block text-sm font-bold text-slate-700 mb-2">Employee</label>
            <select name="user_id" id="user_id" class="w-full border border-slate-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                <option value="">-- Select Employee --</option>
                @foreach($employees as $emp)
                <option value="{{ $emp->id }}" {{ old('user_id', request('user_id')) == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                @endforeach
            </select>
            @error('user_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-6">
            <label for="date" class="block text-sm font-bold text-slate-700 mb-2">Date</label>
            <input type="date" name="date" id="date" value="{{ old('date', $date->format('Y-m-d')) }}" class="w-full border border-slate-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
            @error('date')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-slate-500">Default for this date: {{ $defaultStatus }} (Sunday, odd Saturdays & holidays = Off)</p>
        </div>
        <div class="mb-6">
            <label class="block text-sm font-bold text-slate-700 mb-2">Status</label>
            <div class="flex gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="status" value="present" {{ old('status', $defaultStatus) === 'present' ? 'checked' : '' }} class="text-green-600">
                    <span class="text-slate-700">Present</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="status" value="absent" {{ old('status', $defaultStatus) === 'absent' ? 'checked' : '' }} class="text-red-600">
                    <span class="text-slate-700">Absent</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="status" value="off" {{ old('status', $defaultStatus) === 'off' ? 'checked' : '' }} class="text-slate-600">
                    <span class="text-slate-700">Off</span>
                </label>
            </div>
            @error('status')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-6">
            <label for="notes" class="block text-sm font-bold text-slate-700 mb-2">Notes (optional)</label>
            <textarea name="notes" id="notes" rows="2" class="w-full border border-slate-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Optional note">{{ old('notes') }}</textarea>
            @error('notes')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex gap-4">
            <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg font-bold hover:bg-blue-700">Save</button>
            <a href="{{ route('attendance.index') }}" class="flex-1 bg-slate-100 text-slate-700 py-2 rounded-lg font-bold hover:bg-slate-200 text-center">Cancel</a>
        </div>
    </form>
</div>
@endsection
