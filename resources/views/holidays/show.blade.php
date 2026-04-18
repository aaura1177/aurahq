@extends('layouts.admin')
@section('header', $holiday->name)
@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-slate-100 p-6">
    <dl class="space-y-4">
        <div>
            <dt class="text-sm font-bold text-slate-500 uppercase tracking-wider">Name</dt>
            <dd class="mt-1 text-lg text-slate-800">{{ $holiday->name }}</dd>
        </div>
        <div>
            <dt class="text-sm font-bold text-slate-500 uppercase tracking-wider">Date</dt>
            <dd class="mt-1 text-lg text-slate-800">{{ $holiday->date->format('l, d F Y') }}</dd>
        </div>
    </dl>
    @can('edit holidays')
    <div class="mt-6 pt-6 border-t border-slate-100">
        <a href="{{ route('holidays.edit', $holiday) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 inline-block">Edit Holiday</a>
    </div>
    @endcan
    <div class="mt-4">
        <a href="{{ route('holidays.index') }}" class="text-slate-600 hover:text-slate-800 text-sm">← Back to list</a>
    </div>
</div>
@endsection
