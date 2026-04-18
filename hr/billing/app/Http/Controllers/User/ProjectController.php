<?php

namespace App\Http\Controllers\User;
use App\Models\AdminProject;
use App\Models\User;
use App\Models\ProjectAttandanc;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkLog;
use App\Models\HourlyRates;

use App\Models\ProjectUser;

use Illuminate\Support\Facades\Session;

class ProjectController extends Controller
{
    

    public function index(){



        $userId = session('user_id'); 
    
        $projects = AdminProject::where('user_id', $userId)
        
                                 ->select('id', 'user_id', 'title', 'description', 'start_date', 'end_date', 'status', 'attachment')
                                 ->with('user')  
                                 ->get();
    
        return view('user.projects.index' , data: compact('projects'));
    }



    public function project()
{
    $userId = session('user_id');

    if (!$userId) {
        return redirect()->route('user');
    }

    $userprojects = AdminProject::whereHas('projectUsers', function ($query) use ($userId) {
        $query->where('user_id', $userId);
    })
    ->with(['projectUsers' => function ($query) use ($userId) {
        $query->where('user_id', $userId);
    }])
    ->get();

    foreach ($userprojects as $project) {
        foreach ($project->projectUsers as $user) {
            $totalSeconds = ProjectAttandanc::where('project_id', $project->id)
                ->where('user_id', $user->user_id)
                ->selectRaw('SUM(TIME_TO_SEC(total_minutes)) as total_seconds')
                ->value('total_seconds');

            $totalSeconds = $totalSeconds ?? 0;

            $hours = floor($totalSeconds / 3600);
            $minutes = floor(($totalSeconds % 3600) / 60);
            $seconds = $totalSeconds % 60;

            $user->formatted_time = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }
    }

    return view('user.projects.project_listing', [
        'userprojects' => $userprojects,
    ]);
}




















// public function project()
// {
//     $userId = session('user_id'); 

//     if (!$userId) {
//         return redirect()->route('user');
//     }

//     $userprojects = AdminProject::whereHas('projectUsers', function ($query) use ($userId) {
//         $query->where('user_id', $userId);
//     })
//     ->with(['projectUsers' => function ($query) use ($userId) {
//         $query->where('user_id', $userId);
//     }])
//     ->get();

//     // Attach total attendance time (HH:MM:SS) for each project
//     foreach ($userprojects as $project) {
        
//         $totalSeconds = ProjectAttandanc::where('project_id', $project->id)
//             ->where('user_id', $userId)
//             ->selectRaw('SUM(TIME_TO_SEC(total_minutes)) as total_seconds')
//             ->value('total_seconds');

//         $totalSeconds = $totalSeconds ?? 0;

//         $hours = floor($totalSeconds / 3600);
//         $minutes = floor(($totalSeconds % 3600) / 60);
//         $seconds = $totalSeconds % 60;

//         $project->formatted_time = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
//     }

//     return view('user.projects.project_listing', compact('userprojects'));
// }


//     public function project()
// {
//     $userId = session('user_id'); 

//     if (!$userId) {
//         return redirect()->route('user');
//     }

//     $userprojects = AdminProject::whereHas('projectUsers', function ($query) use ($userId) {
//         $query->where('user_id', $userId);
//     })
//     ->with(['projectUsers' => function ($query) use ($userId) {
//         $query->where('user_id', $userId);
//     }])
//     ->get();

//     return view('user.projects.project_listing', compact('userprojects'));
// }

    

public function checkin(Request $request)
{
    $userId = session('user_id'); 
    $projectId = $request->input('project_id'); 
    $now = Carbon::now('Asia/Kolkata');
    $todayDate = $now->toDateString();


    $latestRate = HourlyRates::where('user_id', $userId)
        ->where('project_id', $projectId)
        ->where('date', '<=', $todayDate)
        ->orderByDesc('date')
        ->first();

    if (!$latestRate) {
        return redirect()->back()->with('error', 'No hourly rate found for this user and project.');
    }

    
    $nullStartTimeRow = ProjectAttandanc::where('user_id', $userId)
        ->where('project_id', $projectId)
        ->whereNull('start_time')
        ->whereDate('date', $todayDate)
        ->latest()
        ->first();

    if ($nullStartTimeRow) {
        $nullStartTimeRow->update([
            'start_time' => $now->format('H:i:s'),
            'h_rate' => $latestRate->h_rate,
            'm_rate' => number_format($latestRate->m_rate, 2),
        ]);

        return redirect()->back()->with('success', 'Check-In completed by updating existing empty row.');
    }

    
    $activeSession = ProjectAttandanc::where('user_id', $userId)
        ->where('project_id', $projectId)
        ->whereDate('date', $todayDate)
        ->whereNull('end_time')
        ->latest()
        ->first();

    if ($activeSession) {
        $startTime = Carbon::parse($activeSession->start_time);
        $totalMinutes = $startTime->diffInMinutes($now);
        $totalAmount = round($totalMinutes * $activeSession->m_rate, 2);

        $activeSession->update([
            'end_time' => $now->format('H:i:s'),
            'total_minutes' => $totalMinutes,
            'total_amount' => $totalAmount,
        ]);
    }

    
    ProjectAttandanc::create([
        'user_id' => $userId,
        'project_id' => $projectId,
        'h_rate' => $latestRate->h_rate,
        'm_rate' => number_format($latestRate->m_rate, 2),
        'start_time' => $now->format('H:i:s'),
        'end_time' => null,
        'total_minutes' => null,
        'total_amount' => null,
        'date' => $todayDate,
    ]);

    return redirect()->back()->with('success', 'Check-In Successfully Completed!');
}





public function checkout(Request $request)
{
    $userId = session('user_id');
    $now = Carbon::now('Asia/Kolkata');

    $record = ProjectAttandanc::where('user_id', $userId)
        ->whereNull('total_minutes')
        ->latest()
        ->first();

    if (!$record) {
        return redirect()->back()->with('error', 'No active check-in found.');
    }

    $startTime = Carbon::parse($record->start_time);
    $totalSeconds = $startTime->diffInSeconds($now);

    // Convert to HH:MM:SS for TIME column
    $totalFormattedTime = gmdate('H:i:s', $totalSeconds);

    // Decimal minutes for amount calculation
    $totalMinutesDecimal = round($totalSeconds / 60, 2);
    $totalAmount = round($record->m_rate * $totalMinutesDecimal, 2);

    // Update attendance record
    $record->update([
        'end_time'       => $now->format('H:i:s'),
        'total_minutes'  => $totalFormattedTime, // ✅ Save as TIME
        'total_amount'   => $totalAmount,
    ]);

    // Update project user summary
    $projectUser = ProjectUser::where('user_id', $userId)
        ->where('project_id', $record->project_id)
        ->first();

    if ($projectUser) {
        $projectUser->update([
            'total_amount'   => $projectUser->total_amount + $totalAmount,
            'pending_amount' => $projectUser->pending_amount + $totalAmount,
        ]);
    }

    return redirect()->back()->with('success', 'Check-Out Successfully Completed!');
}












// public function checkout(Request $request)
// {
//     $userId = session('user_id');
//     $now = Carbon::now('Asia/Kolkata');

//     $record = ProjectAttandanc::where('user_id', $userId)
//         ->whereNull('end_time')
//         ->latest()
//         ->first();

//     if (!$record) {
//         return redirect()->back()->with('error', 'No active check-in found.');
//     }

//     $startTime = Carbon::parse($record->start_time);
//     $totalSeconds = $startTime->diffInSeconds($now);
//     $totalMinutes = round($totalSeconds / 60, 2);
//     $totalAmount = round($record->m_rate * $totalMinutes, 2);

//     $record->update([
//         'end_time' => $now->format('H:i:s'),
//         'total_minutes' => $totalMinutes,
//         'total_amount' => $totalAmount,
//     ]);

    
//     $projectUser = ProjectUser::where('user_id', $userId)
//         ->where('project_id', $record->project_id)
//         ->first();

//     if ($projectUser) {
//         $newTotalAmount = $projectUser->total_amount + $totalAmount;
//         $newPendingAmount = $projectUser->pending_amount + $totalAmount;

//         $projectUser->update([
//             'total_amount' => $newTotalAmount,
//             'pending_amount' => $newPendingAmount,
//         ]);
//     }

//     return redirect()->back()->with('success', 'Check-Out Successfully Completed!');
// }




 public function updatelist(Request $request, $id)
 {
     
     $request->validate([
         'status' => 'required|in:Pending,In Progress,Completed',
     ]);
 
     
     $project = AdminProject::find($id);
     if (!$project) {
         return redirect()->back()->with('error', 'Project not found.');
     }
 

     $project->status = $request->status;
     $project->save();
 
     return redirect()->back()->with('success', 'Project status updated successfully.');
 }
 

    public function update(Request $request, $id)
{
    
     $project = AdminProject::find($id);
    if ($request->status == 'In Progress') {
        AdminProject::where('user_id', $project->user_id)
               ->where('status', '!=', 'Completed')  
               ->update(['status' => 'Pending']);
    }
    $project->status = $request->status;
    $project->save();

    return redirect()->route('project.index');
}

public function destroy($id)
{
    $project = AdminProject::find($id);

    if (!$project) {
        return redirect()->back()->with('error', 'Project not found.');
    }

    $project->delete();

    return redirect()->route('project.index')->with('success', 'Project deleted successfully.');
}


}
