<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EltercityReading;
use App\Models\Employee;

class EltercityReadingController extends Controller
{
   public function index(Request $request)
{
    $fromDate = $request->input('from_date', now()->startOfMonth()->toDateString());
    $toDate = $request->input('to_date', now()->endOfMonth()->toDateString());

    $readings = EltercityReading::whereBetween('date', [$fromDate, $toDate])
        ->orderBy('created_at', 'desc')
        ->get();

    return view('admin.eltercityReadings.index', compact('readings', 'fromDate', 'toDate'));
}

    public function create()
    {
        return view('admin.eltercityReadings.add');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'time_slot'  => 'nullable|in:morning,evening',
            'reading'    => 'nullable|string|max:255',
            'date'       => 'required|date',
            'screenshot' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('screenshot')) {
            $filePath = $request->file('screenshot')->store('eltercity_screenshots', 'public');
            $validated['screenshot'] = $filePath;
        }

        EltercityReading::create($validated);

        return redirect()->route('admin.eltercity_readings')->with('success', 'Reading submitted successfully.');
    }
    public function destroy($id)
    {
        $EltercityReading = EltercityReading::find($id);

        if (!$EltercityReading) {
            return redirect()->route('admin.eltercity_readings')
                ->with('error', 'EltercityReading not found.');
        }

        $EltercityReading->delete();

        return redirect()->route('admin.eltercity_readings')
            ->with('success', 'EltercityReading deleted successfully.');
    }
    public function update(Request $request)
    {

        $reading = EltercityReading::findOrFail($request->id);

        $validated = $request->validate([
            'time_slot'   => 'required|in:morning,evening',
            'date'        => 'required|date',
            'reading'     => 'required|string|max:255',
            'screenshot'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('screenshot')) {
            if ($reading->screenshot && Storage::disk('public')->exists($reading->screenshot)) {
                Storage::disk('public')->delete($reading->screenshot);
            }

            $filePath = $request->file('screenshot')->store('eltercity_screenshots', 'public');
            $validated['screenshot'] = $filePath;
        }

        $reading->update($validated);

        return redirect()->back()->with('success', 'Reading updated successfully.');
    }
}
