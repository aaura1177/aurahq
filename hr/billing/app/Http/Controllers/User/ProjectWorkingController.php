<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ProjectAttandanc;
use Illuminate\Http\Request;
use App\Models\ProjectUser;


class ProjectWorkingController extends Controller
{

public function index($project_id)
{
        $user_id = session('user_id'); 

        
    $projectUsers = ProjectAttandanc::with('project')
        ->where('project_id', $project_id)
        ->where('user_id', $user_id)
        ->whereNotNull('start_time')
        ->whereNotNull('end_time')
        ->whereNotNull('total_minutes')
        ->get();

    return view('user.user_project_working.index', compact('projectUsers'));
}

}
