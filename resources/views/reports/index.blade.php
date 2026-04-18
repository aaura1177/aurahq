@extends('layouts.admin')
@section('header', 'Detailed Financial Statement')
@section('content')

<!-- Custom Date Filter -->
<div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 mb-6">
    <form action="{{ route('reports.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
        <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">Start Date</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">End Date</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="border rounded px-3 py-2 text-sm">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm font-bold hover:bg-blue-700">Apply Filter</button>
        @if(request('start_date'))
            <a href="{{ route('reports.index') }}" class="text-red-500 text-sm hover:underline">Clear</a>
        @endif
    </form>
</div>

<!-- Tabs for Day/Week/Month -->
<div x-data="{ tab: 'month' }" class="space-y-6">
    
    <div class="flex border-b border-slate-200">
        <button @click="tab = 'day'" :class="{ 'border-blue-500 text-blue-600': tab === 'day', 'border-transparent text-slate-500 hover:text-slate-700': tab !== 'day' }" class="px-6 py-3 border-b-2 font-medium text-sm transition">Daily View</button>
        <button @click="tab = 'week'" :class="{ 'border-blue-500 text-blue-600': tab === 'week', 'border-transparent text-slate-500 hover:text-slate-700': tab !== 'week' }" class="px-6 py-3 border-b-2 font-medium text-sm transition">Weekly View</button>
        <button @click="tab = 'month'" :class="{ 'border-blue-500 text-blue-600': tab === 'month', 'border-transparent text-slate-500 hover:text-slate-700': tab !== 'month' }" class="px-6 py-3 border-b-2 font-medium text-sm transition">Monthly View</button>
        @if($custom)
        <button @click="tab = 'custom'" :class="{ 'border-blue-500 text-blue-600': tab === 'custom', 'border-transparent text-slate-500 hover:text-slate-700': tab !== 'custom' }" class="px-6 py-3 border-b-2 font-medium text-sm transition">Custom Range</button>
        @endif
    </div>

    <!-- DAILY -->
    <div x-show="tab === 'day'" class="space-y-6">
        @include('reports.partials.summary', ['data' => $daily, 'title' => 'Today'])
    </div>

    <!-- WEEKLY -->
    <div x-show="tab === 'week'" class="space-y-6">
         @include('reports.partials.summary', ['data' => $weekly, 'title' => 'This Week'])
    </div>

    <!-- MONTHLY -->
    <div x-show="tab === 'month'" class="space-y-6">
         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Pie Chart -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
                <h4 class="font-bold text-slate-700 mb-4">Category Breakdown (Month)</h4>
                <div class="h-[250px]" style="height: 250px;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
             <!-- Trend Chart -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
                <h4 class="font-bold text-slate-700 mb-4">Daily Spend Trend (7 Days)</h4>
                <div class="h-[250px]" style="height: 250px;">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>
         </div>
         @include('reports.partials.summary', ['data' => $monthly, 'title' => 'This Month'])
    </div>

    <!-- CUSTOM -->
    @if($custom)
    <div x-show="tab === 'custom'" class="space-y-6">
         @include('reports.partials.summary', ['data' => $custom, 'title' => 'Custom Range'])
    </div>
    @endif
</div>

<!-- AlpineJS for Tabs -->
<script src="//[unpkg.com/alpinejs](https://unpkg.com/alpinejs)" defer></script>

<!-- Chart Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') return;

    // Category Pie Chart
    new Chart(document.getElementById('categoryChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: @json($catLabels),
            datasets: [{
                data: @json($catData),
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                borderWidth: 0
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // Daily Trend
    new Chart(document.getElementById('dailyChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: @json($trendLabels),
            datasets: [{
                label: 'Expense (₹)',
                data: @json($trendData),
                backgroundColor: '#ef4444',
                borderRadius: 4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
});
</script>
@endsection
