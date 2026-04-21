@extends('layouts.admin')
@section('title', 'New project')
@section('header', 'New project')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded-xl shadow-sm border border-slate-100">
    <form action="{{ route('projects.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required class="w-full border rounded-lg p-2.5 text-sm">
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Client <span class="text-red-500">*</span></label>
            <select name="client_id" required class="w-full border rounded-lg p-2.5 text-sm bg-white">
                @foreach($clients as $c)
                <option value="{{ $c->id }}" {{ old('client_id', $clientId ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Description</label>
            <textarea name="description" rows="3" class="w-full border rounded-lg p-2.5 text-sm">{{ old('description') }}</textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Venture</label>
                <select name="venture" class="w-full border rounded-lg p-2.5 text-sm bg-white">
                    @foreach(\App\Models\Project::VENTURES as $v)
                    <option value="{{ $v }}" {{ old('venture', 'aurateria') === $v ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Status</label>
                <select name="status" class="w-full border rounded-lg p-2.5 text-sm bg-white">
                    @foreach(\App\Models\Project::STATUSES as $st)
                    <option value="{{ $st }}" {{ old('status', 'active') === $st ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $st)) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Budget (₹)</label>
                <input type="number" step="0.01" min="0" name="budget" value="{{ old('budget') }}" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Start</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Expected end</label>
                <input type="date" name="expected_end_date" value="{{ old('expected_end_date') }}" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Actual end</label>
                <input type="date" name="actual_end_date" value="{{ old('actual_end_date') }}" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-bold">Create</button>
            <a href="{{ route('projects.index') }}" class="px-6 py-2.5 border rounded-lg text-slate-600">Cancel</a>
        </div>
    </form>
</div>
@endsection
