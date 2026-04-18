<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\AdminRequests;
use App\Models\Holiday;
use Illuminate\Support\Facades\Auth;

class HolidayController extends Controller
{
    public function index(Request $request)
    {
        $query = Holiday::query();

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

        // Filter by status (Active/Inactive)
        if ($request->filled('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        // Search by holiday name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $holiday = $query->orderBy('date', 'desc')->get();

        return view('admin/holiday/index', compact('holiday'));
    }
    public function addholiday(){
        return view('admin/holiday/add-holiday');
    }
    public function createholiday(AdminRequests $request)

    {
        try {
            $data = $request->validated();
            $data['approved_by'] = Auth::check() ? Auth::user()->id : null;
            $holiday = Holiday::create($data);
            return redirect()->route('admin.holiday')
            ->with( 'success','Holiday created successfully!');
           
        
            
        } catch (\Exception $e) {
           
            return redirect()->route('admin.holiday.add')
            ->with( 'success','Something went wrong!');
        }
    }


    public function editholiday(AdminRequests $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['name'] = strtoupper($validatedData['name']);
    
            $existingHoliday = Holiday::where('name', $validatedData['name'])
                ->where('id', '!=', $validatedData['id'])
                ->first();
    
            if ($existingHoliday) {
                return redirect()->route('admin.holiday')->with([
                    'error' => 'Holiday with this name already exists!'
                ]);
            }
    
            $holiday = Holiday::findOrFail($validatedData['id']);
    
            $holiday->update([
                'name' => $validatedData['name'],
                'approved_by' => $validatedData['approved_by'],
                'date' => $validatedData['date'],
                'is_active' => $validatedData['is_active'],
                'color' => $validatedData['color'],
                'remark' => $validatedData['remark'],
            ]);
    
            return redirect()->route('admin.holiday')->with([
                'success' => 'Holiday updated successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error("Failed to update holiday: " . $e->getMessage());
            return redirect()->route('admin.holiday')->with([
                'error' => 'Failed to update holiday!'
            ]);
        }
    }
    

    public function destroy($id)
    {
        $holiday = Holiday::find($id);
    
        if (!$holiday) {
            return redirect()->route('admin.holiday')
                             ->with('error', 'Holiday not found.');
        }
    
        $holiday->delete();
    
        return redirect()->route('admin.holiday')
                         ->with('success', 'Holiday deleted successfully.');
    }
    
}
