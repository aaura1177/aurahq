@extends('layouts.admin')
@section('header', 'Roles')
@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-4 border-b border-slate-100 flex justify-between items-center">
        <h3 class="font-bold text-slate-700">System Roles</h3>
        <a href="{{ route('roles.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 shadow">+ Create Role</a>
    </div>
    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($roles as $role)
        <div class="border rounded-lg p-4 bg-slate-50 relative group">
            <div class="flex justify-between items-center mb-2">
                <h4 class="font-bold text-lg text-slate-800 capitalize">{{ $role->name }}</h4>
                <div class="flex gap-2">
                    <a href="{{ route('roles.edit', $role->id) }}" class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded hover:bg-yellow-200">Edit</a>
                    @if($role->name !== 'super-admin')
                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Delete this role?');">
                        @csrf @method('DELETE')
                        <button class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded hover:bg-red-200">Delete</button>
                    </form>
                    @endif
                </div>
            </div>
            <p class="text-xs text-slate-500 mb-2">{{ $role->users_count ?? 0 }} Users Assigned</p>
            <div class="flex flex-wrap gap-1 h-20 overflow-y-auto">
                @forelse($role->permissions as $perm)
                    <span class="text-[10px] bg-white border px-1 rounded">{{ $perm->name }}</span>
                @empty
                    <span class="text-xs text-slate-400">No special permissions</span>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection