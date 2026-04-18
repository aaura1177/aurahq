<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ProjectAttandanc;
use App\Models\ProjectUser;

class BillesController extends Controller
{
  public function index(){
        // $users = User::all();
     $users = User::where('role', '!=', 'admin')
        ->where('status', 'active') 
     ->get();
        return view('admin.admin_billes.index', compact('users'));
    }

//   public function getProjectUserData($user_id)
// {       
//     $data = ProjectUser::with('project')
//         ->where('user_id', $user_id)
//         ->get();
//     return view('admin.admin_billes.projects_total', compact('data', 'user_id'));
// }

public function getProjectUserData($user_id)
{
    $projectUsers = ProjectUser::with('project')
        ->where('user_id', $user_id)
        ->get();

    foreach ($projectUsers as $item) {
       
        $attendances = ProjectAttandanc::where('user_id', $user_id)
            ->where('project_id', $item->project_id)
            ->get();

        $totalSeconds = 0;

        foreach ($attendances as $attendance) {
            if ($attendance->total_minutes) {
           
                $parts = explode(':', $attendance->total_minutes);
                $hours = isset($parts[0]) ? (int)$parts[0] : 0;
                $minutes = isset($parts[1]) ? (int)$parts[1] : 0;
                $seconds = isset($parts[2]) ? (int)$parts[2] : 0;

                $totalSeconds += ($hours * 3600) + ($minutes * 60) + $seconds;
            }
        }

        $item->total_time = gmdate('H:i:s', $totalSeconds);
    }
    return view('admin.admin_billes.projects_total', [
        'data' => $projectUsers,
        'user_id' => $user_id
    ]);
}


public function deleteProjectUser($id)
{
    $projectUser = ProjectUser::findOrFail($id);
    $projectUser->delete();

    return redirect()->back()->with('success', 'Project user deleted successfully.');
}

}
