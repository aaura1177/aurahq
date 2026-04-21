@extends('layouts.admin')
@section('title', 'Monthly P&L')
@section('header', 'Financial Intelligence — Monthly P&L')

@section('content')
<div class="space-y-6">
    <form method="get" action="{{ route('finance.dashboard') }}" class="flex flex-wrap items-end gap-4 bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Month</label>
            <input type="month" name="month" value="{{ $monthParam }}" class="border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Venture</label>
            <select name="venture" class="border rounded-lg px-3 py-2 text-sm min-w-[160px]">
                <option value="all" {{ $venture === 'all' ? 'selected' : '' }}>All ventures</option>
                @foreach($ventures as $v)
                    <option value="{{ $v }}" {{ $venture === $v ? 'selected' : '' }}>{{ \App\Models\Finance::ventureLabel($v) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-900">Apply</button>
        <a href="{{ route('finance.pnl', request()->query()) }}" target="_blank" class="text-sm text-blue-600 font-semibold hover:underline ml-auto">Detailed P&amp;L (print)</a>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
            <p class="text-xs font-bold text-slate-400 uppercase">Revenue</p>
            <h3 class="text-2xl font-bold text-green-600 mt-1">₹{{ number_format($revenue, 0) }}</h3>
            <p class="text-sm text-slate-500 mt-2">vs prior month: ₹{{ number_format($prevRevenue, 0) }}
                @if($prevRevenue > 0)
                    <span class="{{ $revenue >= $prevRevenue ? 'text-green-600' : 'text-red-600' }}">
                        ({{ $revenue >= $prevRevenue ? '+' : '' }}{{ number_format((($revenue - $prevRevenue) / $prevRevenue) * 100, 1) }}%)
                    </span>
                @endif
            </p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
            <p class="text-xs font-bold text-slate-400 uppercase">Expenses</p>
            <h3 class="text-2xl font-bold text-red-600 mt-1">₹{{ number_format($expenses, 0) }}</h3>
            <p class="text-sm text-slate-500 mt-2">vs prior month: ₹{{ number_format($prevExpenses, 0) }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
            <p class="text-xs font-bold text-slate-400 uppercase">Profit / Loss</p>
            <h3 class="text-2xl font-bold mt-1 {{ $profit >= 0 ? 'text-green-600' : 'text-red-600' }}">₹{{ number_format($profit, 0) }}</h3>
            <p class="text-sm text-slate-500 mt-2">Prior month P&amp;L: ₹{{ number_format($prevProfit, 0) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
            <h3 class="font-bold text-slate-800 mb-4">Expense breakdown</h3>
            <div class="space-y-3">
                @forelse($expenseBreakdown as $row)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-slate-700">{{ $row['label'] }}</span>
                            <span class="font-semibold text-slate-900">₹{{ number_format($row['amount'], 0) }} <span class="text-slate-400 font-normal">({{ $row['pct'] }}%)</span></span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-red-500 rounded-full" style="width: {{ min(100, $row['pct']) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No expenses in this period.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
            <h3 class="font-bold text-slate-800 mb-4">Revenue by contact (received)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-500 border-b">
                            <th class="pb-2">Contact</th>
                            <th class="pb-2 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($revenueByContact as $r)
                            <tr class="border-b border-slate-50">
                                <td class="py-2 text-slate-800">{{ $r->name }}</td>
                                <td class="py-2 text-right font-semibold text-green-700">₹{{ number_format((float) $r->total, 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="py-4 text-slate-500">No received transactions this month.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
        <h3 class="font-bold text-slate-800 mb-4">Profit trend — last 6 months</h3>
        <div class="relative w-full h-[260px]">
            <canvas id="profitTrendChart"></canvas>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') return;
    const ctx = document.getElementById('profitTrendChart');
    if (!ctx) return;
    new Chart(ctx.getContext('2d'), {
        type: 'line',
        data: {
            labels: @json($sixMonthLabels),
            datasets: [{
                label: 'Net profit',
                data: @json($sixMonthProfit),
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                fill: true,
                tension: 0.25,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [2, 2] } },
                x: { grid: { display: false } },
            }
        }
    });
});
</script>
@endsection
