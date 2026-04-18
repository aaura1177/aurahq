<?php

namespace App\Http\Controllers\User;
use App\Models\User;
use App\Models\Attendance;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
class UserViewController extends Controller
{



public function index($user_id)
{
  
    $user = User::findOrFail($user_id);
    
       $today = now()->toDateString();

    // Check if user already has a checkin today
    $hasCheckedInToday = Attendance::where('user_id', $user_id)
                        ->where('attendance_date', $today)
                        ->exists();

    if (!$hasCheckedInToday) {

        $user->increment('checkin_count');
    }



    $attendances = Attendance::where('user_id', $user_id)
        ->get();
    return view('user.user_attandance.index', compact('user', 'attendances'));
}





public function usercheckin(Request $request)
{
    $userId = session('user_id'); 
    $now = Carbon::now('Asia/Kolkata');
    $today = $now->toDateString();

    // Aaj ke din ka attendance record dhundo
    $attendance = Attendance::where('user_id', $userId)
        ->where('attendance_date', $today)
        ->first();

    if ($attendance) {
        // Agar checkin_time null hai, toh update karo aur checkin_count increment karo
        if (!$attendance->checkin_time) {
            $attendance->increment('checkin_count');  // checkin_count increment
            $attendance->update([
                'checkin_time' => $now->format('H:i:s'),
                'start_time'   => $now->format('H:i:s'),
                'status'       => 'Present',
            ]);

            return redirect()->back()->with('success', 'Checked in by updating today\'s attendance and incrementing check-in count.');
        }

        if (!$attendance->checkout_time) {
            $startTime = Carbon::parse($attendance->start_time);
            $totalSeconds = $startTime->diffInSeconds($now);
            $totalHours = gmdate('H:i:s', $totalSeconds);

            $attendance->update([
                'checkout_time' => $now->format('H:i:s'),
                'end_time'       => $now->format('H:i:s'),
                'total_hours'    => $totalHours,
                'status'         => 'Absent',
            ]);
        }
    } else {
        // Naya record create karo aur checkin_count ko 1 set karo
        Attendance::create([
            'user_id'         => $userId,
            'attendance_date' => $today,
            'checkin_time'    => $now->format('H:i:s'),
            'start_time'      => $now->format('H:i:s'),
            'checkin_count'   => 1,           
            'status'          => 'Present',
        ]);
    }

    return redirect()->back()->with('success', 'Checked in successfully!');
}



//    public function usercheckin(Request $request)
// {
//     $userId = session('user_id'); 
//     $now = Carbon::now('Asia/Kolkata');

    
//     $today = $now->toDateString();
//     $attendance = Attendance::where('user_id', $userId)
//         ->where('attendance_date', $today)
//         ->first();

//     if ($attendance && !$attendance->checkin_time) {
//         $attendance->update([
//             'checkin_time' => $now->format('H:i:s'),
//             'start_time'   => $now->format('H:i:s'),
//             'status'       => 'Present',
//         ]);

//         return redirect()->back()->with('success', 'Checked in by updating today\'s empty row.');
//     }

    
//     if ($attendance && !$attendance->checkout_time) {
//         $startTime = Carbon::parse($attendance->start_time);
//         $totalSeconds = $startTime->diffInSeconds($now);
//         $totalHours = gmdate('H:i:s', $totalSeconds);

//         $attendance->update([
//             'checkout_time' => $now->format('H:i:s'),
//             'end_time'       => $now->format('H:i:s'),
//             'total_hours'    => $totalHours,
//             'status'         => 'Absent',
//         ]);
//     }


//     Attendance::create([
//         'user_id'         => $userId,
//         'attendance_date' => $today,
//         'checkin_time'    => $now->format('H:i:s'),
//         'start_time'      => $now->format('H:i:s'),
//         'status'          => 'Present',
//     ]);

//     return redirect()->back()->with('success', 'Checked in successfully!');
// }


public function usercheckout(Request $request)
{
    $userId = session('user_id'); 

    if (!$userId) {
        return redirect()->back()->with('error', 'Please login first.');
    }

    $today = now()->toDateString();

  $attendance = Attendance::where('user_id', $userId)
        ->where('attendance_date', $today)
        ->whereNotNull('checkin_time')
        ->whereNull('checkout_time')
        ->first();

    if (!$attendance) {
        return redirect()->back()->with('error', 'No active check-in found for today.');
    }

    $checkoutTime = now()->format('H:i:s');
    $attendance->checkout_time = $checkoutTime;

    $start = Carbon::parse($attendance->checkin_time);
    $end = Carbon::parse($checkoutTime);

    $totalSeconds = $start->diffInSeconds($end);

    $hours = floor($totalSeconds / 3600);
    $minutes = floor(($totalSeconds % 3600) / 60);
    $seconds = $totalSeconds % 60;

    $attendance->total_hours = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    $attendance->status = 'Present';

    $attendance->save();

    return redirect()->back()->with('success', 'Checked out successfully!');
}






}
