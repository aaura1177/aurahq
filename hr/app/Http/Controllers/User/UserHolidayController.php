<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Holiday;

class UserHolidayController extends Controller
{
public function index(Request $request)
{
    $query = Holiday::query()->where('is_active', 1);

    // Filter by year (default: current year when no filter applied)
    if (!$request->has('year')) {
        $query->whereYear('date', date('Y'));
    } elseif ($request->filled('year')) {
        $query->whereYear('date', $request->year);
    }

    // Filter by month
    if ($request->filled('month')) {
        $query->whereMonth('date', $request->month);
    }

    // Search by holiday name
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    $holiday = $query->orderBy('date', 'desc')->get();

    return view('user.holiday.index', compact('holiday'));
}
}
