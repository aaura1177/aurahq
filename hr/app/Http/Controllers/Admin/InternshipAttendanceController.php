<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InternshipAttendance;

use App\Models\Employee;

class InternshipAttendanceController extends Controller
{
    public function index(Request $request){
         $employeeId = $request->input('employee_id');
    $fromDate = $request->input('from_date', now()->startOfMonth()->toDateString());
    $toDate = $request->input('to_date', now()->toDateString());

    $attendance = InternshipAttendance::query()
        ->when($employeeId, function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId);
        })
        ->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
            $query->whereBetween('date', [$fromDate, $toDate]);
        })
        ->orderBy('date', 'desc')
        ->get();

    $employees = Employee::where('department_id', 5)->get();
        return view('admin.Internship.index',compact('employees','attendance'));
    }
      public function destroy($id)
    {
        $attendance = InternshipAttendance::find($id);
    
        if (!$attendance) {
            return redirect()->route('admin.internship')
                             ->with('error', 'Attendance not found.');
        }
    
        $attendance->delete();
    
        return redirect()->route('admin.internship')
                         ->with('success', 'Attendance deleted successfully.');
    }
}
