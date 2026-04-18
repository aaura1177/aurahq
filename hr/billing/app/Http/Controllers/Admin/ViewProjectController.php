<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectAttandanc;
use Illuminate\Http\Request;
use App\Models\ProjectUser;

class ViewProjectController extends Controller
{
    
    public function index($user_id){
        
    $projectUsers =ProjectAttandanc::with('project')
     ->where('user_id', $user_id)
    ->get();

        return view('admin.admin_view_projects.index', compact('projectUsers'));
    }
}
