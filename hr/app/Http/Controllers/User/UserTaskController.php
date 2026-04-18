<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Task;  
use Illuminate\Http\Request;
use App\Http\Requests\User\UserRequest;

class UserTaskController extends Controller
{
    public function index()
    {

    $userId = auth('employee')->id();

        $tasks = Task::orderBy('created_at', 'desc')->where('employee_id' , $userId)->get();

        return view('user.task.index', compact('tasks'));
    }
    public function edittask(UserRequest $request)
    {
        $validatedData = $request->validated();

        
        $task = Task::findOrFail($validatedData['id']);

       
        $task->employee_status = $validatedData['employee_status'];
        $task->remark = $validatedData['remark'];

     
        $task->save();

        
        return redirect()->route('user.task')->with('success', 'Task status updated successfully');
    }
}
