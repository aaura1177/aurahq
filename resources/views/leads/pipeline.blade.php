@extends('layouts.admin')
@section('title', 'Pipeline')
@section('header', 'Sales Pipeline')

@section('content')
<div class="space-y-6"
     id="pipeline-board"
     data-csrf="{{ csrf_token() }}"
     data-can-edit="{{ auth()->user()->can('edit leads') ? '1' : '0' }}">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-slate-600">Open pipeline value (excl. won/lost): <strong class="text-slate-900">₹{{ number_format($pipelineValue, 0) }}</strong></p>
            <p class="text-sm text-slate-600">Open leads: <strong class="text-slate-900">{{ $openCount }}</strong></p>
            @can('edit leads')
            <p class="text-xs text-slate-400 mt-1">Drag cards between columns to change stage</p>
            @endcan
        </div>
        <div class="flex gap-2">
            <a href="{{ route('leads.index') }}" class="text-sm px-3 py-1.5 border border-slate-200 rounded-lg hover:bg-white bg-slate-50">List view</a>
            @can('create leads')
            <a href="{{ route('leads.create') }}" class="hq-btn hq-btn-primary">Add Lead</a>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
        @foreach($pipelineStages as $stage)
        <div class="pipeline-col bg-slate-100/80 rounded-xl border border-slate-200 p-3 min-h-[200px]"
             data-stage="{{ $stage }}">
            <div class="mb-3 pb-2 border-b border-slate-200">
                <h3 class="font-bold text-slate-800 text-sm">{{ str_replace('_', ' ', ucfirst($stage)) }}</h3>
                <p class="text-xs text-slate-500 col-meta">{{ $stageStats[$stage]['count'] }} · ₹{{ number_format($stageStats[$stage]['value'], 0) }}</p>
            </div>
            <div class="space-y-2 min-h-[120px] drop-zone">
                @foreach($grouped->get($stage, collect()) as $lead)
                <div class="lead-card bg-white p-3 rounded-lg border border-slate-200 shadow-sm text-sm cursor-grab active:cursor-grabbing"
                     draggable="{{ auth()->user()->can('edit leads') ? 'true' : 'false' }}"
                     data-id="{{ $lead->id }}"
                     data-stage-url="{{ route('leads.stage', $lead) }}"
                     data-value="{{ (float) ($lead->estimated_value ?? 0) }}">
                    <a href="{{ route('leads.show', $lead) }}" class="font-bold text-slate-800 hover:text-blue-600 block" onclick="event.stopPropagation()">{{ $lead->business_name }}</a>
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
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($closedStages as $stage)
        <div class="pipeline-col bg-white rounded-xl border border-slate-200 p-4"
             data-stage="{{ $stage }}">
            <h3 class="font-bold text-slate-800 mb-2">{{ str_replace('_', ' ', ucfirst($stage)) }}</h3>
            <p class="text-xs text-slate-500 mb-3 col-meta">{{ $stageStats[$stage]['count'] }} leads · ₹{{ number_format($stageStats[$stage]['value'], 0) }}</p>
            <div class="space-y-2 max-h-64 overflow-y-auto drop-zone min-h-[48px]">
                @foreach($grouped->get($stage, collect()) as $lead)
                <div class="lead-card flex justify-between items-center text-sm border border-slate-100 rounded-lg p-2 cursor-grab active:cursor-grabbing"
                     draggable="{{ auth()->user()->can('edit leads') ? 'true' : 'false' }}"
                     data-id="{{ $lead->id }}"
                     data-stage-url="{{ route('leads.stage', $lead) }}"
                     data-value="{{ (float) ($lead->estimated_value ?? 0) }}">
                    <a href="{{ route('leads.show', $lead) }}" class="font-medium text-blue-600 hover:underline" onclick="event.stopPropagation()">{{ $lead->business_name }}</a>
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

@can('edit leads')
<script>
(function () {
    const board = document.getElementById('pipeline-board');
    if (!board || board.dataset.canEdit !== '1') return;
    const csrf = board.dataset.csrf;
    let dragCard = null;

    board.querySelectorAll('.lead-card').forEach(function (card) {
        card.addEventListener('dragstart', function (e) {
            dragCard = card;
            card.classList.add('opacity-50');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', card.dataset.id);
        });
        card.addEventListener('dragend', function () {
            card.classList.remove('opacity-50');
            board.querySelectorAll('.pipeline-col').forEach(function (c) {
                c.classList.remove('ring-2', 'ring-blue-300');
            });
            dragCard = null;
        });
    });

    board.querySelectorAll('.pipeline-col').forEach(function (col) {
        const zone = col.querySelector('.drop-zone');
        if (!zone) return;

        col.addEventListener('dragover', function (e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            col.classList.add('ring-2', 'ring-blue-300');
        });
        col.addEventListener('dragleave', function () {
            col.classList.remove('ring-2', 'ring-blue-300');
        });
        col.addEventListener('drop', async function (e) {
            e.preventDefault();
            col.classList.remove('ring-2', 'ring-blue-300');
            if (!dragCard) return;

            const newStage = col.dataset.stage;
            const url = dragCard.dataset.stageUrl;
            const body = { stage: newStage };

            if (newStage === 'lost') {
                const reason = window.prompt('Lost reason (required):');
                if (!reason || !reason.trim()) return;
                body.lost_reason = reason.trim();
            }

            zone.appendChild(dragCard);

            try {
                const res = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(body),
                });
                if (!res.ok) {
                    window.location.reload();
                    return;
                }
                // light refresh of counts
                window.location.reload();
            } catch (err) {
                console.error(err);
                window.location.reload();
            }
        });
    });
})();
</script>
@endcan
@endsection
