<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiJson;
use Illuminate\Http\Request;

class ReportApiController extends Controller
{
    public function index(Request $request)
    {
        return ApiJson::ok([
            'daily' => ['total' => 0, 'items_count' => 0, 'expenses_count' => 0],
            'weekly' => ['total' => 0],
            'monthly' => ['total' => 0],
            'custom' => null,
            'category_labels' => [],
            'category_data' => [],
        ]);
    }
}
