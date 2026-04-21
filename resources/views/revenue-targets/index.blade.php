@extends('layouts.admin')
@section('title', 'Revenue Targets')
@section('header', 'Revenue Targets')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        @can('create revenue targets')
        <a href="{{ route('revenue-targets.create') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700">
            <i class="fas fa-plus"></i> New target
        </a>
        @endcan
    </div>

    <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 text-left">
                <tr>
                    <th class="px-4 py-3 font-semibold">Month</th>
                    <th class="px-4 py-3 font-semibold text-right">Target</th>
                    <th class="px-4 py-3 font-semibold text-right">Actual revenue</th>
                    <th class="px-4 py-3 font-semibold text-right">% achieved</th>
                    <th class="px-4 py-3 font-semibold print:hidden">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($targets as $row)
                    <tr class="border-t border-slate-100 hover:bg-slate-50/80">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ \Carbon\Carbon::parse($row->month)->format('F Y') }}</td>
                        <td class="px-4 py-3 text-right">₹{{ number_format((float) $row->target_amount, 0) }}</td>
                        <td class="px-4 py-3 text-right text-green-700 font-semibold">₹{{ number_format((float) $row->actual_revenue, 0) }}</td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-bold {{ $row->pct_achieved >= 100 ? 'text-green-600' : ($row->pct_achieved >= 75 ? 'text-amber-600' : 'text-red-600') }}">
                                {{ number_format($row->pct_achieved, 1) }}%
                            </span>
                        </td>
                        <td class="px-4 py-3 print:hidden">
                            <div class="flex gap-2 justify-end">
                                @can('edit revenue targets')
                                <a href="{{ route('revenue-targets.edit', $row->id) }}" class="text-blue-600 hover:underline text-xs font-semibold">Edit</a>
                                @endcan
                                @can('delete revenue targets')
                                <form action="{{ route('revenue-targets.destroy', $row->id) }}" method="post" class="inline" onsubmit="return confirm('Delete this target?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-xs font-semibold">Delete</button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">No revenue targets yet. Create one to track goals on the CEO dashboard.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $targets->links() }}
        </div>
    </div>
</div>
@endsection
