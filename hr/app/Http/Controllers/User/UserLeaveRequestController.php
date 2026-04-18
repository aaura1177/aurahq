<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserRequest;
use Illuminate\Http\Request;
use App\Models\LeaveType;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\LeaveRequestMail;
use Illuminate\Support\Carbon; 


use App\Models\LeaveRequest;

class UserLeaveRequestController extends Controller
{
 
    public function index()
    {
        $emp_id = auth('employee')->id();
    
        $leavetypes = LeaveRequest::where('employee_id', $emp_id)->with('user','leaveType')->orderBy('created_at', 'desc')->get();
    
        return view('user.leaves.index', compact('leavetypes'));
    }
    
public function addleaves(){


     $leavetypes  =  LeaveType::all();
    return  view('user.leaves.add-leaves' ,compact('leavetypes'));

}

 // Import the UserRequest class

// public function createleaves(UserRequest $request)
// {

//     $validatedData = $request->validated();

//       $emp_id = auth('employee')->id();
//     $leave= LeaveRequest::create([
//         'employee_id' => $emp_id,
//         'leave_type_id' => $validatedData['leave_type_id'],
//         'start_at' => $validatedData['start_at'],
//         'end_at' => $validatedData['end_at'],
//         'reason' => $validatedData['reason'],
//     ]);

//     Notification::create([
//         'employee_id' => $emp_id,
       
//         'message' => $leave->leave_type_id,
//         'status' => 0,
//         'is_read' => '0',
//     ]);


//     return redirect()->route('user.leave')->with('success', 'Leave applied successfully!');
// }



public function createleaves(UserRequest $request)
{
    $validatedData = $request->validated();
    $emp_id = auth('employee')->id();

    $leave = LeaveRequest::create([
        'employee_id' => $emp_id,
        'leave_type_id' => $validatedData['leave_type_id'],
        'start_at' => $validatedData['start_at'],
        'end_at' => $validatedData['end_at'],
        'reason' => $validatedData['reason'],
       'applied_on' => Carbon::now()->toDateString(),
    ]);

    // Load relations after create
    $leave->load(['employee', 'leaveType']);

    Notification::create([
        'employee_id' => $emp_id,
        'message' => $leave->leave_type_id,
        'status' => 0,
        'is_read' => '0',
    ]);

    // ✅ Send email with model object
    Mail::to('office@aurateria.com')->send(new LeaveRequestMail($leave));

    return redirect()->route('user.leave')->with('success', 'Leave applied successfully!');
}




public function destroy($id)
{
    $leaveRequest = LeaveRequest::find($id);

    if ($leaveRequest) {
        $leaveRequest->delete();

        return redirect()->route('user.leave')->with('success', 'Leave request deleted successfully.');
    }

    return redirect()->route('user.leave')->with('error', 'Leave request not found.');
}

    
}
