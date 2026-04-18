<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Mail\AdminCheckoutNotification;
use Illuminate\Support\Facades\Mail;

class AttendanceController extends Controller
{
 
public function index(Request $request)
{
    $employeeId = $request->input('employee_id');
    $fromDate = $request->input('from_date', now()->startOfMonth()->toDateString());
    $toDate = $request->input('to_date', now()->toDateString());

    $attendance = Attendance::query()
        ->when($employeeId, function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId);
        })
        ->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
            $query->whereBetween('date', [$fromDate, $toDate]);
        })
        ->orderBy('date', 'desc')
        ->get();

    $employees = Employee::where('department_id',1)->where('status',1)->get();

    return view('admin.attendance.index', compact('attendance', 'employees'));
}





public function destroy($id)
{
    $attendance = Attendance::findOrFail($id);

    // Sirf employee_id ko null karo
    $attendance->employee_id = null;
    $attendance->save();

    return redirect()->route('admin.attendance')->with('success', 'Employee ID removed successfully.');
}



public function admincheckout(Request $request)
{
   

    $attendanceId = $request->attendance_id;
    $attendance = Attendance::where('id', $attendanceId)
         ->whereDate('date', Carbon::today())
        ->first();

    $employee = Employee::where('id',$attendance->employee_id)->first();

    if (!$employee) {
        return redirect()->back()->with('error', 'Employee not found.');
    }

    if (!is_numeric($employee->salary) || $employee->salary <= 0) {
        $earnedSalary = 0;
    }

    $daysInMonth  = Carbon::now()->daysInMonth;

    $workDayHours = 7;

    $employeeSalaryMonth = $employee->salary;
    $employeeSalaryDay = $employeeSalaryMonth / $daysInMonth;
    $employeeSalaryPerHour = $employeeSalaryDay / $workDayHours;
    $employeeSalaryPerMinute = $employeeSalaryPerHour / 60;
    $employeeSalaryPerSecond = $employeeSalaryPerMinute / 60;

    

    if (!$attendance) {
        return redirect()->back()->with('error', 'Check-In record not found!');
    }

    $checkInTime = Carbon::parse($attendance->check_in_time);
    $checkOutTime = Carbon::now();

    $workingSeconds = $checkInTime->diffInSeconds($checkOutTime);
    $workingHoursFormatted = gmdate('H:i:s', $workingSeconds);

    $overtimeSeconds = max(0, $workingSeconds - ($workDayHours * 3600));
    $overtimeHoursFormatted = gmdate('H:i:s', $overtimeSeconds);

    $earnedSalary = round($workingSeconds * $employeeSalaryPerSecond, 2); 

    $attendance->update([
        'check_out_time' => $checkOutTime->toTimeString(),
        'working_hours' => $workingHoursFormatted,
        'overtime_hours' => $overtimeHoursFormatted,
        'earned_salary' => $earnedSalary,
    ]);



     try {
    Mail::to('office@aurateria.com')->send(new AdminCheckoutNotification($attendance));
} catch (\Exception $e) {
    // Optional: log error or show warning if needed
    // Log::error("Checkout mail failed: " . $e->getMessage());
}




    return redirect()->back()->with('success', 'Check-Out Successfully Completed!');
}


public function update(Request $request)
{
    $request->validate([
        'id' => 'required|exists:attendance,id',
        'status' => 'required|in:Present,Absent,Half-Day,Leave',
        'check_in_time' => 'nullable|regex:/^\d{2}:\d{2}(:\d{2})?$/',
        'check_out_time' => 'nullable|regex:/^\d{2}:\d{2}(:\d{2})?$/',
        'date' => 'required|date',
    ]);

    $attendance = Attendance::findOrFail($request->id);
    $employee = Employee::find($attendance->employee_id);

    $attendance->status = $request->status;
    $attendance->check_in_time = $request->check_in_time;
    $attendance->check_out_time = $request->check_out_time;
    $attendance->date = $request->date;

    if ($request->check_in_time && $request->check_out_time) {
        try {
            $checkIn = Carbon::parse($request->check_in_time);
            $checkOut = Carbon::parse($request->check_out_time);

            if ($checkOut->greaterThan($checkIn)) {
                $workingSeconds = $checkIn->diffInSeconds($checkOut);
                $workingHours = gmdate('H:i:s', $workingSeconds);
                $attendance->working_hours = $workingHours;

                // Calculate earned salary
                if ($employee && is_numeric($employee->salary) && $employee->salary > 0) {
                    $daysInMonth = Carbon::parse($request->date)->daysInMonth;
                    $workDayHours = 7;
                    $perSecondSalary = ($employee->salary / $daysInMonth) / $workDayHours / 60 / 60;
                    $earnedSalary = round($workingSeconds * $perSecondSalary, 2);
                    $attendance->earned_salary = $earnedSalary;

                    // Optionally calculate overtime
                    $overtimeSeconds = max(0, $workingSeconds - ($workDayHours * 3600));
                    $overtimeHoursFormatted = gmdate('H:i:s', $overtimeSeconds);
                    $attendance->overtime_hours = $overtimeHoursFormatted;
                }
            } else {
                $attendance->working_hours = null;
                $attendance->earned_salary = 0;
                $attendance->overtime_hours = null;
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['time_error' => 'Invalid time format provided.']);
        }
    } else {
        $attendance->working_hours = null;
        $attendance->earned_salary = 0;
        $attendance->overtime_hours = null;
    }

    $attendance->save();

    return redirect()->back()->with('success', 'Attendance updated successfully!');
}



}
