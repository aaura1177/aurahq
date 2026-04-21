@extends('layouts.admin')
@section('title', 'Overdue Follow-ups')
@section('header', 'Overdue Follow-ups')

@section('content')
<div class="space-y-6">
    <p class="text-sm text-slate-600">Leads with a follow-up date before today (excluding won/lost).</p>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-red-50 text-red-900 font-bold border-b border-red-100">
                    <tr>
                        <th class="px-4 py-3">Business</th>
                        <th class="px-4 py-3">Contact</th>
                        <th class="px-4 py-3">Stage</th>
                        <th class="px-4 py-3">Follow-up was</th>
                        <th class="px-4 py-3">Days overdue</th>
                        <th class="px-4 py-3">Assigned</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($leads as $lead)
                    @php
                        $days = $lead->next_follow_up
                            ? \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($lead->next_follow_up->format('Y-m-d')))
                            : 0;
                    @endphp
                    <tr class="hover:bg-red-50/30">
                        <td class="px-4 py-3 font-semibold">
                            <a href="{{ route('leads.show', $lead) }}" class="text-blue-600 hover:underline">{{ $lead->business_name }}</a>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $lead->contact_person ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-bold text-slate-700">{{ $lead->stage_label }}</span>
                        </td>
                        <td class="px-4 py-3 text-red-700 font-medium">{{ $lead->next_follow_up?->format('M j, Y') }}</td>
                        <td class="px-4 py-3 text-red-600 font-bold">{{ $days }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $lead->assignee?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @can('edit leads')
                            <a href="{{ route('leads.edit', $lead) }}" class="text-blue-600 hover:underline text-sm">Edit</a>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-slate-500">No overdue follow-ups. Great work.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leads->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">{{ $leads->links() }}</div>
        @endif
    </div>
</div>
@endsection
