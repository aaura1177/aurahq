@extends('layouts.admin')
@section('header', 'Finance Contacts')
@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-4 border-b border-slate-100 flex justify-between items-center">
        <h3 class="font-bold text-slate-700">Directory & Balances</h3>
        @can('create finance contacts')
        <a href="{{ route('finance-contacts.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm shadow hover:bg-blue-700">+ Add Contact</a>
        @endcan
    </div>
    <table class="w-full text-sm text-left">
        <thead class="bg-slate-50 text-slate-500 font-medium">
            <tr>
                <th class="px-6 py-4">Name</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4">Net Balance</th>
                <th class="px-6 py-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($contacts as $contact)
            @php $net = $contact->netBalance(); @endphp
            <tr class="hover:bg-slate-50 {{ !$contact->is_active ? 'bg-slate-50 opacity-75' : '' }}">
                <td class="px-6 py-4 font-bold text-slate-700">{{ $contact->name }}</td>
                <td class="px-6 py-4">
                    @if($contact->is_active)
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">Active</span>
                    @else
                        <span class="bg-slate-200 text-slate-600 px-2 py-1 rounded text-xs font-bold">Disabled</span>
                    @endif
                </td>
                <td class="px-6 py-4 font-bold {{ $net < 0 ? 'text-red-600' : 'text-green-600' }}">
                    ₹{{ number_format(abs($net), 2) }} {{ $net < 0 ? '(Dr)' : '(Cr)' }}
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('finance-contacts.show', $contact->id) }}" class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-bold">View</a>
                        
                        @can('edit finance contacts')
                        <a href="{{ route('finance-contacts.edit', $contact->id) }}" class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs font-bold">Edit</a>
                        
                        <form action="{{ route('finance-contacts.toggle', $contact->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <button class="px-2 py-1 rounded text-xs font-bold {{ $contact->is_active ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700' }}">
                                {{ $contact->is_active ? 'Disable' : 'Enable' }}
                            </button>
                        </form>
                        @endcan

                        @can('delete finance contacts')
                        <form action="{{ route('finance-contacts.destroy', $contact->id) }}" method="POST" onsubmit="return confirm('Delete permanently?');">
                            @csrf @method('DELETE')
                            <button class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">Delete</button>
                        </form>
                        @endcan
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
