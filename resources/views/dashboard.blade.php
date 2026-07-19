@extends('layouts.admin')
@section('title', 'Dashboard')
@section('header', 'Command Center')

@section('content')
@role('super-admin')
<div class="space-y-6">

    @if(!empty($morningBrief))
    <section class="bg-slate-900 text-white rounded-2xl p-5 sm:p-6 border border-slate-800">
        <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Morning Brief</p>
                <h3 class="text-lg font-semibold mt-0.5">{{ \Carbon\Carbon::parse($morningBrief['date'])->format('l, F j') }}</h3>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('daily-focus.today') }}" class="px-3 py-1.5 rounded-lg bg-amber-500/20 text-amber-200 text-xs font-semibold hover:bg-amber-500/30">My Day {{ $morningBrief['my_day_filled'] ?? count($morningBrief['my_day_slots']) }}/3</a>
                <a href="{{ route('tasks.personal', ['filter' => 'all']) }}" class="px-3 py-1.5 rounded-lg bg-white/10 text-slate-200 text-xs font-semibold hover:bg-white/15">My Tasks</a>
                <a href="{{ route('leads.overdue') }}" class="px-3 py-1.5 rounded-lg bg-white/10 text-slate-200 text-xs font-semibold hover:bg-white/15">Follow-ups</a>
            </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
            <div class="rounded-xl bg-white/5 border border-white/10 p-3">
                <p class="text-[10px] uppercase tracking-wide text-slate-400">Priorities</p>
                <p class="text-2xl font-bold mt-1">{{ $morningBrief['counts']['priority'] }}</p>
            </div>
            <div class="rounded-xl bg-white/5 border border-white/10 p-3">
                <p class="text-[10px] uppercase tracking-wide text-slate-400">Due today</p>
                <p class="text-2xl font-bold mt-1">{{ $morningBrief['counts']['due_today'] }}</p>
            </div>
            <div class="rounded-xl {{ $morningBrief['counts']['overdue_tasks'] ? 'bg-red-500/15 border-red-400/30' : 'bg-white/5 border-white/10' }} border p-3">
                <p class="text-[10px] uppercase tracking-wide text-slate-400">Overdue tasks</p>
                <p class="text-2xl font-bold mt-1 {{ $morningBrief['counts']['overdue_tasks'] ? 'text-red-300' : '' }}">{{ $morningBrief['counts']['overdue_tasks'] }}</p>
            </div>
            <div class="rounded-xl {{ $morningBrief['counts']['overdue_leads'] ? 'bg-amber-500/15 border-amber-400/30' : 'bg-white/5 border-white/10' }} border p-3">
                <p class="text-[10px] uppercase tracking-wide text-slate-400">Lead follow-ups</p>
                <p class="text-2xl font-bold mt-1 {{ $morningBrief['counts']['overdue_leads'] ? 'text-amber-200' : '' }}">{{ $morningBrief['counts']['overdue_leads'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">My Day focuses</p>
                @forelse($morningBrief['my_day_slots'] as $slot)
                    <p class="text-sm py-1.5 border-b border-white/5 {{ $slot['done'] ? 'line-through text-slate-500' : 'text-slate-100' }}">
                        {{ $slot['slot'] }}. {{ $slot['title'] }}
                    </p>
                @empty
                    <p class="text-sm text-slate-400">No focuses set —
                        <a href="{{ route('daily-focus.today') }}" class="text-amber-300 hover:underline">open My Day</a>
                    </p>
                @endforelse
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Top personal work</p>
                @forelse($morningBrief['priority_tasks']->take(4) as $t)
                    <p class="text-sm py-1.5 border-b border-white/5 text-slate-200 truncate">{{ $t->title }}
                        <span class="text-slate-500 text-xs">· {{ str_replace('_', ' ', $t->frequency) }}</span>
                    </p>
                @empty
                    <p class="text-sm text-slate-400">No personal tasks —
                        <a href="{{ route('tasks.create', ['context' => 'top_five']) }}" class="text-amber-300 hover:underline">add one</a>
                    </p>
                @endforelse
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Needs attention</p>
                @forelse($morningBrief['overdue_leads']->take(3) as $l)
                    <a href="{{ route('leads.overdue') }}" class="block text-sm py-1.5 border-b border-white/5 text-amber-200 hover:text-amber-100 truncate">{{ $l->business_name }}</a>
                @empty
                    @if($morningBrief['overdue_tasks']->isNotEmpty())
                        @foreach($morningBrief['overdue_tasks']->take(3) as $t)
                            <p class="text-sm py-1.5 border-b border-white/5 text-red-300 truncate">{{ $t->title }}</p>
                        @endforeach
                    @else
                        <p class="text-sm text-slate-400">Nothing overdue. Clear runway.</p>
                    @endif
                @endforelse
            </div>
        </div>
    </section>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('finance.index') }}" class="block bg-white p-5 rounded-xl border border-slate-100 hover:border-green-200 hover:bg-green-50/30 transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Monthly Revenue</p>
                    <h3 class="text-2xl font-bold text-green-600 mt-1">₹{{ number_format($monthlyRevenue, 0) }}</h3>
                    @if($monthlyRevenue <= 0)
                        <p class="text-xs text-slate-500 mt-2">No receipts yet — <span class="text-green-700 font-medium">add a transaction</span></p>
                    @else
                        <p class="text-sm mt-2 flex items-center gap-1 {{ $revenueChange >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            <i class="fas fa-arrow-{{ $revenueChange >= 0 ? 'up' : 'down' }}"></i>
                            {{ $revenueChange >= 0 ? '+' : '' }}{{ $revenueChange }}% vs last month
                        </p>
                    @endif
                </div>
                <div class="p-2 bg-green-50 text-green-600 rounded-lg"><i class="fas fa-arrow-trend-up"></i></div>
            </div>
        </a>

        <a href="{{ route('finance.index') }}" class="block bg-white p-5 rounded-xl border border-slate-100 hover:border-red-200 hover:bg-red-50/20 transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Monthly Expenses</p>
                    <h3 class="text-2xl font-bold text-red-600 mt-1">₹{{ number_format($monthlyExpenses, 0) }}</h3>
                    <p class="text-xs text-slate-500 mt-2">{{ $monthlyExpenses <= 0 ? 'No expenses logged this month' : 'This month' }}</p>
                </div>
                <div class="p-2 bg-red-50 text-red-600 rounded-lg"><i class="fas fa-arrow-trend-down"></i></div>
            </div>
        </a>

        <a href="{{ route('finance.dashboard') }}" class="block bg-white p-5 rounded-xl border border-slate-100 hover:border-blue-200 hover:bg-blue-50/20 transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Monthly Profit / Loss</p>
                    <h3 class="text-2xl font-bold mt-1 {{ $monthlyProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        ₹{{ number_format($monthlyProfit, 0) }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-2">Open P&amp;L →</p>
                </div>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg"><i class="fas fa-wallet"></i></div>
            </div>
        </a>

        @php
            $targetBarClass = $targetProgress >= 75 ? 'bg-green-500' : ($targetProgress >= 40 ? 'bg-amber-500' : 'bg-red-500');
            $targetTextClass = $targetProgress >= 75 ? 'text-green-700' : ($targetProgress >= 40 ? 'text-amber-700' : 'text-red-700');
        @endphp
        <a href="{{ route('revenue-targets.index') }}" class="block bg-white p-5 rounded-xl border border-slate-100 hover:border-slate-300 transition">
            <div class="flex justify-between items-start">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Revenue Target</p>
                    <p class="text-sm font-semibold text-slate-700 mt-2">₹{{ number_format($monthlyRevenue, 0) }} / ₹{{ number_format($targetAmount, 0) }}</p>
                    <p class="text-sm font-bold {{ $targetTextClass }} mt-1">{{ $targetProgress }}%</p>
                    <div class="mt-3 h-2 w-full rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full {{ $targetBarClass }}" style="width: {{ $targetProgress }}%"></div>
                    </div>
                </div>
                <div class="p-2 bg-slate-50 text-slate-600 rounded-lg shrink-0 ml-2"><i class="fas fa-bullseye"></i></div>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('leads.pipeline') }}" class="block bg-white p-5 rounded-xl border border-slate-100 hover:border-purple-200 hover:bg-purple-50/20 transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Pipeline Value</p>
                    <h3 class="text-2xl font-bold text-purple-600 mt-1">₹{{ number_format((float) $pipelineValue, 0) }}</h3>
                    @if($pipelineValue <= 0)
                        <p class="text-xs text-slate-500 mt-2">Open pipeline is empty — <span class="text-purple-700 font-medium">add a lead</span></p>
                    @else
                        <p class="text-xs text-slate-500 mt-2">Open leads (excl. won / lost)</p>
                    @endif
                </div>
                <div class="p-2 bg-purple-50 text-purple-600 rounded-lg"><i class="fas fa-funnel-dollar"></i></div>
            </div>
        </a>
        <a href="{{ route('clients.index') }}" class="block bg-white p-5 rounded-xl border border-slate-100 hover:border-cyan-200 hover:bg-cyan-50/20 transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Active Clients</p>
                    <h3 class="text-2xl font-bold text-cyan-600 mt-1">{{ number_format($activeClientsCount) }}</h3>
                    <p class="text-xs text-slate-500 mt-2">{{ $activeClientsCount ? 'View clients →' : 'No active clients yet' }}</p>
                </div>
                <div class="p-2 bg-cyan-50 text-cyan-600 rounded-lg"><i class="fas fa-handshake"></i></div>
            </div>
        </a>
        <a href="{{ route('invoices.index') }}" class="block bg-white p-5 rounded-xl border border-slate-100 hover:border-amber-200 hover:bg-amber-50/20 transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Pending Invoices</p>
                    <h3 class="text-2xl font-bold text-amber-600 mt-1">₹{{ number_format((float) $pendingInvoicesAmount, 0) }}</h3>
                    @if($pendingInvoicesAmount <= 0)
                        <p class="text-xs text-slate-500 mt-2">Nothing unpaid — <span class="text-amber-700 font-medium">create invoice</span></p>
                    @else
                        <p class="text-xs text-slate-500 mt-2">Sent + overdue (unpaid)</p>
                    @endif
                </div>
                <div class="p-2 bg-amber-50 text-amber-600 rounded-lg"><i class="fas fa-file-invoice"></i></div>
            </div>
        </a>
    </div>

    @if(isset($dashboardVentures) && $dashboardVentures->isNotEmpty())
    <div>
        <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wide mb-3">Venture health</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($dashboardVentures as $v)
                <a href="{{ route('ventures.show', $v) }}" class="block bg-white p-5 rounded-xl border border-slate-100 hover:border-slate-200 transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 bg-slate-50" style="color: {{ $v->color }}">
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 bg-white p-5 rounded-xl border border-slate-100">
            <h4 class="font-bold text-slate-700 mb-4 text-sm">Revenue vs Target — Last 6 Months</h4>
            <div class="relative w-full h-[260px]">
                <canvas id="sixMonthRevenueChart"></canvas>
            </div>
        </div>
        <div class="bg-white p-5 rounded-xl border border-slate-100">
            <h4 class="font-bold text-slate-700 mb-4 text-sm">Weekly Income &amp; Expenses</h4>
            <div class="relative w-full h-[260px]">
                <canvas id="weeklyChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('tasks.personal', ['filter' => 'all']) }}" class="block bg-white p-5 rounded-xl border border-slate-100 hover:shadow-sm transition {{ $tasksDueToday > 0 ? 'bg-amber-50 border-amber-200' : '' }}">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Tasks Due Today</p>
            <p class="text-3xl font-bold text-slate-800 mt-2">{{ $tasksDueToday }}</p>
            <p class="text-sm text-blue-600 font-medium mt-3">Open My Tasks →</p>
        </a>
        <a href="{{ route('tasks.personal', ['filter' => 'all']) }}" class="block bg-white p-5 rounded-xl border border-slate-100 hover:shadow-sm transition {{ $tasksOverdue > 0 ? 'bg-red-50 border-red-200' : '' }}">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Overdue Tasks</p>
            <p class="text-3xl font-bold {{ $tasksOverdue > 0 ? 'text-red-700' : 'text-slate-800' }} mt-2">{{ $tasksOverdue }}</p>
            <p class="text-sm text-blue-600 font-medium mt-3">Review →</p>
        </a>
        <div class="space-y-4">
            @if((count($morningReportMissing ?? []) > 0) || (count($eveningReportMissing ?? []) > 0))
                @if(count($morningReportMissing ?? []) > 0)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <h4 class="font-bold text-amber-800 mb-2 text-sm">Morning report missing</h4>
                    <ul class="text-sm text-amber-800 space-y-1">
                        @foreach($morningReportMissing as $u)
                        <li>{{ $u->name }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('daily-reports.index') }}" class="inline-block mt-3 text-sm font-medium text-amber-800 hover:underline">View reports →</a>
                </div>
                @endif
                @if(count($eveningReportMissing ?? []) > 0)
                <div class="bg-orange-50 border border-orange-200 rounded-xl p-4">
                    <h4 class="font-bold text-orange-800 mb-2 text-sm">Evening report missing</h4>
                    <ul class="text-sm text-orange-800 space-y-1">
                        @foreach($eveningReportMissing as $u)
                        <li>{{ $u->name }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('daily-reports.index') }}" class="inline-block mt-3 text-sm font-medium text-orange-800 hover:underline">View reports →</a>
                </div>
                @endif
            @else
                <div class="bg-white p-5 rounded-xl border border-slate-100 h-full flex flex-col justify-center">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Daily Report Compliance</p>
                    <p class="text-sm text-slate-600 mt-2">No outstanding alerts.</p>
                    <a href="{{ route('daily-reports.index') }}" class="inline-block mt-3 text-sm font-medium text-blue-600 hover:underline">View reports →</a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Chart === 'undefined') return;

        const sixCanvas = document.getElementById('sixMonthRevenueChart');
        if (sixCanvas) {
            new Chart(sixCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($sixMonthLabels),
                    datasets: [
                        { type: 'bar', label: 'Revenue', data: @json($sixMonthRevenue), backgroundColor: '#22c55e', borderRadius: 4, barPercentage: 0.65, categoryPercentage: 0.75 },
                        { type: 'line', label: 'Target', data: @json($sixMonthTarget), borderColor: '#ef4444', backgroundColor: 'transparent', borderDash: [6, 6], borderWidth: 2, pointRadius: 3, pointBackgroundColor: '#ef4444', tension: 0.2, fill: false },
                    ],
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8 } } },
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [2, 2], drawBorder: false }, ticks: { font: { size: 11 } } },
                        x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                    },
                },
            });
        }

        const weeklyCanvas = document.getElementById('weeklyChart');
        if (weeklyCanvas) {
            new Chart(weeklyCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        { label: 'Income', data: @json($incomeData), backgroundColor: '#22c55e', borderRadius: 4, barPercentage: 0.6, categoryPercentage: 0.8 },
                        { label: 'Expense', data: @json($expenseData), backgroundColor: '#ef4444', borderRadius: 4, barPercentage: 0.6, categoryPercentage: 0.8 },
                    ],
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8 } } },
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [2, 2], drawBorder: false }, ticks: { font: { size: 10 } } },
                        x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                    },
                },
            });
        }
    });
</script>
@else
   <div class="bg-white p-8 rounded-xl border border-slate-100 text-center mt-10">
        <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
            <i class="fas fa-user-shield"></i>
        </div>
        <h2 class="text-2xl font-bold text-slate-800 mb-2">Welcome, {{ Auth::user()->name }}!</h2>
        <p class="text-slate-500">Use the sidebar — or press <kbd class="px-1.5 py-0.5 bg-slate-100 rounded text-xs">⌘K</kbd> — to jump anywhere.</p>
   </div>
@endrole
@endsection
