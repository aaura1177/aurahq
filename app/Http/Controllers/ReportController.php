<?php
namespace App\Http\Controllers;
use App\Models\GroceryListItem;
use App\Models\GroceryExpense;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller implements HasMiddleware
{
    public static function middleware(): array {
        return [new Middleware('permission:view reports', only: ['index'])];
    }

    private function getReportData($startDate, $endDate) {
        $items = GroceryListItem::where('status', 'purchased')
            ->where('is_active', true)
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->orderBy('updated_at', 'desc')
            ->get();
            
        $expenses = GroceryExpense::whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        return [
            'total' => $items->sum('actual_cost') + $expenses->sum('amount'),
            'items' => $items,
            'expenses' => $expenses
        ];
    }

    public function index(Request $request) {
        $now = Carbon::now();
        
        // 1. Standard Timelines
        $daily = $this->getReportData($now->copy()->startOfDay(), $now->copy()->endOfDay());
        $weekly = $this->getReportData($now->copy()->startOfWeek(), $now->copy()->endOfWeek());
        $monthly = $this->getReportData($now->copy()->startOfMonth(), $now->copy()->endOfMonth());

        // 2. Custom Filter (validate dates to avoid Carbon parse errors)
        $custom = null;
        if ($request->start_date && $request->end_date) {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);
            $custom = $this->getReportData(
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            );
        }

        // 3. Category Breakdown (Pie Chart) - Monthly
        $catStats = GroceryListItem::where('status', 'purchased')
            ->where('is_active', true)
            ->whereBetween('updated_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->select('type', DB::raw('SUM(actual_cost) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');
        
        // Add variable expenses as a category
        $varTotal = $monthly['expenses']->sum('amount');
        if($varTotal > 0) $catStats['variations'] = $varTotal;

        $catLabels = $catStats->keys();
        $catData = $catStats->values();

        // 4. Daily Trend (Bar Chart) - Last 7 Days
        $trendLabels = [];
        $trendData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $trendLabels[] = $date->format('D'); 
            $data = $this->getReportData($date->copy()->startOfDay(), $date->copy()->endOfDay());
            $trendData[] = $data['total'];
        }

        return view('reports.index', compact(
            'daily', 'weekly', 'monthly', 'custom', 
            'catLabels', 'catData', 'trendLabels', 'trendData'
        ));
    }
}
