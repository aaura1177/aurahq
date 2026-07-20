@extends('layouts.admin')
@section('title', 'Today')
@section('header', 'Today')

@section('content')
@php
    $targetAmount = $monthTarget ? (float) $monthTarget->target_amount : null;
    $targetPct = ($targetAmount && $targetAmount > 0)
        ? min(round(($monthlyRevenue / $targetAmount) * 100, 1), 100)
        : null;
@endphp

<div class="space-y-6 max-w-6xl">
    <x-ui.page-header title="Today" :subtitle="now()->format('l, d M Y')">
        @role('super-admin')
            <x-ui.button variant="secondary" :href="route('daily-focus.today')">My Day</x-ui.button>
        @endrole
        <x-ui.button variant="primary" :href="route('tasks.personal', ['filter' => 'open'])">My Tasks</x-ui.button>
    </x-ui.page-header>

    <x-ui.card class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Road to ₹8.5 Cr · $1M by May 2027</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">
                    {{ \App\Support\RevenueProgress::inr($revMillion['actual']) }}
                    <span class="text-slate-400 text-lg">/ ₹8.5Cr</span>
                </p>
            </div>
            <div class="text-right">
                <p class="text-3xl font-bold text-brand-600">{{ $revMillion['pct'] }}%</p>
            </div>
        </div>
        <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
            <div class="bg-brand-600 h-3 rounded-full transition-all" style="width: {{ min($revMillion['pct'], 100) }}%"></div>
        </div>
        <div class="mt-4 grid grid-cols-3 gap-4 text-center pt-4 border-t border-slate-100">
            <div>
                <p class="text-xs text-slate-500">This Month Target</p>
                <p class="text-sm font-bold text-slate-800">{{ \App\Support\RevenueProgress::inr($revMonth['target']) }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Collected</p>
                <p class="text-sm font-bold text-green-600">{{ \App\Support\RevenueProgress::inr($revMonth['actual']) }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">{{ $revMonth['days_left'] }}d left · gap</p>
                <p class="text-sm font-bold text-amber-600">{{ \App\Support\RevenueProgress::inr($revMonth['gap']) }}</p>
            </div>
        </div>
    </x-ui.card>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <a href="{{ route('finance.create') }}" class="flex items-center justify-center gap-2 px-4 py-3 bg-white rounded-xl border border-slate-200 shadow-sm hover:border-brand-400 hover:shadow transition text-sm font-medium text-slate-700">
            <i class="fas fa-plus text-green-500"></i> Log Money
        </a>
        <a href="{{ route('leads.create') }}" class="flex items-center justify-center gap-2 px-4 py-3 bg-white rounded-xl border border-slate-200 shadow-sm hover:border-brand-400 hover:shadow transition text-sm font-medium text-slate-700">
            <i class="fas fa-user-plus text-brand-500"></i> Add Lead
        </a>
        <a href="{{ route('tasks.create') }}" class="flex items-center justify-center gap-2 px-4 py-3 bg-white rounded-xl border border-slate-200 shadow-sm hover:border-brand-400 hover:shadow transition text-sm font-medium text-slate-700">
            <i class="fas fa-circle-plus text-blue-500"></i> Add Task
        </a>
        <a href="{{ route('invoices.create') }}" class="flex items-center justify-center gap-2 px-4 py-3 bg-white rounded-xl border border-slate-200 shadow-sm hover:border-brand-400 hover:shadow transition text-sm font-medium text-slate-700">
            <i class="fas fa-file-invoice text-amber-500"></i> New Invoice
        </a>
    </div>

    {{-- Row 1: stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('finance.dashboard') }}" class="block hover:opacity-95 transition">
            @if($targetAmount)
                <x-ui.stat
                    label="This Month"
                    :value="'₹'.number_format($monthlyRevenue, 0)"
                    :trend="'₹'.number_format($targetAmount, 0).' target · '.$targetPct.'%'"
                    color="brand"
                    icon="fa-bullseye"
                />
            @else
                <x-ui.stat
                    label="This Month"
                    :value="'₹'.number_format($monthlyRevenue, 0)"
                    trend="Set a target"
                    color="brand"
                    icon="fa-bullseye"
                />
            @endif
        </a>

        <a href="{{ route('leads.pipeline') }}" class="block hover:opacity-95 transition">
            <x-ui.stat
                label="Pipeline"
                :value="$pipelineCount"
                :trend="'₹'.number_format($pipelineValue, 0).' open value'"
                color="brand"
                icon="fa-columns"
            />
        </a>

        <a href="{{ route('leads.overdue') }}" class="block hover:opacity-95 transition">
            <x-ui.stat
                label="To Chase"
                :value="$toChaseCount"
                :trend="$toChaseCount > 0 ? 'Follow-ups past due' : 'All clear'"
                :color="$toChaseCount > 0 ? 'red' : 'brand'"
                icon="fa-clock"
            />
        </a>

        <a href="{{ route('invoices.index') }}" class="block hover:opacity-95 transition">
            <x-ui.stat
                label="Unpaid"
                :value="'₹'.number_format($unpaidTotal, 0)"
                :trend="$unpaidCount.' invoice'.($unpaidCount === 1 ? '' : 's')"
                :color="$unpaidTotal > 0 ? 'amber' : 'brand'"
                icon="fa-file-invoice-dollar"
            />
        </a>
    </div>

    {{-- Row 2: priorities + leads --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <x-ui.card title="Top Priorities" padding="p-0">
            @if($todayFocus)
                <div class="px-5 py-3 border-b border-slate-100 bg-brand-50/50">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-xs font-semibold text-brand-700 uppercase tracking-wide">My Day</p>
                        <a href="{{ route('daily-focus.today') }}" class="text-xs font-medium text-brand-600 hover:underline">Open →</a>
                    </div>
                    <ul class="mt-2 space-y-1">
                        @foreach([1, 2, 3] as $slot)
                            @php
                                $title = $todayFocus->{"task_{$slot}_title"};
                                $done = $todayFocus->{"task_{$slot}_completed"};
                            @endphp
                            @if($title)
                                <li class="text-sm {{ $done ? 'line-through text-slate-400' : 'text-slate-700' }}">
                                    {{ $slot }}. {{ $title }}
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($topTasks->isEmpty())
                <x-ui.empty-state icon="fa-flag" title="No priorities set" message="Add a daily or top-five personal task.">
                    <x-ui.button variant="primary" :href="route('tasks.create', ['context' => 'top_five'])">Add priority</x-ui.button>
                </x-ui.empty-state>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach($topTasks as $task)
                        <li>
                            <a href="{{ route('tasks.show', $task) }}" class="flex items-center justify-between gap-3 px-5 py-3 hover:bg-slate-50 transition">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-slate-800 truncate">{{ $task->title }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ str_replace('_', ' ', $task->frequency) }} · {{ $task->status }}</p>
                                </div>
                                @php
                                    $badge = match($task->priority) {
                                        'critical' => 'danger',
                                        'urgent' => 'warning',
                                        default => 'slate',
                                    };
                                @endphp
                                <x-ui.badge :color="$badge">{{ $task->priority }}</x-ui.badge>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div class="px-5 py-3 border-t border-slate-100">
                    <a href="{{ route('tasks.personal', ['filter' => 'open']) }}" class="text-xs font-semibold text-brand-600 hover:underline">All my tasks →</a>
                </div>
            @endif
        </x-ui.card>

        <x-ui.card title="Leads to Chase" padding="p-0">
            @if($overdueLeads->isEmpty())
                <x-ui.empty-state icon="fa-check-circle" title="All caught up" message="No overdue follow-ups right now.">
                    <x-ui.button variant="secondary" :href="route('leads.pipeline')">Open pipeline</x-ui.button>
                </x-ui.empty-state>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach($overdueLeads as $lead)
                        @php
                            $days = (int) $lead->next_follow_up->diffInDays(now()->startOfDay());
                        @endphp
                        <li>
                            <a href="{{ route('leads.show', $lead) }}" class="flex items-center justify-between gap-3 px-5 py-3 hover:bg-slate-50 transition">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-slate-800 truncate">{{ $lead->business_name }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5 truncate">{{ $lead->contact_person ?? '—' }} · due {{ $lead->next_follow_up->format('M j') }}</p>
                                </div>
                                <x-ui.badge color="danger">{{ $days }}d overdue</x-ui.badge>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div class="px-5 py-3 border-t border-slate-100">
                    <a href="{{ route('leads.overdue') }}" class="text-xs font-semibold text-brand-600 hover:underline">All overdue →</a>
                </div>
            @endif
        </x-ui.card>
    </div>

    {{-- Row 3: report status --}}
    <x-ui.card title="Report Status">
        @if($reportStatus)
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <x-ui.badge color="success">Filed</x-ui.badge>
                    <p class="text-sm text-slate-700">Morning report filed for today.</p>
                </div>
                <x-ui.button variant="secondary" :href="route('daily-reports.index')">View reports</x-ui.button>
            </div>
        @else
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <x-ui.badge color="warning">Due</x-ui.badge>
                    <p class="text-sm text-slate-700">Today's morning report is not submitted yet.</p>
                </div>
                <x-ui.button variant="primary" :href="route('daily-reports.create')">File report</x-ui.button>
            </div>
        @endif
    </x-ui.card>
</div>
@endsection
