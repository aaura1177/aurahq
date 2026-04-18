<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRequests;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends Controller
{
   public function index(Request $request)
{
    $employees = Employee::all();

    $month = $request->input('month', now()->month);
    $year = $request->input('year', now()->year);
    $employeeId = $request->input('employee_id');

    $query = LeaveRequest::query();

    $query->whereMonth('start_at', $month)
          ->whereYear('start_at', $year);

    if (!empty($employeeId)) {
        $query->where('employee_id', $employeeId);
    }

    $leaves = $query->orderBy('created_at', 'desc')->get();

    return view('admin.leaves.index', compact('leaves', 'employees'));
}

    public function editstatus(AdminRequests $request)
    {

        $leaveRequest = LeaveRequest::findOrFail($request->id);

        $user = Auth::user();
        $leaveRequest->status = $request->status;
        $leaveRequest->approved_by = $user->id;


        if ($request->status === 'rejected') {
            $leaveRequest->rejection_reason_text = $request->rejection_reason_text;
        } else {
            $leaveRequest->rejection_reason_text = null; // Clear rejection reason if status isn't rejected
        }

        // Save the updated leave request
        $leaveRequest->save();

        // Redirect back with a success message
        return redirect()->route('admin.leaves')->with('success', 'Leave request updated successfully.');
    }
}
