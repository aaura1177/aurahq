@extends('layouts.admin')
@section('header', 'Add Finance Contact')
@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-slate-100 p-6">
    <form action="{{ route('finance-contacts.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-bold text-slate-700">Contact Name</label>
            <input type="text" name="name" class="w-full mt-1 border rounded-lg px-4 py-2" placeholder="e.g. Client X or Vendor Y" required>
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-700">Phone Number (Optional)</label>
            <input type="text" name="phone" class="w-full mt-1 border rounded-lg px-4 py-2">
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-700">Email (Optional)</label>
            <input type="email" name="email" class="w-full mt-1 border rounded-lg px-4 py-2">
        </div>
        <div class="pt-4">
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-bold hover:bg-blue-700">Save Contact</button>
        </div>
    </form>
</div>
@endsection
