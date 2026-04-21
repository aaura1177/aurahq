@extends('layouts.admin')
@section('title', $lead->business_name)
@section('header', 'Lead Details')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
            <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">{{ $lead->business_name }}</h1>
                    @php $c = $lead->stage_color; @endphp
                    <span class="inline-flex mt-2 px-3 py-1 rounded-full text-xs font-bold
                        @if($c==='slate') bg-slate-100 text-slate-700
                        @elseif($c==='blue') bg-blue-100 text-blue-700
                        @elseif($c==='cyan') bg-cyan-100 text-cyan-800
                        @elseif($c==='purple') bg-purple-100 text-purple-700
                        @elseif($c==='orange') bg-orange-100 text-orange-800
                        @elseif($c==='green') bg-green-100 text-green-700
                        @elseif($c==='red') bg-red-100 text-red-700
                        @endif">{{ $lead->stage_label }}</span>
                </div>
                <div class="flex gap-2">
                    @can('edit leads')
                    <a href="{{ route('leads.edit', $lead) }}" class="text-sm px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-900">Edit</a>
                    @endcan
                </div>
            </div>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                <div><dt class="text-slate-400 font-bold uppercase text-xs">Contact</dt><dd class="text-slate-800">{{ $lead->contact_person ?? '—' }}</dd></div>
                <div><dt class="text-slate-400 font-bold uppercase text-xs">Phone</dt><dd class="text-slate-800">{{ $lead->phone ?? '—' }}</dd></div>
                <div><dt class="text-slate-400 font-bold uppercase text-xs">Email</dt><dd class="text-slate-800">{{ $lead->email ?? '—' }}</dd></div>
                <div><dt class="text-slate-400 font-bold uppercase text-xs">Website</dt><dd class="text-slate-800">@if($lead->website)<a href="{{ $lead->website }}" class="text-blue-600 hover:underline" target="_blank" rel="noopener">{{ $lead->website }}</a>@else — @endif</dd></div>
                <div><dt class="text-slate-400 font-bold uppercase text-xs">Industry</dt><dd class="text-slate-800">{{ $lead->industry ? ucfirst(str_replace('_', ' ', $lead->industry)) : '—' }}</dd></div>
                <div><dt class="text-slate-400 font-bold uppercase text-xs">City</dt><dd class="text-slate-800">{{ $lead->city ?? '—' }}</dd></div>
                <div><dt class="text-slate-400 font-bold uppercase text-xs">Source</dt><dd class="text-slate-800">{{ $lead->source ? ucfirst(str_replace('_', ' ', $lead->source)) : '—' }}</dd></div>
                <div><dt class="text-slate-400 font-bold uppercase text-xs">Est. value</dt><dd class="text-slate-800 font-semibold">{{ $lead->estimated_value !== null ? '₹'.number_format($lead->estimated_value, 0) : '—' }}</dd></div>
            </dl>
            @if($lead->notes)
            <div class="mt-4 pt-4 border-t border-slate-100">
                <p class="text-xs font-bold text-slate-400 uppercase mb-1">Notes</p>
                <p class="text-slate-700 whitespace-pre-wrap">{{ $lead->notes }}</p>
            </div>
            @endif
            @if($lead->stage === 'lost' && $lead->lost_reason)
            <div class="mt-4 p-3 bg-red-50 border border-red-100 rounded-lg text-sm text-red-800">
                <strong>Lost reason:</strong> {{ $lead->lost_reason }}
            </div>
            @endif
        </div>

        @can('create lead activities')
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
            <h3 class="font-bold text-slate-800 mb-4">Log activity</h3>
            <form action="{{ route('leads.activity', $lead) }}" method="POST" class="space-y-3">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-1">
                        <label class="block text-xs font-bold text-slate-500 mb-1">Type</label>
                        <select name="type" class="w-full border rounded-lg p-2 text-sm bg-white" required>
                            @foreach(\App\Models\LeadActivity::TYPES as $t)
                            @if($t !== 'stage_change')
                            <option value="{{ $t }}">{{ str_replace('_', ' ', ucfirst($t)) }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 mb-1">Description</label>
                        <textarea name="description" rows="2" class="w-full border rounded-lg p-2 text-sm" required placeholder="What happened?"></textarea>
                    </div>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700">Save activity</button>
            </form>
        </div>
        @endcan

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
            <h3 class="font-bold text-slate-800 mb-6">Activity timeline</h3>
            <div class="relative border-l-2 border-slate-200 ml-3 space-y-6 pl-6">
                @forelse($lead->activities as $activity)
                <div class="relative">
                    @php
                        $dot = match($activity->type_color) {
                            'blue' => 'bg-blue-500',
                            'green' => 'bg-green-500',
                            'purple' => 'bg-purple-500',
                            'cyan' => 'bg-cyan-500',
                            'orange' => 'bg-orange-500',
                            'indigo' => 'bg-indigo-500',
                            'amber' => 'bg-amber-500',
                            default => 'bg-slate-400',
                        };
                    @endphp
                    <span class="absolute -left-[1.6rem] top-1 w-3 h-3 rounded-full ring-4 ring-white border border-slate-200 {{ $dot }}"></span>
                    <div class="flex flex-wrap items-start justify-between gap-2 mb-1">
                        <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 min-w-0">
                            <i class="{{ $activity->type_icon }} mr-1.5 inline-block text-base leading-none text-slate-600 shrink-0" aria-hidden="true"></i>
                            <span class="font-bold text-slate-700">{{ str_replace('_', ' ', ucfirst($activity->type)) }}</span>
                            <span>·</span>
                            <span>{{ $activity->user?->name }}</span>
                            <span>·</span>
                            <span>{{ $activity->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            @can('edit lead activities')
                            <details class="group/edit">
                                <summary class="cursor-pointer list-none text-xs font-medium text-blue-600 hover:text-blue-800 hover:underline">Edit</summary>
                                <form action="{{ route('leads.activities.update', [$lead, $activity]) }}" method="POST" class="mt-3 p-4 bg-slate-50 border border-slate-200 rounded-lg space-y-3 w-full max-w-md">
                                    @csrf
                                    @method('PATCH')
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Type</label>
                                        <select name="type" class="w-full border rounded-lg p-2 text-sm bg-white">
                                            @foreach(\App\Models\LeadActivity::TYPES as $t)
                                            <option value="{{ $t }}" {{ $activity->type === $t ? 'selected' : '' }}>{{ str_replace('_', ' ', ucfirst($t)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Description</label>
                                        <textarea name="description" rows="3" class="w-full border rounded-lg p-2 text-sm" required>{{ $activity->description }}</textarea>
                                    </div>
                                    <button type="submit" class="text-sm bg-blue-600 text-white px-3 py-1.5 rounded-lg font-semibold hover:bg-blue-700">Save changes</button>
                                </form>
                            </details>
                            @endcan
                            @can('delete lead activities')
                            <form action="{{ route('leads.activities.destroy', [$lead, $activity]) }}" method="POST" class="inline" onsubmit="return confirm('Remove this activity from the timeline?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-800 hover:underline">Delete</button>
                            </form>
                            @endcan
                        </div>
                    </div>
                    <p class="text-slate-800 text-sm whitespace-pre-wrap">{{ $activity->description }}</p>
                </div>
                @empty
                <p class="text-slate-400 text-sm">No activities yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="space-y-6">
        @can('edit leads')
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
            <h3 class="font-bold text-slate-800 mb-3">Change stage</h3>
            <form action="{{ route('leads.stage', $lead) }}" method="POST" class="space-y-3">
                @csrf
                @method('PATCH')
                <select name="stage" class="w-full border rounded-lg p-2 text-sm bg-white">
                    @foreach(\App\Models\Lead::STAGES as $st)
                    <option value="{{ $st }}" {{ $lead->stage === $st ? 'selected' : '' }}>{{ str_replace('_', ' ', ucfirst($st)) }}</option>
                    @endforeach
                </select>
                <div id="lost-reason-wrap" class="{{ $lead->stage === 'lost' ? '' : 'hidden' }}">
                    <label class="block text-xs font-bold text-slate-500 mb-1">Lost reason</label>
                    <input type="text" name="lost_reason" value="{{ old('lost_reason', $lead->lost_reason) }}" class="w-full border rounded-lg p-2 text-sm" placeholder="Required if Lost">
                </div>
                <button type="submit" class="w-full bg-slate-800 text-white py-2 rounded-lg text-sm font-semibold hover:bg-slate-900">Update stage</button>
            </form>
        </div>
        <script>
            document.querySelector('select[name="stage"]')?.addEventListener('change', function() {
                const wrap = document.getElementById('lost-reason-wrap');
                if (wrap) wrap.classList.toggle('hidden', this.value !== 'lost');
            });
        </script>
        @endcan

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
            <h3 class="font-bold text-slate-800 mb-3">Quick stats</h3>
            <ul class="text-sm space-y-2 text-slate-600">
                <li>Days since created: <strong class="text-slate-900">{{ max(0, (int) $lead->created_at->copy()->startOfDay()->diffInDays(now()->copy()->startOfDay())) }}</strong></li>
                <li>Total activities: <strong class="text-slate-900">{{ $lead->activities->count() }}</strong></li>
                <li>Last contact: <strong class="text-slate-900">{{ $lead->last_contacted_at ? $lead->last_contacted_at->diffForHumans() : '—' }}</strong></li>
                <li>Assigned: <strong class="text-slate-900">{{ $lead->assignee?->name ?? '—' }}</strong></li>
            </ul>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
            <h3 class="font-bold text-slate-800 mb-2">Next follow-up</h3>
            <p class="text-lg font-semibold text-slate-800">{{ $lead->next_follow_up ? $lead->next_follow_up->format('M j, Y') : 'Not set' }}</p>
            @can('edit leads')
            <a href="{{ route('leads.edit', $lead) }}#follow-up" class="inline-block mt-3 text-sm text-blue-600 font-medium hover:underline">Update in edit →</a>
            @endcan
        </div>

        @if($lead->stage === 'won')
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
            @php $wonClient = $lead->clients->first(); @endphp
            @if($wonClient)
                <a href="{{ route('clients.show', $wonClient) }}" class="block w-full text-center py-2 rounded-lg text-sm font-semibold bg-cyan-600 text-white hover:bg-cyan-700 transition">View client</a>
                <p class="text-xs text-slate-500 mt-2">This lead is linked to a client record.</p>
            @elseif(auth()->user()->can('create clients'))
                <a href="{{ route('clients.create', ['lead_id' => $lead->id]) }}" class="block w-full text-center py-2 rounded-lg text-sm font-semibold bg-blue-600 text-white hover:bg-blue-700 transition">Create client</a>
                <p class="text-xs text-slate-500 mt-2">Pre-fills name and contact details from this lead.</p>
            @else
                <p class="text-sm text-slate-600">A client can be created from this won lead by a user with permission to create clients.</p>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
