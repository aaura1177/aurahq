<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use App\Models\Holiday;
use Carbon\Carbon;

class DashboardController extends Controller
{



    public function index()
    {



        $today = Carbon::today();
        $totalEmployee = Employee::count();
        $holidays = Holiday::where('date', '>=', Carbon::today())
            ->where('date', '<=', Carbon::today()->addDays(30))
            ->get();
        $leaves = LeaveRequest::where('start_at', '>=', Carbon::today())
            ->where('start_at', '<=', Carbon::today()->addDays(10))
            ->get();

        $absentEmployees = Attendance::leftJoin('employees', 'attendance.employee_id', '=', 'employees.id')
            ->whereDate('attendance.date', $today)
            ->select('employees.name', 'attendance.status')
            ->get();


        $Persent = Attendance::whereDate('attendance.date', $today)
            ->where('attendance.status', 'Present')
            ->join('employees', 'attendance.employee_id', '=', 'employees.id')
            ->select('employees.name', 'attendance.status')
            ->get();

        $absent = Attendance::whereDate('attendance.date', $today)
            ->where('attendance.status', 'Absent')
            ->join('employees', 'attendance.employee_id', '=', 'employees.id')
            ->select('employees.name', 'attendance.status')
            ->get();
        $futureDate = Carbon::today()->addDays(10);

        $birthdays = Employee::whereMonth('date_of_birth', '>=', $today->month)
            ->whereMonth('date_of_birth', '<=', $futureDate->month)
            ->whereRaw('DAYOFYEAR(date_of_birth) >= ?', [$today->dayOfYear])
            ->whereRaw('DAYOFYEAR(date_of_birth) <= ?', [$futureDate->dayOfYear])
            ->get();


        return view('admin.dashboard.index', compact('totalEmployee', 'absent', 'Persent', 'leaves', 'holidays', 'birthdays'));
    }

    public function profile()
    {
        return view('admin.profile.index');
    }
    public function profileupdate(AdminRequests $request)
    {
        // Validate the request data
        $validatedData = $request->validated();

       $user = auth()->user();

        if (!$user) {
            return redirect()->route('login')->withErrors(['error' => 'User not found']);
        }

        if ($request->hasFile('image')) {

            if ($user->image && Storage::exists('public/' . $user->image)) {
                \Log::debug('Deleting old image:', ['path' => 'public/' . $user->image]);
                Storage::delete('public/' . $user->image);
            }

            $path = $request->file('image')->store('profile_images', 'public');

            $validatedData['image'] = $path;
        } else {
            \Log::debug('No Image Uploaded');
        }

        $user->update($validatedData);

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully!');
    }


    public function passwordupdate(AdminRequests $request)
    {
        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->password = Hash::make($request->password);

        if ($user->save()) {
            return redirect()->route('admin.profile')->with('success', 'Your password has been updated successfully!');
        }

        return back()->withErrors(['password' => 'There was an issue updating your password. Please try again.']);
    }


    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login')->with('success', 'You have been logged out successfully.');
    }
}
