<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiJson;
use App\Models\Finance;
use App\Models\RevenueTarget;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RevenueTargetApiController extends Controller
{
    public function index()
    {
        $paginator = RevenueTarget::orderByDesc('month')->paginate(25);

        return ApiJson::paginated($paginator, function (RevenueTarget $t) {
            $start = Carbon::parse($t->month)->startOfMonth();
            $end = $start->copy()->endOfMonth();
            $actual = (float) Finance::query()
                ->where('is_active', true)
                ->where('type', 'received')
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('amount');
            $target = (float) $t->target_amount;

            return [
                'id' => $t->id,
                'month' => $t->month->format('Y-m-d'),
                'target_amount' => $target,
                'actual_revenue' => $actual,
                'pct_achieved' => $target > 0 ? min(round(($actual / $target) * 100, 1), 999) : 0.0,
                'notes' => $t->notes,
            ];
        });
    }

    public function store(Request $request)
    {
        $request->validate([
            'month' => ['required', 'date', Rule::unique('revenue_targets', 'month')],
            'target_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:2000',
        ]);

        $month = Carbon::parse($request->input('month'))->startOfMonth()->format('Y-m-d');
        $t = RevenueTarget::create([
            'month' => $month,
            'target_amount' => $request->input('target_amount'),
            'notes' => $request->input('notes'),
        ]);

        return ApiJson::created(['id' => $t->id], 'Revenue target created successfully');
    }

    public function update(Request $request, RevenueTarget $revenueTarget)
    {
        $request->validate([
            'month' => ['sometimes', 'date', Rule::unique('revenue_targets', 'month')->ignore($revenueTarget->id)],
            'target_amount' => 'sometimes|numeric|min:0',
            'notes' => 'nullable|string|max:2000',
        ]);

        $data = $request->only(['target_amount', 'notes']);
        if ($request->has('month')) {
            $data['month'] = Carbon::parse($request->input('month'))->startOfMonth()->format('Y-m-d');
        }
        $revenueTarget->update($data);

        return ApiJson::ok($revenueTarget->fresh(), 'Updated');
    }

    public function destroy(RevenueTarget $revenueTarget)
    {
        $revenueTarget->delete();

        return ApiJson::noContent();
    }
}
