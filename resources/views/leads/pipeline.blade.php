@extends('layouts.admin')
@section('title', 'Pipeline')
@section('header', 'Sales Pipeline')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-slate-600">Open pipeline value (excl. won/lost): <strong class="text-slate-900">₹{{ number_format($pipelineValue, 0) }}</strong></p>
            <p class="text-sm text-slate-600">Open leads: <strong class="text-slate-900">{{ $openCount }}</strong></p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('leads.index') }}" class="text-sm px-4 py-2 border border-slate-200 rounded-lg hover:bg-white bg-slate-50">List view</a>
            @can('create leads')
            <a href="{{ route('leads.create') }}" class="text-sm px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">Add Lead</a>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
        @foreach($pipelineStages as $stage)
        <div class="bg-slate-100/80 rounded-xl border border-slate-200 p-3 min-h-[200px]">
            <div class="mb-3 pb-2 border-b border-slate-200">
                <h3 class="font-bold text-slate-800 text-sm">{{ str_replace('_', ' ', ucfirst($stage)) }}</h3>
                <p class="text-xs text-slate-500">{{ $stageStats[$stage]['count'] }} · ₹{{ number_format($stageStats[$stage]['value'], 0) }}</p>
            </div>
            <div class="space-y-2">
                @foreach($grouped->get($stage, collect()) as $lead)
                <div class="bg-white p-3 rounded-lg border border-slate-200 shadow-sm text-sm">
                    <a href="{{ route('leads.show', $lead) }}" class="font-bold text-slate-800 hover:text-blue-600 block">{{ $lead->business_name }}</a>
                    @if($lead->estimated_value)
                    <p class="text-green-700 font-medium mt-1">₹{{ number_format($lead->estimated_value, 0) }}</p>
                    @endif
                    @if($lead->next_follow_up)
                    <p class="text-xs mt-1 {{ $lead->isOverdue() ? 'text-red-600 font-semibold' : 'text-slate-500' }}">
                        {{ $lead->next_follow_up->format('M j') }}
                    </p>
                    @endif
                    @if($lead->assignee)
                    <p class="text-xs text-slate-500 mt-1" title="{{ $lead->assignee->name }}">{{ strtoupper(substr($lead->assignee->name, 0, 1)) }}</p>
                    @endif
                    @can('edit leads')
                    <form action="{{ route('leads.stage', $lead) }}" method="POST" class="mt-2">
                        @csrf
                        @method('PATCH')
                        <label class="text-[10px] text-slate-400 uppercase font-bold">Move to</label>
                        <select name="stage" class="w-full text-xs border rounded p-1 mt-0.5 bg-white" onchange="this.form.submit()">
                            @foreach($stages as $st)
                            <option value="{{ $st }}" {{ $lead->stage === $st ? 'selected' : '' }}>{{ str_replace('_', ' ', ucfirst($st)) }}</option>
                            @endforeach
                        </select>
                    </form>
                    @endcan
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($closedStages as $stage)
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <h3 class="font-bold text-slate-800 mb-2">{{ str_replace('_', ' ', ucfirst($stage)) }}</h3>
            <p class="text-xs text-slate-500 mb-3">{{ $stageStats[$stage]['count'] }} leads · ₹{{ number_format($stageStats[$stage]['value'], 0) }}</p>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @foreach($grouped->get($stage, collect()) as $lead)
                <div class="flex justify-between items-center text-sm border border-slate-100 rounded-lg p-2">
                    <a href="{{ route('leads.show', $lead) }}" class="font-medium text-blue-600 hover:underline">{{ $lead->business_name }}</a>
                    @if($lead->estimated_value)
                    <span class="text-slate-600">₹{{ number_format($lead->estimated_value, 0) }}</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
