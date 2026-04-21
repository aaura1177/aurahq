@extends('layouts.admin')
@section('title', 'Edit project')
@section('header', 'Edit project')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded-xl shadow-sm border border-slate-100">
    <form action="{{ route('projects.update', $project) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $project->name) }}" required class="w-full border rounded-lg p-2.5 text-sm">
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Client <span class="text-red-500">*</span></label>
            <select name="client_id" required class="w-full border rounded-lg p-2.5 text-sm bg-white">
                @foreach($clients as $c)
                <option value="{{ $c->id }}" {{ old('client_id', $project->client_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Description</label>
            <textarea name="description" rows="3" class="w-full border rounded-lg p-2.5 text-sm">{{ old('description', $project->description) }}</textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Venture</label>
                <select name="venture" class="w-full border rounded-lg p-2.5 text-sm bg-white">
                    @foreach(\App\Models\Project::VENTURES as $v)
                    <option value="{{ $v }}" {{ old('venture', $project->venture) === $v ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Status</label>
                <select name="status" class="w-full border rounded-lg p-2.5 text-sm bg-white">
                    @foreach(\App\Models\Project::STATUSES as $st)
                    <option value="{{ $st }}" {{ old('status', $project->status) === $st ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $st)) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Budget (₹)</label>
            <input type="number" step="0.01" min="0" name="budget" value="{{ old('budget', $project->budget) }}" class="w-full border rounded-lg p-2.5 text-sm">
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Start</label>
                <input type="date" name="start_date" value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Expected end</label>
                <input type="date" name="expected_end_date" value="{{ old('expected_end_date', $project->expected_end_date?->format('Y-m-d')) }}" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Actual end</label>
                <input type="date" name="actual_end_date" value="{{ old('actual_end_date', $project->actual_end_date?->format('Y-m-d')) }}" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $project->is_active) ? 'checked' : '' }}> Active
        </label>
        <div class="flex gap-3">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-bold">Save</button>
            <a href="{{ route('projects.show', $project) }}" class="px-6 py-2.5 border rounded-lg text-slate-600">Cancel</a>
        </div>
    </form>
</div>
@endsection
