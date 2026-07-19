<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ReportController extends Controller implements HasMiddleware
{
    public static function middleware(): array {
        return [new Middleware('permission:view reports', only: ['index'])];
    }

    public function index(Request $request) {
        $daily = ['total' => 0, 'items' => collect(), 'expenses' => collect()];
        $weekly = ['total' => 0, 'items' => collect(), 'expenses' => collect()];
        $monthly = ['total' => 0, 'items' => collect(), 'expenses' => collect()];
        $custom = null;
        $catLabels = collect();
        $catData = collect();
        $trendLabels = [];
        $trendData = [];

        return view('reports.index', compact(
            'daily', 'weekly', 'monthly', 'custom',
            'catLabels', 'catData', 'trendLabels', 'trendData'
        ));
    }
}
