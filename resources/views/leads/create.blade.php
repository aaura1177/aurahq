@extends('layouts.admin')
@section('title', 'Add Lead')
@section('header', 'New Lead')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow-sm border border-slate-100">
    <form action="{{ route('leads.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Business name <span class="text-red-500">*</span></label>
            <input type="text" name="business_name" value="{{ old('business_name') }}" required class="w-full border rounded-lg p-2.5 text-sm">
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Contact person</label>
                <input type="text" name="contact_person" value="{{ old('contact_person') }}" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Website</label>
                <input type="text" name="website" value="{{ old('website') }}" placeholder="example.com" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Industry</label>
                <select name="industry" class="w-full border rounded-lg p-2.5 bg-white text-sm">
                    <option value="">—</option>
                    @foreach($industries as $ind)
                    <option value="{{ $ind }}" {{ old('industry') === $ind ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $ind)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">City</label>
                <input type="text" name="city" value="{{ old('city') }}" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Source</label>
                <select name="source" class="w-full border rounded-lg p-2.5 bg-white text-sm">
                    <option value="">—</option>
                    @foreach($sources as $src)
                    <option value="{{ $src }}" {{ old('source') === $src ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $src)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Stage</label>
                <select name="stage" class="w-full border rounded-lg p-2.5 bg-white text-sm">
                    @foreach($stages as $st)
                    <option value="{{ $st }}" {{ old('stage', 'prospect') === $st ? 'selected' : '' }}>{{ str_replace('_', ' ', ucfirst($st)) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Estimated value (₹)</label>
                <input type="number" step="0.01" min="0" name="estimated_value" value="{{ old('estimated_value') }}" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Assigned to</label>
                <select name="assigned_to" class="w-full border rounded-lg p-2.5 bg-white text-sm">
                    <option value="">—</option>
                    @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ old('assigned_to', auth()->id()) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Next follow-up</label>
            <input type="date" name="next_follow_up" value="{{ old('next_follow_up') }}" class="w-full border rounded-lg p-2.5 text-sm max-w-xs">
            <p class="text-xs text-slate-400 mt-1">Leave blank to default to 4 days from today (unless won/lost).</p>
        </div>
        <div id="lost-wrap" class="hidden">
            <label class="block text-sm font-bold text-slate-700 mb-1">Lost reason <span class="text-red-500">*</span></label>
            <input type="text" name="lost_reason" value="{{ old('lost_reason') }}" class="w-full border rounded-lg p-2.5 text-sm">
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Notes</label>
            <textarea name="notes" rows="3" class="w-full border rounded-lg p-2.5 text-sm">{{ old('notes') }}</textarea>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-blue-700">Create lead</button>
            <a href="{{ route('leads.index') }}" class="px-6 py-2.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">Cancel</a>
        </div>
    </form>
</div>
<script>
    const stageSel = document.querySelector('select[name="stage"]');
    const lostWrap = document.getElementById('lost-wrap');
    function syncLost() {
        if (!stageSel || !lostWrap) return;
        lostWrap.classList.toggle('hidden', stageSel.value !== 'lost');
    }
    stageSel?.addEventListener('change', syncLost);
    syncLost();
</script>
@endsection
