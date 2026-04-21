@extends('layouts.admin')
@section('title', 'Dashboard')
@section('header', 'Command Center')

@section('content')
@role('super-admin')
<div class="space-y-6">
    {{-- ROW 1 — Revenue metrics --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Monthly Revenue</p>
                    <h3 class="text-2xl font-bold text-green-600 mt-1">₹{{ number_format($monthlyRevenue, 0) }}</h3>
                    <p class="text-sm mt-2 flex items-center gap-1 {{ $revenueChange >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        <i class="fas fa-arrow-{{ $revenueChange >= 0 ? 'up' : 'down' }}"></i>
                        {{ $revenueChange >= 0 ? '+' : '' }}{{ $revenueChange }}% vs last month
                    </p>
                </div>
                <div class="p-2 bg-green-50 text-green-600 rounded-lg"><i class="fas fa-arrow-trend-up"></i></div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Monthly Expenses</p>
                    <h3 class="text-2xl font-bold text-red-600 mt-1">₹{{ number_format($monthlyExpenses, 0) }}</h3>
                    <p class="text-sm text-slate-500 mt-2">This month</p>
                </div>
                <div class="p-2 bg-red-50 text-red-600 rounded-lg"><i class="fas fa-arrow-trend-down"></i></div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Monthly Profit / Loss</p>
                    <h3 class="text-2xl font-bold mt-1 {{ $monthlyProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        ₹{{ number_format($monthlyProfit, 0) }}
                    </h3>
                    <p class="text-sm text-slate-500 mt-2">{{ $monthlyProfit >= 0 ? 'Profit' : 'Loss' }} this month</p>
                </div>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg"><i class="fas fa-wallet"></i></div>
            </div>
        </div>

        @php
            $targetBarClass = $targetProgress >= 75 ? 'bg-green-500' : ($targetProgress >= 40 ? 'bg-amber-500' : 'bg-red-500');
            $targetTextClass = $targetProgress >= 75 ? 'text-green-700' : ($targetProgress >= 40 ? 'text-amber-700' : 'text-red-700');
        @endphp
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Revenue Target</p>
                    <p class="text-sm font-semibold text-slate-700 mt-2">₹{{ number_format($monthlyRevenue, 0) }} / ₹{{ number_format($targetAmount, 0) }}</p>
                    <p class="text-sm font-bold {{ $targetTextClass }} mt-1">{{ $targetProgress }}%</p>
                    <div class="mt-3 h-2.5 w-full rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full {{ $targetBarClass }}" style="width: {{ $targetProgress }}%"></div>
                    </div>
                </div>
                <div class="p-2 bg-slate-50 text-slate-600 rounded-lg shrink-0 ml-2"><i class="fas fa-bullseye"></i></div>
            </div>
        </div>
    </div>

    {{-- ROW 2 — CRM / clients / invoicing --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Pipeline Value</p>
                    <h3 class="text-2xl font-bold text-purple-600 mt-1">₹{{ number_format((float) $pipelineValue, 0) }}</h3>
                    <p class="text-sm text-slate-500 mt-2">Open leads (excl. won / lost)</p>
                </div>
                <div class="p-2 bg-purple-50 text-purple-600 rounded-lg"><i class="fas fa-funnel-dollar"></i></div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Active Clients</p>
                    <h3 class="text-2xl font-bold text-cyan-600 mt-1">{{ number_format($activeClientsCount) }}</h3>
                    <p class="text-sm text-slate-500 mt-2">Clients marked active</p>
                </div>
                <div class="p-2 bg-cyan-50 text-cyan-600 rounded-lg"><i class="fas fa-handshake"></i></div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Pending Invoices</p>
                    <h3 class="text-2xl font-bold text-amber-600 mt-1">₹{{ number_format((float) $pendingInvoicesAmount, 0) }}</h3>
                    <p class="text-sm text-slate-500 mt-2">Sent + overdue (unpaid)</p>
                </div>
                <div class="p-2 bg-amber-50 text-amber-600 rounded-lg"><i class="fas fa-file-invoice"></i></div>
            </div>
        </div>
    </div>

    @if(isset($dashboardVentures) && $dashboardVentures->isNotEmpty())
    {{-- Venture health --}}
    <div>
        <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wide mb-3">Venture health</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($dashboardVentures as $v)
                <a href="{{ route('ventures.show', $v) }}" class="block bg-white p-5 rounded-xl shadow-sm border border-slate-100 hover:shadow-md hover:border-slate-200 transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 bg-slate-50" style="color: {{ $v->color }}">
                                <i class="fas {{ $v->icon }}"></i>
                            </span>
                            <span class="font-bold text-slate-900 truncate">{{ $v->name }}</span>
                        </div>
                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded shrink-0
                            @if($v->status === 'active') bg-green-100 text-green-800
                            @elseif($v->status === 'paused') bg-amber-100 text-amber-800
                            @else bg-blue-100 text-blue-800 @endif">{{ $v->status }}</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-3">
                        @if($v->lastUpdate)
                            Last update {{ $v->lastUpdate->created_at->diffForHumans() }}
                        @else
                            No updates yet
                        @endif
                    </p>
                    <p class="text-sm font-semibold text-slate-700 mt-2">{{ $v->open_projects_count }} open projects</p>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ROW 3 — Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-slate-100">
            <h4 class="font-bold text-slate-700 mb-4">Revenue vs Target — Last 6 Months</h4>
            <div class="relative w-full h-[280px]">
                <canvas id="sixMonthRevenueChart"></canvas>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
            <h4 class="font-bold text-slate-700 mb-4">Weekly Income &amp; Expenses</h4>
            <div class="relative w-full h-[280px]">
                <canvas id="weeklyChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ROW 4 — Action items --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('tasks.assignments') }}" class="block bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition {{ $tasksDueToday > 0 ? 'bg-amber-50 border-amber-200' : '' }}">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Tasks Due Today</p>
            <p class="text-3xl font-bold text-slate-800 mt-2">{{ $tasksDueToday }}</p>
            <p class="text-sm text-blue-600 font-medium mt-3">Open assignments →</p>
        </a>
        <a href="{{ route('tasks.assignments') }}" class="block bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition {{ $tasksOverdue > 0 ? 'bg-red-50 border-red-200' : '' }}">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Overdue Tasks</p>
            <p class="text-3xl font-bold {{ $tasksOverdue > 0 ? 'text-red-700' : 'text-slate-800' }} mt-2">{{ $tasksOverdue }}</p>
            <p class="text-sm text-blue-600 font-medium mt-3">Review assignments →</p>
        </a>
        <div class="space-y-4">
            @if((count($morningReportMissing ?? []) > 0) || (count($eveningReportMissing ?? []) > 0))
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
            @else
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 h-full flex flex-col justify-center">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Daily Report Compliance</p>
                    <p class="text-sm text-slate-600 mt-2">No outstanding compliance alerts for the current deadlines.</p>
                    <a href="{{ route('daily-reports.index') }}" class="inline-block mt-3 text-sm font-medium text-blue-600 hover:underline">View daily reports →</a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Chart === 'undefined') {
            console.error('Chart.js library is not loaded.');
            return;
        }

        const sixCanvas = document.getElementById('sixMonthRevenueChart');
        if (sixCanvas) {
            const sixCtx = sixCanvas.getContext('2d');
            new Chart(sixCtx, {
                type: 'bar',
                data: {
                    labels: @json($sixMonthLabels),
                    datasets: [
                        {
                            type: 'bar',
                            label: 'Revenue',
                            data: @json($sixMonthRevenue),
                            backgroundColor: '#22c55e',
                            borderRadius: 4,
                            barPercentage: 0.65,
                            categoryPercentage: 0.75,
                        },
                        {
                            type: 'line',
                            label: 'Target',
                            data: @json($sixMonthTarget),
                            borderColor: '#ef4444',
                            backgroundColor: 'transparent',
                            borderDash: [6, 6],
                            borderWidth: 2,
                            pointRadius: 3,
                            pointBackgroundColor: '#ef4444',
                            tension: 0.2,
                            fill: false,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: { usePointStyle: true, boxWidth: 8 },
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [2, 2], drawBorder: false },
                            ticks: { font: { size: 11 } },
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 } },
                        },
                    },
                },
            });
        }

        const weeklyCanvas = document.getElementById('weeklyChart');
        if (weeklyCanvas) {
            const wctx = weeklyCanvas.getContext('2d');
            new Chart(wctx, {
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
                            categoryPercentage: 0.8,
                        },
                        {
                            label: 'Expense',
                            data: @json($expenseData),
                            backgroundColor: '#ef4444',
                            borderRadius: 4,
                            barPercentage: 0.6,
                            categoryPercentage: 0.8,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: { usePointStyle: true, boxWidth: 8 },
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [2, 2], drawBorder: false },
                            ticks: { font: { size: 10 } },
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 } },
                        },
                    },
                },
            });
        }
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
