@extends('layouts.admin')
@section('title', 'Dashboard')
@section('header', 'Overview')

@section('content')
@role('super-admin')
<div class="space-y-6">
    {{-- Daily report missing (IST) --}}
    @if((count($morningReportMissing ?? []) > 0) || (count($eveningReportMissing ?? []) > 0))
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @if(count($morningReportMissing ?? []) > 0)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
            <h4 class="font-bold text-amber-800 mb-2">Morning report not submitted (by 11:00 AM IST)</h4>
            <p class="text-sm text-amber-700 mb-2">These employees did not submit their morning report for today. Reminder emails have been sent at 11:00 AM IST.</p>
            <ul class="text-sm text-amber-800 space-y-1">
                @foreach($morningReportMissing as $u)
                <li>{{ $u->name }} @if($u->email)<span class="text-amber-600">({{ $u->email }})</span>@endif</li>
                @endforeach
            </ul>
            <a href="{{ route('daily-reports.index') }}" class="inline-block mt-3 text-sm font-medium text-amber-800 hover:underline">View daily reports →</a>
        </div>
        @endif
        @if(count($eveningReportMissing ?? []) > 0)
        <div class="bg-orange-50 border border-orange-200 rounded-xl p-4">
            <h4 class="font-bold text-orange-800 mb-2">Evening report not submitted (by 5:15 PM IST)</h4>
            <p class="text-sm text-orange-700 mb-2">These employees (present today) did not submit their evening report. Reminder emails sent at 5:15 PM IST.</p>
            <ul class="text-sm text-orange-800 space-y-1">
                @foreach($eveningReportMissing as $u)
                <li>{{ $u->name }} @if($u->email)<span class="text-orange-600">({{ $u->email }})</span>@endif</li>
                @endforeach
            </ul>
            <a href="{{ route('daily-reports.index') }}" class="inline-block mt-3 text-sm font-medium text-orange-800 hover:underline">View daily reports →</a>
        </div>
        @endif
    </div>
    @endif

    <!-- Top Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <a href="{{ route('finance.index') }}" class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase">Total Spending (Given)</p>
                    <h3 class="text-2xl font-bold text-red-600 mt-1">₹{{ number_format($totalSpending, 2) }}</h3>
                </div>
                <div class="p-2 bg-red-50 text-red-600 rounded-lg group-hover:bg-red-100 transition"><i class="fas fa-arrow-up"></i></div>
            </div>
        </a>

        <a href="{{ route('finance.index') }}" class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase">Total Received (Income)</p>
                    <h3 class="text-2xl font-bold text-green-600 mt-1">₹{{ number_format($totalReceived, 2) }}</h3>
                </div>
                <div class="p-2 bg-green-50 text-green-600 rounded-lg group-hover:bg-green-100 transition"><i class="fas fa-arrow-down"></i></div>
            </div>
        </a>

        <a href="{{ route('finance.index') }}" class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase">Net Balance</p>
                    <h3 class="text-2xl font-bold {{ $netBalance >= 0 ? 'text-slate-800' : 'text-red-600' }} mt-1">
                        ₹{{ number_format($netBalance, 2) }}
                    </h3>
                </div>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg group-hover:bg-blue-100 transition"><i class="fas fa-wallet"></i></div>
            </div>
        </a>

        <a href="{{ route('tasks.index') }}" class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase">Pending Tasks</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $pendingTasks }}</h3>
                </div>
                <div class="p-2 bg-purple-50 text-purple-600 rounded-lg group-hover:bg-purple-100 transition"><i class="fas fa-tasks"></i></div>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Weekly Chart -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-slate-100">
            <h4 class="font-bold text-slate-700 mb-4">Weekly Financial Overview</h4>
            <div class="relative h-[300px] w-full" style="height: 300px;"> <!-- Added inline style -->
                <canvas id="weeklyChart"></canvas>
            </div>
        </div>

        <!-- Recent Activity Feed -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
            <h4 class="font-bold text-slate-700 mb-4">Recent Transactions</h4>
            <div class="space-y-4">
                @forelse($recentTransactions as $transaction)
                <div class="flex items-center justify-between pb-4 border-b border-slate-50 last:border-0 last:pb-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $transaction->type == 'given' ? 'bg-red-50 text-red-500' : 'bg-green-50 text-green-500' }}">
                            <i class="fas {{ $transaction->type == 'given' ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-700">{{ $transaction->contact->name }}</p>
                            <p class="text-xs text-slate-400">{{ $transaction->transaction_date->diffForHumans() }}</p>
                        </div>
                    </div>
                    <span class="font-bold text-sm {{ $transaction->type == 'given' ? 'text-red-600' : 'text-green-600' }}">
                        {{ $transaction->type == 'given' ? '-' : '+' }} ₹{{ number_format($transaction->amount, 0) }}
                    </span>
                </div>
                @empty
                <p class="text-sm text-slate-400 text-center py-4">No recent activity</p>
                @endforelse
            </div>
            <div class="mt-6 pt-4 border-t border-slate-50 text-center">
                <a href="{{ route('finance.index') }}" class="text-sm text-blue-600 font-medium hover:underline">View All Transactions</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Chart === 'undefined') {
            console.error('Chart.js library is not loaded. Please check your network or layout file.');
            return;
        }

        const canvas = document.getElementById('weeklyChart');
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [
                    {
                        label: 'Income',
                        data: @json($incomeData),
                        backgroundColor: '#22c55e',
                        borderRadius: 4,
                        barPercentage: 0.6,
                        categoryPercentage: 0.8
                    },
                    {
                        label: 'Expense',
                        data: @json($expenseData),
                        backgroundColor: '#ef4444',
                        borderRadius: 4,
                        barPercentage: 0.6,
                        categoryPercentage: 0.8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: { usePointStyle: true, boxWidth: 8 }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [2, 2], drawBorder: false },
                        ticks: { font: { size: 11 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    });
</script>
@else
   <div class="bg-white p-8 rounded-xl shadow-sm border border-slate-100 text-center mt-10">
        <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
            <i class="fas fa-user-shield"></i>
        </div>
        <h2 class="text-2xl font-bold text-slate-800 mb-2">Welcome, {{ Auth::user()->name }}!</h2>
        <p class="text-slate-500">Please use the sidebar to access your assigned modules.</p>
   </div>
@endrole
@endsection
