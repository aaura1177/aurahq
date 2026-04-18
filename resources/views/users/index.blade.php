@extends('layouts.admin')
@section('title', 'Users')
@section('header', 'User Management')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-4 border-b border-slate-100 flex justify-between items-center">
        <h3 class="font-bold text-slate-700">All Users</h3>
        <a href="{{ route('users.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">+ Add New User</a>
    </div>
    <table class="w-full text-sm text-left">
        <thead class="bg-slate-50 text-slate-500 font-medium">
            <tr>
                <th class="px-6 py-4">Name</th>
                <th class="px-6 py-4">Email</th>
                <th class="px-6 py-4">Role</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($users as $user)
            <tr class="hover:bg-slate-50 {{ !$user->is_active ? 'bg-slate-50 opacity-75' : '' }}">
                <td class="px-6 py-4 font-medium">{{ $user->name }}</td>
                <td class="px-6 py-4">{{ $user->email }}</td>
                <td class="px-6 py-4">
                    @foreach($user->roles as $role)
                        <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded text-xs font-bold uppercase">{{ $role->name }}</span>
                    @endforeach
                </td>
                <td class="px-6 py-4">
                    @if($user->is_active)
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">Active</span>
                    @else
                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-bold">Blocked</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                        @if($user->id !== auth()->id())
                            <form action="{{ route('users.toggle', $user->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="text-xs px-2 py-1 rounded font-bold {{ $user->is_active ? 'bg-orange-100 text-orange-700 hover:bg-orange-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                                    {{ $user->is_active ? 'Block' : 'Unblock' }}
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('users.edit', $user->id) }}" class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs font-bold hover:bg-yellow-200">Edit</a>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Delete this user?');">
                            @csrf @method('DELETE')
                            <button class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold hover:bg-red-200">Delete</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4">{{ $users->links() }}</div>
</div>
@endsection