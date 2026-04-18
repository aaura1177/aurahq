<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayApiController extends Controller
{
    public function index()
    {
        $holidays = Holiday::orderBy('date', 'desc')->get()->map(fn ($h) => [
            'id' => $h->id,
            'name' => $h->name,
            'date' => $h->date->format('Y-m-d'),
        ]);
        return response()->json(['data' => $holidays]);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255', 'date' => 'required|date']);
        $holiday = Holiday::create($request->only('name', 'date'));
        return response()->json(['message' => 'Created', 'data' => ['id' => $holiday->id, 'name' => $holiday->name, 'date' => $holiday->date->format('Y-m-d')]], 201);
    }

    public function show(Holiday $holiday)
    {
        return response()->json(['data' => ['id' => $holiday->id, 'name' => $holiday->name, 'date' => $holiday->date->format('Y-m-d')]]);
    }

    public function update(Request $request, Holiday $holiday)
    {
        $request->validate(['name' => 'required|string|max:255', 'date' => 'required|date']);
        $holiday->update($request->only('name', 'date'));
        return response()->json(['message' => 'Updated', 'data' => ['id' => $holiday->id, 'name' => $holiday->name, 'date' => $holiday->date->format('Y-m-d')]]);
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
