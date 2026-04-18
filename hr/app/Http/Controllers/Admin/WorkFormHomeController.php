<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\Models\WorkForm;
use App\Models\Employee;
use App\Models\Attendance;
use App\Http\Requests\Admin\AdminRequests;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

class WorkFormHomeController extends Controller
{
 public function index(Request $request)
{
    $employeeId = $request->input('employee_id');
    $fromDate = $request->input('from_date', now()->startOfMonth()->toDateString());
    $toDate = $request->input('to_date', now()->endOfMonth()->toDateString());

    $workformhomeQuery = WorkForm::with('user')
        ->whereBetween('work_date', [$fromDate, $toDate])
        ->orderBy('created_at', 'desc');

    if (!empty($employeeId)) {
        $workformhomeQuery->where('user_id', $employeeId);
    }

    $workformhome = $workformhomeQuery->get();
    $employees = Employee::all();

    return view('admin.workformhome.index', compact('workformhome', 'employees'));
}



    public function update(AdminRequests $request)
    {
        try {
            $validatedData = $request->validated();

            $workForm = WorkForm::findOrFail($validatedData['id']);


            if ($validatedData['status'] === 'approved') {
                $workDate = Carbon::parse($workForm->work_date);
                $startTime = Carbon::createFromFormat('H:i:s', $workForm->start_time)->setDateFrom($workDate);
                $endTime = Carbon::createFromFormat('H:i:s', $workForm->end_time)->setDateFrom($workDate);

                if ($endTime->lessThan($startTime)) {
                    $endTime->addDay();
                }

                $workingSeconds = $startTime->diffInSeconds($endTime);
                $workingHoursFormatted = gmdate('H:i:s', $workingSeconds);

                $workDayHours = 7;
                $overtimeSeconds = max(0, $workingSeconds - ($workDayHours * 3600));
                $overtimeHoursFormatted = gmdate('H:i:s', $overtimeSeconds);

                $employee = Employee::find($workForm->user_id);

                if (!$employee || !is_numeric($employee->salary) || $employee->salary <= 0) {
                    return redirect()->back()->with('error', 'Invalid or missing employee salary.');
                }

                $daysInMonth =  Carbon::now()->daysInMonth;;
                $salaryPerDay = $employee->salary / $daysInMonth;
                $salaryPerHour = $salaryPerDay / $workDayHours;
                $salaryPerMinute = $salaryPerHour / 60;
                $salaryPerSecond = $salaryPerMinute / 60;

                $earnedSalary = round($workingSeconds * $salaryPerSecond, 2);

                $workForm->update([
                    'status' => $validatedData['status'],
                    'working_hours'  => $workingHoursFormatted,
                ]);
                //      $existingAttendance = Attendance::where('employee_id', $workForm->user_id)
                //     ->where('date', $workForm->work_date)
                //     ->first();

                // if (!$existingAttendance) {
                //     Attendance::create([
                //         'employee_id'    => $workForm->user_id,
                //         'date'           => $workForm->work_date,
                //         'check_in_time'  => $workForm->start_time,
                //         'check_out_time' => $workForm->end_time,
                //         'working_hours'  => $workingHoursFormatted,
                //         'earned_salary'  => $earnedSalary,
                //         'status'         => 'Present',
                //     ]);
                // }

            }

            return redirect()->route('admin.work-form-home')->with('success', 'Status updated successfully!');
        } catch (\Exception $e) {
            Log::error("Status update failed: " . $e->getMessage());
            return redirect()->route('admin.work-form-home')->with('error', 'Failed to update status.');
        }
    }

    public function destroy($id)
    {
        $project = WorkForm::find($id);

        if (!$project) {
            return redirect()->route('admin.work-form-home')
                ->with('error', 'work-form-home not found.');
        }

        $project->delete();

        return redirect()->route('admin.work-form-home')
            ->with('success', 'work-form-home deleted successfully.');
    }
}
