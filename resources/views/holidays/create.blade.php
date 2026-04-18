@extends('layouts.admin')
@section('header', 'Add Holiday')
@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-slate-100 p-6">
    <form action="{{ route('holidays.store') }}" method="POST">
        @csrf
        <div class="mb-6">
            <label for="name" class="block text-sm font-bold text-slate-700 mb-2">Holiday Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full border border-slate-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Independence Day" required>
            @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-6">
            <label for="date" class="block text-sm font-bold text-slate-700 mb-2">Date</label>
            <input type="date" name="date" id="date" value="{{ old('date') }}" class="w-full border border-slate-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
            @error('date')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex gap-4">
            <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg font-bold hover:bg-blue-700">Save Holiday</button>
            <a href="{{ route('holidays.index') }}" class="flex-1 bg-slate-100 text-slate-700 py-2 rounded-lg font-bold hover:bg-slate-200 text-center">Cancel</a>
        </div>
    </form>
</div>
@endsection
