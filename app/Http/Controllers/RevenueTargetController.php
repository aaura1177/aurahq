<?php

namespace App\Http\Controllers;

use App\Models\Finance;
use App\Models\RevenueTarget;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;

class RevenueTargetController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view revenue targets', only: ['index']),
            new Middleware('permission:create revenue targets', only: ['create', 'store']),
            new Middleware('permission:edit revenue targets', only: ['edit', 'update']),
            new Middleware('permission:delete revenue targets', only: ['destroy']),
        ];
    }

    public function index()
    {
        $targets = RevenueTarget::orderByDesc('month')->paginate(24);

        $targets->getCollection()->transform(function (RevenueTarget $t) {
            $start = Carbon::parse($t->month)->startOfMonth();
            $end = $start->copy()->endOfMonth();
            $actual = (float) Finance::query()
                ->where('is_active', true)
                ->where('type', 'received')
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('amount');
            $target = (float) $t->target_amount;
            $pct = $target > 0 ? min(round(($actual / $target) * 100, 1), 999) : 0.0;

            return (object) [
                'id' => $t->id,
                'month' => $t->month,
                'target_amount' => $t->target_amount,
                'notes' => $t->notes,
                'actual_revenue' => $actual,
                'pct_achieved' => $pct,
            ];
        });

        return view('revenue-targets.index', compact('targets'));
    }

    public function create()
    {
        return view('revenue-targets.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        RevenueTarget::create($data);

        return redirect()->route('revenue-targets.index')->with('success', 'Revenue target saved.');
    }

    public function edit(RevenueTarget $revenueTarget)
    {
        return view('revenue-targets.edit', ['target' => $revenueTarget]);
    }

    public function update(Request $request, RevenueTarget $revenueTarget)
    {
        $data = $this->validated($request, $revenueTarget);
        $revenueTarget->update($data);

        return redirect()->route('revenue-targets.index')->with('success', 'Revenue target updated.');
    }

    public function destroy(RevenueTarget $revenueTarget)
    {
        $revenueTarget->delete();

        return redirect()->route('revenue-targets.index')->with('success', 'Revenue target removed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?RevenueTarget $existing = null): array
    {
        $monthRule = [
            'required',
            'date',
            Rule::unique('revenue_targets', 'month')->ignore($existing?->id),
        ];

        $request->validate([
            'month' => $monthRule,
            'target_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:2000',
        ]);

        $month = Carbon::parse($request->input('month'))->startOfMonth()->format('Y-m-d');

        return [
            'month' => $month,
            'target_amount' => $request->input('target_amount'),
            'notes' => $request->input('notes'),
        ];
    }
}
