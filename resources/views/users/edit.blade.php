@extends('layouts.admin')
@section('title', 'Edit User')
@section('header', 'Edit User')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-slate-100 p-6">
    <form action="{{ route('users.update', $user->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-medium text-slate-700">Full Name</label>
            <input type="text" name="name" value="{{ $user->name }}" class="w-full mt-1 border rounded-lg px-4 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Email Address</label>
            <input type="email" name="email" value="{{ $user->email }}" class="w-full mt-1 border rounded-lg px-4 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Password (Leave blank to keep current)</label>
            <input type="password" name="password" class="w-full mt-1 border rounded-lg px-4 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Assign Role</label>
            <select name="role" class="w-full mt-1 border rounded-lg px-4 py-2 bg-white">
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="pt-4 flex gap-4">
            <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg font-bold hover:bg-blue-700">Update User</button>
            <a href="{{ route('users.index') }}" class="flex-1 bg-slate-100 text-slate-700 py-2 rounded-lg font-bold hover:bg-slate-200 text-center">Cancel</a>
        </div>
    </form>
</div>
@endsection
