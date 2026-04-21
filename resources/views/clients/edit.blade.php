@extends('layouts.admin')
@section('title', 'Edit client')
@section('header', 'Edit client')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded-xl shadow-sm border border-slate-100">
    <form action="{{ route('clients.update', $client) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $client->name) }}" required class="w-full border rounded-lg p-2.5 text-sm">
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Contact person</label>
                <input type="text" name="contact_person" value="{{ old('contact_person', $client->contact_person) }}" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $client->phone) }}" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $client->email) }}" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Company</label>
                <input type="text" name="company" value="{{ old('company', $client->company) }}" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Notes</label>
            <textarea name="notes" rows="3" class="w-full border rounded-lg p-2.5 text-sm">{{ old('notes', $client->notes) }}</textarea>
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $client->is_active) ? 'checked' : '' }}> Active
        </label>
        <div class="flex gap-3">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-blue-700">Save</button>
            <a href="{{ route('clients.show', $client) }}" class="px-6 py-2.5 rounded-lg border text-slate-600">Cancel</a>
        </div>
    </form>
</div>
@endsection
