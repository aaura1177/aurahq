<?php

namespace App\Http\Controllers\Admin;
use App\Models\AdminProject;
use App\Models\User;
use App\Models\ProjectUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


class AdminProjectController extends Controller
{
  
public function index()
{
    $projects = AdminProject::select('projects.id', 'projects.user_id', 'projects.title', 'projects.description', 
    'projects.start_date','projects.end_date', 'projects.client_name', 
    'projects.status', 'projects.attachment', 
    // DB::raw('COALESCE(SEC_TO_TIME(SUM(TIME_TO_SEC(project_attendance.total_minutes))), "00:00:00") as formatted_working_time')
    )
    ->leftJoin('project_user', 'projects.id', '=', 'project_user.project_id')  
    ->groupBy('projects.id', 'projects.user_id', 'projects.title', 'projects.description', 'projects.start_date', 'projects.end_date', 'projects.client_name', 'projects.status', 'projects.attachment')  
    ->with('user')  
    ->get();
    $users = User::all(); 
    return view('admin.admin_projects.index', compact('projects', 'users'));

}

    public function createproject(){

        $users = User::all();
        return view('admin.admin_projects.addproject', compact('users'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'client_name' => 'required|string|max:255',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
            'status' => 'nullable|in:in_progress,completed,pending,on_hold',
            'user_id' => 'nullable|exists:users,id', 
        ]);
        $attachment = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $folderName = 'uploads/attachment'; 
            $destinationPath = public_path($folderName);
            
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true); 
            }
            $attachment = $folderName . '/' . $file->getClientOriginalName();
            $file->move($destinationPath, $attachment);
        }
    $userId = session('user_id'); 
    if (!$userId) {
        return redirect()->route('login.form')->with('error', 'Please log in to create a project.');
    }
        $project = AdminProject::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'client_name' => $request->input('client_name'),
            'attachment' => $attachment,
            'status' => $request->input('status'),
             'user_id' => $request->input('user_id'), 
        ]);

        return redirect()->route('admin.project')->with('success', 'Project created successfully!');
    }


 public function update(Request $request, $id)
    {   
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'client_name' => 'nullable|string',
            'status' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg|max:10240',
        ]);

        $project = AdminProject::findOrFail($id);
        $project->fill($validated); 
        if ($request->hasFile('attachment')) {
            
            if ($project->attachment && file_exists(public_path($project->attachment))) {
                unlink(public_path($project->attachment));
            }
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/attachment'), $filename);
            $project->attachment = 'uploads/attachment/' . $filename;
        }
        $project->save();
        return redirect()->route('admin.project')->with('success', 'Project updated successfully');
    }


    public function destroy($id)
{
    $project = AdminProject::find($id);
    if (!$project) {
        return response()->json(['message' => 'Project not found.'], 404);
    }
    if ($project->attachment) {
        
        $filePath = storage_path('app/public/uploads/attachment/' . $project->attachment);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
    $project->delete();
    return redirect()->back()->with('success', 'Project deleted successfully!');
}




  public function statusupdate(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:Pending,In Progress,Completed,active,inactive',
    ]); 
    $projectUser = ProjectUser::findOrFail($id);
    $projectUser->status = $request->status;
    $projectUser->save();
    return back()->with('success', 'Project status updated successfully.');
}



public function assingdestroy($id)
{
    
    $projectUser = ProjectUser::findOrFail($id);
    $projectUser->delete();
    return redirect()->route('assinged.projects')->with('success', 'Project user deleted successfully!');
}
    

public function userdestroy($id)
{
    $projectUser = ProjectUser::findOrFail($id);
    $projectUser->delete();
    return redirect()->route('assing.user')->with('success', 'Project user deleted successfully!');
}
}
