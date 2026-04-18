<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\InternshipAttendance;
use Carbon\Carbon;

class UserInternshipController extends Controller
{
   public function index(Request $request)
{
    $fromDate = $request->input('from_date', now()->startOfMonth()->toDateString());
    $toDate = $request->input('to_date', now()->endOfMonth()->toDateString());

    $employees = Employee::where('department_id', 5)->get();
    $userId = auth('employee')->id();

    $internshipAttendance = InternshipAttendance::where('user_id', $userId)
        ->whereBetween('date', [$fromDate, $toDate])
        ->orderBy('created_at', 'desc')
        ->get();

    return view('user.Internship.index', compact('employees', 'internshipAttendance', 'fromDate', 'toDate'));
}


    public function checkStatus($employee_id)
    {
        $today = Carbon::today()->toDateString();
 $userId = auth('employee')->id();
        $record = InternshipAttendance::where('employee_id', $employee_id)
            ->where('date', $today)
            ->where('user_id', $userId)
            ->whereNull('check_out_time')
            ->latest()
            ->first();

        if ($record && $record->check_in_time) {
            return response()->json(['status' => 'in']);
        }

        return response()->json(['status' => 'out']);
    }

    public function tapIn($employee_id)
    {
        $today = Carbon::today()->toDateString();
$now = Carbon::now();
        $userId = auth('employee')->id();

        // Check for any incomplete session
        // $incomplete = InternshipAttendance::where('employee_id', $employee_id)
        //     ->where('date', $today)
        //     ->whereNull('check_out_time')
        //     ->first();

        // if ($incomplete) {
        //     return redirect()->back()->with('error', 'You are already tapped in. Please tap out first.');
        // }

     $incomplete = InternshipAttendance::where('user_id', $userId)
    ->where('date', $today)
    ->whereNull('check_out_time')
    ->first();

if ($incomplete) {
    $checkIn = Carbon::parse($incomplete->check_in_time);
    $workingHours = $checkIn->diff($now)->format('%H:%I:%S');

    $incomplete->check_out_time = $now->toTimeString();
    $incomplete->working_hours = $workingHours;
    $incomplete->save();
}




        // Create new record
        InternshipAttendance::create([
            'user_id' => $userId,
            'employee_id' => $employee_id,
            'date' => $today,
            'check_in_time' => $now,
            'status' => 'Present',
        ]);

        return redirect()->back()->with('success', 'Tapped In Successfully');
    }

    public function tapOut($employee_id)
    {
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        // Get the latest incomplete session
        $attendance = InternshipAttendance::where('employee_id', $employee_id)
            ->where('date', $today)
            ->whereNull('check_out_time')
            ->latest()
            ->first();

        if ($attendance) {
            $checkIn = Carbon::parse($attendance->check_in_time);
            $workingHours = $checkIn->diff($now)->format('%H:%I:%S');

            $attendance->update([
                'check_out_time' => $now->toTimeString(),
                'working_hours' => $workingHours,
            ]);

            return redirect()->back()->with('success', 'Tapped Out Successfully');
        }

        return redirect()->back()->with('error', 'No active tap-in session found.');
    }
}
