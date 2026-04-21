<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\Support\ApiJson;
use Illuminate\Http\Request;

class HolidayApiController extends Controller
{
    public function index()
    {
        $paginator = Holiday::orderBy('date', 'desc')->paginate(25);

        return ApiJson::paginated($paginator, fn ($h) => [
            'id' => $h->id,
            'name' => $h->name,
            'date' => $h->date->format('Y-m-d'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255', 'date' => 'required|date']);
        $holiday = Holiday::create($request->only('name', 'date'));

        return ApiJson::created([
            'id' => $holiday->id,
            'name' => $holiday->name,
            'date' => $holiday->date->format('Y-m-d'),
        ], 'Holiday created successfully');
    }

    public function show(Holiday $holiday)
    {
        return ApiJson::ok([
            'id' => $holiday->id,
            'name' => $holiday->name,
            'date' => $holiday->date->format('Y-m-d'),
        ]);
    }

    public function update(Request $request, Holiday $holiday)
    {
        $request->validate(['name' => 'required|string|max:255', 'date' => 'required|date']);
        $holiday->update($request->only('name', 'date'));

        return ApiJson::ok([
            'id' => $holiday->id,
            'name' => $holiday->name,
            'date' => $holiday->date->format('Y-m-d'),
        ], 'Updated');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        return ApiJson::noContent();
    }
}
