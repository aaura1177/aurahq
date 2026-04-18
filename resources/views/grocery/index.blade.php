@extends('layouts.admin')
@section('header', 'Grocery & Stock')
@section('content')
<div class="space-y-6">
    
    <!-- Top Filters -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 flex flex-wrap gap-4 justify-between items-center">
        <!-- Week Filter -->
        <form action="{{ route('grocery.index') }}" method="GET" class="flex items-center gap-2">
            <input type="hidden" name="type" value="{{ $type }}">
            <select name="week" onchange="this.form.submit()" class="border rounded-lg px-3 py-2 text-sm bg-slate-50">
                <option value="">All Weeks</option>
                @foreach($weeks as $w)
                    <option value="{{ $w['value'] }}" {{ $week == $w['value'] ? 'selected' : '' }}>{{ $w['label'] }}</option>
                @endforeach
            </select>
        </form>

        <!-- Actions -->
        <div class="flex gap-2">
            <a href="{{ route('reports.index') }}" class="bg-slate-100 text-slate-700 px-3 py-2 rounded-lg text-sm hover:bg-slate-200 border border-slate-200">Financial Reports</a>
            <a href="{{ route('grocery.templates') }}" class="bg-slate-700 text-white px-3 py-2 rounded-lg text-sm hover:bg-slate-800">Manage Defaults</a>
            
            @can('create grocery')
            @if($type == 'today')
                <a href="{{ route('grocery.create', ['type' => 'today']) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm shadow hover:bg-green-700">+ Add Custom Daily Item</a>
            @else
                <a href="{{ route('grocery.create', ['type' => $type]) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm shadow hover:bg-blue-700">+ Add Item</a>
            @endif
            @endcan
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <!-- Tabs -->
        <div class="flex border-b border-slate-200 bg-slate-50 overflow-x-auto">
            @foreach(['vegetables', 'blinkit', 'supermart', 'others', 'today'] as $tab)
            <a href="{{ route('grocery.index', ['type' => $tab, 'week' => $week]) }}" 
               class="px-6 py-3 border-b-2 font-medium text-sm transition capitalize whitespace-nowrap
               {{ $type == $tab ? 'border-blue-500 text-blue-600 bg-white' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
               {{ $tab }}
               @if($tab == 'today') <span class="ml-1 text-[10px] bg-red-100 text-red-600 px-1 rounded">Daily</span> @endif
            </a>
            @endforeach
        </div>

        <!-- Variable Expense Form (Only visible on Today tab) -->
        @if($type == 'today')
        <div class="p-4 bg-slate-50 border-b border-slate-200">
            <p class="text-sm font-bold text-slate-700 mb-2">Quick Variable Expense (Internal)</p>
            <form action="{{ route('grocery.variable') }}" method="POST" class="flex gap-2 items-center">
                @csrf
                <input type="text" name="remark" placeholder="Description (e.g. Rikshaw fare)" class="border rounded px-2 py-1 text-sm w-full" required>
                <input type="number" name="amount" placeholder="₹ Amount" class="border rounded px-2 py-1 text-sm w-32" required>
                <button type="submit" class="bg-slate-700 text-white px-3 py-1 rounded text-sm hover:bg-slate-800">Add</button>
            </form>
        </div>

        <!-- Variable Expenses List -->
        <div class="p-4 bg-slate-50/50 border-b border-slate-100">
            <h4 class="text-xs font-bold text-slate-600 uppercase mb-2">Variations / Extra Expenses</h4>
            @php 
                $expenses = \App\Models\GroceryExpense::whereDate('date', \Carbon\Carbon::today())->get(); 
            @endphp
            <div class="space-y-2">
                @foreach($expenses as $exp)
                <div class="flex justify-between items-center text-sm border-b border-slate-100 pb-1 last:border-0">
                    <span class="text-slate-700">{{ $exp->remark }}</span>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-800">₹{{ number_format($exp->amount, 2) }}</span>
                        @can('edit grocery expenses')
                        <a href="{{ route('grocery-expenses.edit', $exp->id) }}" class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded hover:bg-yellow-200">Edit</a>
                        <form action="{{ route('grocery-expenses.destroy', $exp->id) }}" method="POST" onsubmit="return confirm('Delete expense?');" class="inline">
                            @csrf @method('DELETE')
                            <button class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded hover:bg-red-200">Delete</button>
                        </form>
                        @endcan
                    </div>
                </div>
                @endforeach
                @if($expenses->isEmpty()) <p class="text-xs text-slate-400 italic">No extra expenses today.</p> @endif
            </div>
        </div>
        @endif

        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-500 font-medium border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">Item Name</th>
                    <th class="px-6 py-4">Qty</th>
                    <th class="px-6 py-4">Est. Price</th>
                    <th class="px-6 py-4">Actual Cost</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($groceryItems as $item)
                <tr class="hover:bg-slate-50 transition {{ !$item->is_active && $type != 'today' ? 'opacity-50' : '' }}">
                    <td class="px-6 py-4 font-medium text-slate-800">
                        {{ $item->item_name }}
                        @if($item->is_frequent) <i class="fas fa-sync text-blue-400 ml-1 text-xs" title="Frequent"></i> @endif
                    </td>
                    <td class="px-6 py-4">{{ $item->qty }}</td>
                    <td class="px-6 py-4">₹{{ number_format($item->estimated_price, 2) }}</td>
                    <td class="px-6 py-4 font-bold text-slate-700">
                        {{ $item->actual_cost ? '₹'.number_format($item->actual_cost, 2) : '-' }}
                    </td> 
                    <td class="px-6 py-4">
                        @if(!$item->is_active)
                             <span class="bg-slate-200 text-slate-600 px-2 py-1 rounded text-xs font-bold">Disabled</span>
                        @elseif($item->status == 'purchased')
                             <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">Purchased</span>
                        @else
                             <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs font-bold">Pending</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right flex justify-end gap-2 items-center">
                        <!-- Action Logic for ALL types -->
                        @if(!$item->is_active)
                            <form action="{{ route('grocery.toggle', $item->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200 font-bold">Activate</button>
                            </form>
                        @elseif($item->status == 'pending')
                            <form action="{{ route('grocery.purchase', $item->id) }}" method="POST" class="flex gap-1 items-center">
                                @csrf
                                <input type="number" name="actual_cost" placeholder="₹ Cost" class="w-16 px-1 py-0.5 border rounded text-xs" required value="{{ $item->estimated_price }}">
                                <button class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700 font-bold">Buy</button>
                            </form>
                        @elseif($item->status == 'purchased')
                            <form action="{{ route('grocery.pending', $item->id) }}" method="POST">
                                @csrf @method('POST')
                                <button class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded hover:bg-gray-300 font-bold" title="Undo Purchase">Undo</button>
                            </form>
                        @endif

                        @can('edit grocery')
                        <a href="{{ route('grocery.edit', $item->id) }}" class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded hover:bg-yellow-200 font-bold">Edit</a>
                        @endcan
                        
                        @can('delete grocery')
                        <form action="{{ route('grocery.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Delete?');" class="inline">
                            @csrf @method('DELETE')
                            <button class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded hover:bg-red-200 font-bold">Delete</button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-slate-50 font-bold text-slate-700">
                <tr>
                    <td colspan="2" class="px-6 py-4 text-right">Totals:</td>
                    <td class="px-6 py-4 text-blue-600">Est: ₹{{ number_format($totalEstimated, 2) }}</td>
                    <td class="px-6 py-4 text-green-600">Act: ₹{{ number_format($totalActual, 2) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
