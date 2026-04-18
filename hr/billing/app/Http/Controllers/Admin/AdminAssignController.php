<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AdminProject;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ProjectUser;
use App\Models\ProjectAttandanc;
use Illuminate\Support\Facades\DB;

class AdminAssignController extends Controller
{
    
    public function index(){
    
        return view('admin.admin_projects.assingproject');
    }

public function time($project_id)
{
     $projectUsers= ProjectAttandanc::with(['user', 'project'])
     ->where('project_id', $project_id)
    ->get();
    
    return view('admin.admin_projects.projects', compact('projectUsers'));
}


public function showusers($project_id)
{
    
    $assignedUserIds = ProjectUser::where('project_id', $project_id)->pluck('user_id');

    $users = User::whereNotIn('id', $assignedUserIds)
        ->where('status', 'active') 
        ->where('role', '!=', 'admin')
        ->get();

    
    $assignedUsers = ProjectUser::where('project_id', $project_id)
        ->with(['user:id,name']) 
        ->get()
 ->map(function ($assigned) use ($project_id) {
    
    $totalSeconds = DB::table('project_attendance')
        ->where('project_id', $project_id)
        ->where('user_id', $assigned->user_id)
        ->selectRaw('SUM(TIME_TO_SEC(total_minutes)) as total_seconds')
        ->value('total_seconds');

    $totalSeconds = $totalSeconds ?? 0;


    $hours = floor($totalSeconds / 3600);
    $minutes = floor(($totalSeconds % 3600) / 60);
    $seconds = $totalSeconds % 60;

    $assigned->formatted_time = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

    return $assigned;
});

    return view('admin.admin_projects.assingproject', compact('assignedUsers', 'users', 'project_id'));
}



public function assignUser(Request $request)
{
    $request->validate([
        'project_id' => 'required',
        'user_id'    => 'required|exists:users,id',
    ]);
    $alreadyAssigned = ProjectUser::where('project_id', $request->project_id)
        ->where('user_id', $request->user_id)
        ->exists();

    if ($alreadyAssigned) {
        return back()->with('error', 'This user is already assigned to the project.');
    }
    ProjectUser::create([
        'user_id'    => $request->user_id,
        'project_id' => $request->project_id,
    ]);
    return back()->with('success', 'User assigned successfully.');
}




public function deleteUser($id)
{
    $assignedUser = ProjectUser::find($id);

    if (!$assignedUser) {
        return redirect()->back()->with('error', 'User not found');
    }

    $assignedUser->delete();

    return redirect()->back()->with('success', 'User assignment deleted successfully');
}


}
