<?php

namespace App\Providers;

use App\Models\Attendance;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\Task;
use Carbon\Carbon;

class EmployeeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $username = Auth::user()->username;
                $employee_id = Auth::guard('employee')->id();

                $notifications = Notification::where('employee_id', $employee_id)
                    ->where('is_read', '0')
                    ->orderBy('created_at', 'desc')
                    ->get();

                $count = $notifications->count();
            } else {
                $username = "Rahul Yadav";
                $notifications = collect();
                $count = 0;
            }

            // Define notification messages
            $notiMessages = [
                'task' => "$username has assigned you a new Task!",
            ];

            // Define notification links
            $notiLinks = [
                'task' => '/user/task',
            ];

            $employee_id = Auth::guard('employee')->id();

            $todayAttendances = Attendance::where('employee_id', $employee_id)
            ->whereDate('date', Carbon::today())
            ->get();
        
        $checkinCount = $todayAttendances->whereNotNull('check_in_time')->count();
        
        $lastIncomplete = $todayAttendances
            ->whereNotNull('check_in_time')
            ->whereNull('check_out_time')
            ->sortByDesc('id')
            ->first();



            $urgenttask = Task::where('employee_id', $employee_id)->where('status' , 'Urgent')->where('employee_status' , 'pending')->first();


            $view->with([
                'notiMessages' => $notiMessages,
                'notiLinks' => $notiLinks,
                'employee_notifications' => $notifications,
                'count' => $count,
                'checkinCount' => $checkinCount,
                'lastIncomplete' => $lastIncomplete,
                'urgenttask' => $urgenttask
            ]);
        });
    }

    public function register() {}
}
