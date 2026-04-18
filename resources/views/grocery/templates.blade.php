@extends('layouts.admin')
@section('header', 'Manage Daily Defaults')
@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Add Form -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 h-fit">
        <h3 class="font-bold text-lg mb-4">Add Default Item</h3>
        <form action="{{ route('grocery.templates.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Target Category</label>
                <select name="type" class="w-full border rounded p-2 bg-white" required>
                    <option value="today">Today (Daily)</option>
                    <option value="vegetables">Vegetables</option>
                    <option value="blinkit">Blinkit</option>
                    <option value="supermart">Supermart</option>
                    <option value="others">Others</option>
                </select>
            </div>
            <input type="text" name="item_name" placeholder="Item Name (e.g. Milk)" class="w-full border rounded p-2" required>
            <input type="text" name="qty" placeholder="Default Qty (e.g. 1L)" class="w-full border rounded p-2" required>
            <input type="number" name="estimated_price" placeholder="Est. Price" class="w-full border rounded p-2">
            <button type="submit" class="w-full bg-slate-800 text-white py-2 rounded">Add Template</button>
        </form>
    </div>

    <!-- List -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-500 font-medium">
                <tr>
                    <th class="px-6 py-3">Category</th>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Qty</th>
                    <th class="px-6 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($templates as $t)
                <tr>
                    <td class="px-6 py-3">
                        <span class="text-xs font-bold uppercase px-2 py-1 rounded bg-slate-100 text-slate-600">
                            {{ $t->type ?? 'Today' }}
                        </span>
                    </td>
                    <td class="px-6 py-3 font-medium">{{ $t->item_name }}</td>
                    <td class="px-6 py-3">{{ $t->qty }}</td>
                    <td class="px-6 py-3 text-right flex gap-2 justify-end">
                        <a href="{{ route('grocery.templates.edit', $t->id) }}" class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded hover:bg-yellow-200 font-bold">Edit</a>
                        <form action="{{ route('grocery.templates.destroy', $t->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded hover:bg-red-200 font-bold">Remove</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection