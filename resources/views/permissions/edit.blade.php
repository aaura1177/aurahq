@extends('layouts.admin')
@section('header', 'Edit Permission')
@section('content')
<div class="max-w-xl mx-auto bg-white rounded-xl shadow-sm border border-slate-100 p-6">
    <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-6">
            <label class="block text-sm font-bold text-slate-700 mb-2">Permission Name</label>
            <input type="text" name="name" value="{{ $permission->name }}" class="w-full border rounded-lg px-4 py-2" required>
        </div>
        <div class="flex gap-4">
            <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg font-bold hover:bg-blue-700">Update</button>
            <a href="{{ route('permissions.index') }}" class="flex-1 bg-slate-100 text-slate-700 py-2 rounded-lg font-bold hover:bg-slate-200 text-center">Cancel</a>
        </div>
    </form>
</div>
@endsection
