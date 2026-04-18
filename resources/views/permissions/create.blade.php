@extends('layouts.admin')
@section('header', 'Create Permission')
@section('content')
<div class="max-w-xl mx-auto bg-white rounded-xl shadow-sm border border-slate-100 p-6">
    <form action="{{ route('permissions.store') }}" method="POST">
        @csrf
        <div class="mb-6">
            <label class="block text-sm font-bold text-slate-700 mb-2">Permission Name</label>
            <input type="text" name="name" class="w-full border rounded-lg px-4 py-2" placeholder="e.g. export data" required>
            <p class="text-xs text-slate-500 mt-1">Use a descriptive name like "view_reports" or "approve_budget"</p>
        </div>
        <div class="flex gap-4">
            <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg font-bold hover:bg-blue-700">Save</button>
            <a href="{{ route('permissions.index') }}" class="flex-1 bg-slate-100 text-slate-700 py-2 rounded-lg font-bold hover:bg-slate-200 text-center">Cancel</a>
        </div>
    </form>
</div>
@endsection
