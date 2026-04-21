@extends('layouts.admin')
@section('title', 'Edit Lead')
@section('header', 'Edit Lead')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow-sm border border-slate-100">
    <form action="{{ route('leads.update', $lead) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Business name <span class="text-red-500">*</span></label>
            <input type="text" name="business_name" value="{{ old('business_name', $lead->business_name) }}" required class="w-full border rounded-lg p-2.5 text-sm">
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Contact person</label>
                <input type="text" name="contact_person" value="{{ old('contact_person', $lead->contact_person) }}" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $lead->phone) }}" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $lead->email) }}" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Website</label>
                <input type="text" name="website" value="{{ old('website', $lead->website) }}" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Industry</label>
                <select name="industry" class="w-full border rounded-lg p-2.5 bg-white text-sm">
                    <option value="">—</option>
                    @foreach($industries as $ind)
                    <option value="{{ $ind }}" {{ old('industry', $lead->industry) === $ind ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $ind)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">City</label>
                <input type="text" name="city" value="{{ old('city', $lead->city) }}" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Source</label>
                <select name="source" class="w-full border rounded-lg p-2.5 bg-white text-sm">
                    <option value="">—</option>
                    @foreach($sources as $src)
                    <option value="{{ $src }}" {{ old('source', $lead->source) === $src ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $src)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Stage</label>
                <select name="stage" id="edit-stage" class="w-full border rounded-lg p-2.5 bg-white text-sm">
                    @foreach($stages as $st)
                    <option value="{{ $st }}" {{ old('stage', $lead->stage) === $st ? 'selected' : '' }}>{{ str_replace('_', ' ', ucfirst($st)) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Estimated value (₹)</label>
                <input type="number" step="0.01" min="0" name="estimated_value" value="{{ old('estimated_value', $lead->estimated_value) }}" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Assigned to</label>
                <select name="assigned_to" class="w-full border rounded-lg p-2.5 bg-white text-sm">
                    <option value="">—</option>
                    @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ old('assigned_to', $lead->assigned_to) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1" id="follow-up">Next follow-up</label>
            <input type="date" name="next_follow_up" value="{{ old('next_follow_up', $lead->next_follow_up?->format('Y-m-d')) }}" class="w-full border rounded-lg p-2.5 text-sm max-w-xs">
        </div>
        <div id="edit-lost-wrap" class="{{ $lead->stage === 'lost' || old('stage') === 'lost' ? '' : 'hidden' }}">
            <label class="block text-sm font-bold text-slate-700 mb-1">Lost reason <span class="text-red-500">*</span></label>
            <input type="text" name="lost_reason" value="{{ old('lost_reason', $lead->lost_reason) }}" class="w-full border rounded-lg p-2.5 text-sm">
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Notes</label>
            <textarea name="notes" rows="3" class="w-full border rounded-lg p-2.5 text-sm">{{ old('notes', $lead->notes) }}</textarea>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-blue-700">Save</button>
            <a href="{{ route('leads.show', $lead) }}" class="px-6 py-2.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">Cancel</a>
        </div>
    </form>
</div>
<script>
    const es = document.getElementById('edit-stage');
    const lw = document.getElementById('edit-lost-wrap');
    es?.addEventListener('change', () => { if (lw) lw.classList.toggle('hidden', es.value !== 'lost'); });
</script>
@endsection
