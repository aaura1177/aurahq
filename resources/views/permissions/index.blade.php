@extends('layouts.admin')
@section('header', 'Permissions')
@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-4 border-b border-slate-100 flex justify-between items-center">
        <h3 class="font-bold text-slate-700">All Permissions</h3>
        <a href="{{ route('permissions.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 shadow">+ Add Permission</a>
    </div>
    <div class="p-4">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-500 font-medium">
                    <tr>
                        <th class="px-6 py-3">Permission Name</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($permissions as $permission)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-3 font-medium text-slate-700 capitalize">{{ str_replace('_', ' ', $permission->name) }}</td>
                        <td class="px-6 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('permissions.edit', $permission->id) }}" class="text-yellow-600 hover:text-yellow-800">Edit</a>
                                <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" onsubmit="return confirm('Delete permission?');">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $permissions->links() }}</div>
    </div>
</div>
@endsection
