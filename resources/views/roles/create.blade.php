@extends('layouts.admin')
@section('header', 'Create New Role')
@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm border border-slate-100 p-6">
    <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        <div class="mb-6">
            <label class="block text-sm font-bold text-slate-700 mb-2">Role Name</label>
            <input type="text" name="name" class="w-full border rounded-lg px-4 py-2" placeholder="e.g. manager" required>
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-bold text-slate-700 mb-2">Assign Permissions</label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($permissions as $perm)
                <label class="flex items-center space-x-2 p-2 border rounded hover:bg-slate-50 cursor-pointer">
                    <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" class="rounded text-blue-600">
                    <span class="text-xs capitalize">{{ str_replace('_', ' ', $perm->name) }}</span>
                </label>
                @endforeach
            </div>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-bold hover:bg-blue-700">Save Role</button>
    </form>
</div>
@endsection