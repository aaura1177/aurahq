<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiJson;
use App\Models\GroceryExpense;
use App\Models\GroceryListItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportApiController extends Controller
{
    private function getReportData($start, $end)
    {
        $items = GroceryListItem::where('status', 'purchased')->where('is_active', true)
            ->whereBetween('updated_at', [$start, $end])->orderBy('updated_at', 'desc')->get();
        $expenses = GroceryExpense::whereBetween('date', [$start->format('Y-m-d'), $end->format('Y-m-d')])->orderBy('date', 'desc')->get();
        return [
            'total' => $items->sum('actual_cost') + $expenses->sum('amount'),
            'items' => $items,
            'expenses' => $expenses,
        ];
    }

    public function index(Request $request)
    {
        $now = Carbon::now();
        $daily = $this->getReportData($now->copy()->startOfDay(), $now->copy()->endOfDay());
        $weekly = $this->getReportData($now->copy()->startOfWeek(), $now->copy()->endOfWeek());
        $monthly = $this->getReportData($now->copy()->startOfMonth(), $now->copy()->endOfMonth());
        $custom = null;
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $request->validate(['start_date' => 'required|date', 'end_date' => 'required|date|after_or_equal:start_date']);
            $custom = $this->getReportData(
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            );
        }
        $catStats = GroceryListItem::where('status', 'purchased')->where('is_active', true)
            ->whereBetween('updated_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->select('type', DB::raw('SUM(actual_cost) as total'))->groupBy('type')->pluck('total', 'type');
        $varTotal = $monthly['expenses']->sum('amount');
        $catLabels = $catStats->keys()->values()->all();
        $catData = $catStats->values()->all();
        if ($varTotal > 0) {
            $catLabels[] = 'variations';
            $catData[] = $varTotal;
        }
        return ApiJson::ok([
            'daily' => ['total' => $daily['total'], 'items_count' => $daily['items']->count(), 'expenses_count' => $daily['expenses']->count()],
            'weekly' => ['total' => $weekly['total']],
            'monthly' => ['total' => $monthly['total']],
            'custom' => $custom ? ['total' => $custom['total']] : null,
            'category_labels' => $catLabels,
            'category_data' => $catData,
        ]);
    }
}
