@extends('layouts.admin')
@section('header', 'Holidays')
@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-4 border-b border-slate-100 flex justify-between items-center">
        <h3 class="font-bold text-slate-700">Holiday List</h3>
        @can('create holidays')
        <a href="{{ route('holidays.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 shadow">+ Add Holiday</a>
        @endcan
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-slate-600 text-sm font-semibold">
                <tr>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Date</th>
                    @if(auth()->user()->can('edit holidays') || auth()->user()->can('delete holidays'))
                    <th class="px-6 py-3 text-right">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($holidays as $holiday)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4 font-medium text-slate-800">{{ $holiday->name }}</td>
                    <td class="px-6 py-4 text-slate-600">{{ $holiday->date->format('d M Y') }}</td>
                    @if(auth()->user()->can('edit holidays') || auth()->user()->can('delete holidays'))
                    <td class="px-6 py-4 text-right">
                        @can('edit holidays')
                        <a href="{{ route('holidays.edit', $holiday) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</a>
                        @endcan
                        @can('delete holidays')
                        <form action="{{ route('holidays.destroy', $holiday) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete this holiday?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                        </form>
                        @endcan
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ (auth()->user()->can('edit holidays') || auth()->user()->can('delete holidays')) ? 3 : 2 }}" class="px-6 py-8 text-center text-slate-500">No holidays added yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
