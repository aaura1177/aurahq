<div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
    <div class="flex justify-between items-center mb-4">
        <h3 class="font-bold text-lg text-slate-800">{{ $title }} Summary</h3>
        <span class="text-2xl font-bold text-red-600">Total: ₹{{ number_format($data['total'], 2) }}</span>
    </div>

    @if($data['items']->count() > 0 || $data['expenses']->count() > 0)
        <h4 class="text-xs font-bold text-slate-500 uppercase mb-2">Purchased Items</h4>
        <div class="overflow-x-auto mb-6">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-4 py-2">Item</th>
                        <th class="px-4 py-2">Type</th>
                        <th class="px-4 py-2">Qty</th>
                        <th class="px-4 py-2 text-right">Cost</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($data['items'] as $item)
                    <tr>
                        <td class="px-4 py-2">{{ $item->item_name }}</td>
                        <td class="px-4 py-2"><span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded text-xs uppercase">{{ $item->type }}</span></td>
                        <td class="px-4 py-2">{{ $item->qty }}</td>
                        <td class="px-4 py-2 text-right font-bold">₹{{ number_format($item->actual_cost, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($data['expenses']->count() > 0)
        <h4 class="text-xs font-bold text-slate-500 uppercase mb-2">Variable Expenses</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-4 py-2">Description</th>
                        <th class="px-4 py-2">Date</th>
                        <th class="px-4 py-2 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($data['expenses'] as $exp)
                    <tr>
                        <td class="px-4 py-2">{{ $exp->remark }}</td>
                        <td class="px-4 py-2 text-slate-500">{{ $exp->date->format('M d') }}</td>
                        <td class="px-4 py-2 text-right font-bold text-red-600">₹{{ number_format($exp->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    @else
        <p class="text-center text-slate-400 py-4">No records found for this period.</p>
    @endif
</div>
