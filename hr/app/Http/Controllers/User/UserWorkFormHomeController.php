<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\WorkForm;
use App\Http\Requests\User\UserRequest;
use App\Mail\WorkFromHomeRequestMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class UserWorkFormHomeController extends Controller
{
    public function index(){
         $emp_id = auth('employee')->id();

        $workformhome = WorkForm::where('user_id', $emp_id)
    ->orderBy('created_at', 'desc')
    ->get();

        return view('user.workformhome.index',compact('workformhome'));
    }
    public function create(){
        return view('user.workformhome.add');
    }
// public function store(UserRequest $request)
// {
//     $validatedData = $request->validated();

//  $emp_id = auth('employee')->id();
//     WorkForm::create([
//         'user_id' => $emp_id,
//         'work_date' => $validatedData['work_date'],
//         'start_time' => $validatedData['start_time'],
//         'end_time' => $validatedData['end_time'],
//         'reason' => $validatedData['reason'],
//         'location' => $validatedData['location'],
//         'status' => 'pending', // optional, default already set in DB
//     ]);

//     return redirect()->route('user.work.form.home')->with('success', 'Work From Home request submitted!');
// }




public function store(UserRequest $request)
{
    $validatedData = $request->validated();
    $emp_id = auth('employee')->id();

    $workForm = WorkForm::create([
        'user_id' => $emp_id,
        'work_date' => $validatedData['work_date'],
        'start_time' => $validatedData['start_time'],
        'end_time' => $validatedData['end_time'],
        'reason' => $validatedData['reason'],
        'location' => $validatedData['location'],
        'status' => 'pending',
    ]);

    $workForm->load('user');

    Mail::to('office@aurateria.com')->send(new WorkFromHomeRequestMail($workForm));

    return redirect()->route('user.work.form.home')->with('success', 'Work From Home request submitted!');
}



public function destroy($id)
{
    $WorkForm = WorkForm::find($id);

    if ($WorkForm) {
        $WorkForm->delete();

        return redirect()->route('user.work.form.home')->with('success', 'Work From Home request deleted successfully.');
    }

    return redirect()->route('user.work.form.home')->with('error', 'Work From Home request not found.');
}


}
