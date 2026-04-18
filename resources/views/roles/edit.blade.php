@extends('layouts.admin')
@section('header', 'Edit Role: ' . ucfirst($role->name))
@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm border border-slate-100 p-6">
    <form action="{{ route('roles.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-6">
            <label class="block text-sm font-bold text-slate-700 mb-2">Role Name</label>
            <input type="text" name="name" value="{{ $role->name }}" class="w-full border rounded-lg px-4 py-2" required>
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-bold text-slate-700 mb-2">Assign Permissions</label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($permissions as $perm)
                <label class="flex items-center space-x-2 p-2 border rounded hover:bg-slate-50 cursor-pointer {{ $role->hasPermissionTo($perm->name) ? 'bg-blue-50 border-blue-200' : '' }}">
                    <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" class="rounded text-blue-600" 
                        {{ $role->hasPermissionTo($perm->name) ? 'checked' : '' }}>
                    <span class="text-xs capitalize">{{ str_replace('_', ' ', $perm->name) }}</span>
                </label>
                @endforeach
            </div>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg font-bold hover:bg-blue-700">Update Role</button>
            <a href="{{ route('roles.index') }}" class="flex-1 bg-slate-100 text-slate-700 py-2 rounded-lg font-bold hover:bg-slate-200 text-center">Cancel</a>
        </div>
    </form>
</div>
@endsection
