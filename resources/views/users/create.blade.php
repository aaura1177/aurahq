@extends('layouts.admin')
@section('title', 'Add User')
@section('header', 'Create New User')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-slate-100 p-6">
    <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-700">Full Name</label>
            <input type="text" name="name" class="w-full mt-1 border rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Email Address</label>
            <input type="email" name="email" class="w-full mt-1 border rounded-lg px-4 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Password</label>
            <input type="password" name="password" class="w-full mt-1 border rounded-lg px-4 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Assign Role</label>
            <select name="role" class="w-full mt-1 border rounded-lg px-4 py-2 bg-white">
                @foreach($roles as $role)
                    <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                @endforeach
            </select>
        </div>
        <div class="pt-4">
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-bold hover:bg-blue-700">Create User</button>
        </div>
    </form>
</div>
@endsection
