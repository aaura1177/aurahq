@extends('layouts.admin')
@section('title', 'P&L Statement')
@section('header', 'P&L Statement')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex flex-wrap justify-between items-center gap-4 print:hidden">
        <form method="get" action="{{ route('finance.pnl') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Month</label>
                <input type="month" name="month" value="{{ $monthParam }}" class="border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Venture</label>
                <select name="venture" class="border rounded-lg px-3 py-2 text-sm">
                    <option value="all" {{ $venture === 'all' ? 'selected' : '' }}>All</option>
                    @foreach($ventures as $v)
                        <option value="{{ $v }}" {{ $venture === $v ? 'selected' : '' }}>{{ \App\Models\Finance::ventureLabel($v) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-semibold">Apply</button>
        </form>
        <button type="button" onclick="window.print()" class="text-sm bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700">Print</button>
    </div>

    <div class="bg-white p-8 rounded-xl border border-slate-200 shadow-sm print:shadow-none print:border-0">
        <p class="text-sm text-slate-500">{{ $monthStart->format('F Y') }}
            @if($venture !== 'all')
                — {{ \App\Models\Finance::ventureLabel($venture) }}
            @else
                — All ventures
            @endif
        </p>
        <h1 class="text-2xl font-bold text-slate-900 mt-2 mb-8">Income statement</h1>

        <h2 class="text-sm font-bold text-slate-500 uppercase tracking-wide mb-3">Revenue</h2>
        <div class="space-y-2 mb-6">
            @foreach($incomeLines as $line)
                <div class="flex justify-between text-sm">
                    <span class="text-slate-700">{{ $line['label'] }}</span>
                    <span class="font-medium">₹{{ number_format($line['amount'], 2) }}</span>
                </div>
            @endforeach
            @if(count($incomeLines) === 0)
                <p class="text-sm text-slate-400">—</p>
            @endif
            <div class="flex justify-between border-t border-slate-200 pt-3 font-bold text-green-700">
                <span>Total revenue</span>
                <span>₹{{ number_format($totalRevenue, 2) }}</span>
            </div>
        </div>

        <h2 class="text-sm font-bold text-slate-500 uppercase tracking-wide mb-3">Expenses</h2>
        <div class="space-y-2 mb-6">
            @foreach($expenseLines as $line)
                <div class="flex justify-between text-sm">
                    <span class="text-slate-700">{{ $line['label'] }}</span>
                    <span class="font-medium">₹{{ number_format($line['amount'], 2) }}</span>
                </div>
            @endforeach
            @if(count($expenseLines) === 0)
                <p class="text-sm text-slate-400">—</p>
            @endif
            <div class="flex justify-between border-t border-slate-200 pt-3 font-bold text-red-700">
                <span>Total expenses</span>
                <span>₹{{ number_format($totalExpenses, 2) }}</span>
            </div>
        </div>

        <div class="flex justify-between text-lg font-bold border-t-2 border-slate-300 pt-4 {{ $netProfit >= 0 ? 'text-green-700' : 'text-red-700' }}">
            <span>Net profit / (loss)</span>
            <span>₹{{ number_format($netProfit, 2) }}</span>
        </div>
    </div>
</div>
@endsection
