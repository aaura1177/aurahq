<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\HourlyRates;
use App\Models\ProjectAttandanc;
use App\Models\AdminProject;

class RateController extends Controller
{
    //
    public function index()  {
        $hourlyRates = HourlyRates::with(['user', 'project'])
    ->orderBy('id', 'desc')
    ->get();
        return view('admin.hourly_rate.index'  , compact('hourlyRates'));        
    }


public function showadd(){
       $users = User::select('id', 'name')
          ->where('status', 'active') 
         ->where('role', '!=', 'admin')
       ->get();
        $projects = AdminProject::select('id', 'title')->get();
        return view('admin.hourly_rate.add_rate',compact('users', 'projects'));        
}
public function store(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'project_id' => 'required|exists:projects,id',
        'h_rate' => 'required|numeric|min:0',
        'date' => 'required|date',
    ]);

    $mRate = round($request->h_rate / 60, 2);

    $hourlyRate = new HourlyRates();
    $hourlyRate->user_id = $request->user_id;
    $hourlyRate->project_id = $request->project_id;
    $hourlyRate->h_rate = $request->h_rate;
    $hourlyRate->m_rate = $mRate;
    $hourlyRate->date = $request->date;
    $hourlyRate->save();
    
    $attendance = ProjectAttandanc::firstOrNew([
        'user_id' => $request->user_id,
        'project_id' => $request->project_id,
        'date' => $request->date,
    ]);

    $attendance->h_rate = $request->h_rate;
    $attendance->m_rate = $mRate;
    $attendance->save();

    return redirect()->route('admin.hourly.rate')->with('success', 'Hourly rate added and attendance rates updated successfully!');
}

public function ratedelete($id)
{
    
    $hourlyRate = HourlyRates::findOrFail($id);
    ProjectAttandanc::where('user_id', $hourlyRate->user_id)
        ->where('project_id', $hourlyRate->project_id)
        ->whereDate('date', $hourlyRate->date)
        ->delete();
    $hourlyRate->delete();

    return redirect()->back()->with('success', 'Hourly rate and related project attendance deleted successfully.');
}


}
