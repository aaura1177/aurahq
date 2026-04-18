<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRequests;
use App\Models\Task;
use App\Models\Employee;
use App\Models\Notification;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class TaskController extends Controller
{

    public  function index()
    {
        $projects = Project::all();
        $employees = Employee::where('status', 1)->where('department_id',1)->get();
        $tasks = Task::orderBy('created_at', 'desc')->with(['project', 'employee', 'user'])->get();
        return view('admin.tasks.index', compact('tasks', 'projects', 'employees'));
    }


    public  function addtask()
    {
        $projects = Project::all();
        $employees = Employee::where('status', 1)->where('department_id',1)->get();
        return view('admin.tasks.add-task', compact('projects', 'employees'));
    }


    public function createtask(AdminRequests $request)
    {
        try {
            $id = Auth::id();
            if (!$id) {
                return redirect()->route('admin.login')->with('error', 'Login');
            }
            $data = $request->validated();
            $data['user_id'] = $id;

           $task =  Task::create($data);

            Notification::create([
                'employee_id' => $task->employee_id,
                'user_id' => $id,
                'message' => $task->name,
                'status' => 0,
                'is_read' => '0',
            ]);


            return redirect()->route('admin.tasks')->with('success', 'Task created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }


    public function edit(AdminRequests $request)
    {
        try {
            $data = $request->validated();

            $task = Task::find($data['id']);
            if (!$task) {
                return back()->with('error', 'Task not found.');
            }
            $task->update($data);
            return redirect()->route('admin.tasks')->with('success', 'Task updated successfully.');
        } catch (\Exception $e) {

            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }


    public function destroy($id)
    {
        try {
            $task = Task::findOrFail($id);
            $task->delete();

            return redirect()->route('admin.tasks')->with('success', 'Task deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}
