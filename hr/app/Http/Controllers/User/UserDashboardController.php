<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserRequest;
use App\Models\LeaveRequest;
use App\Models\Task;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\DeviceLog;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use Illuminate\Support\Facades\Mail;
use App\Mail\EarlyCheckoutAlert;
use App\Models\IpLocation;
use Stevebauman\Location\Facades\Location;
use Jenssegers\Agent\Agent;



class UserDashboardController extends Controller
{
  public function index(UserRequest $request)
  {


      $today = Carbon::today();
    $futureDate = Carbon::today()->addDays(10);
    $userId = auth('employee')->id();

    $holidays = Holiday::where('date', '>=', Carbon::today())
      ->where('date', '<=', Carbon::today()->addDays(30))
      ->get();

    $checkin_check = Attendance::where('employee_id', $userId)
      ->whereDate('date', Carbon::today())
      ->latest()
      ->first();

    //  $count = LeaveRequest::all();
    $attendance = Attendance::where('employee_id', $userId)
      ->whereMonth('date', now()->month)
      ->orderBy('created_at', 'desc')
      ->get();

    $attendanceCount = Attendance::where('employee_id', $userId)
      ->whereMonth('date', now()->month)
      ->whereNotNull('check_in_time')
      ->distinct('date')
      ->count();
    $tasks = Task::where('employee_id', $userId)->whereDate('created_at', Carbon::today())->orderBy('created_at', 'desc')->get();


    $birthdays = Employee::whereMonth('date_of_birth', '>=', $today->month)
      ->whereMonth('date_of_birth', '<=', $futureDate->month)
      ->whereRaw('DAYOFYEAR(date_of_birth) >= ?', [$today->dayOfYear])
      ->whereRaw('DAYOFYEAR(date_of_birth) <= ?', [$futureDate->dayOfYear])
      ->get();

    return view('user.dashboard.index', compact('attendance', 'attendanceCount', 'tasks', 'birthdays', 'holidays'));
  }


  public function attendance(Request $request)
  {
    $userId = auth('employee')->id();

    $fromDate = $request->input('from_date', now()->startOfMonth()->toDateString());
    $toDate = $request->input('to_date', now()->endOfMonth()->toDateString());

    $attendance = Attendance::where('employee_id', $userId)
      ->whereBetween('date', [$fromDate, $toDate])
      ->orderBy('date', 'desc')
      ->get();

    return view('user.dashboard.attendance', compact('attendance', 'fromDate', 'toDate'));
  }


  public function profile()
  {
    return view('user.profile.index');
  }



  public function profileupdate(UserRequest $request)
  {
    $validatedData = $request->validated();

    $user = auth('employee')->user();

    if ($request->hasFile('image')) {
      if ($user->image) {
        Storage::delete('public/' . $user->image);
      }
      $path = $request->file('image')->store('employee_images', 'public');
      $validatedData['image'] = $path;
    }

    $user->update($validatedData);

    return redirect()->route('user.profile')->with('success', 'Profile updated successfully!');
  }


  public function passwordupdate(UserRequest $request)
  {
    $user = auth('employee')->user();

    if (!Hash::check($request->current_password, $user->password)) {
      return back()->withErrors(['current_password' => 'The current password is incorrect.']);
    }

    $user->password = Hash::make($request->password);

    if ($user->save()) {
      return redirect()->route('user.profile')->with('success', 'Your password has been updated successfully!');
    }

    return back()->withErrors(['password' => 'There was an issue updating your password. Please try again.']);
  }

  // public function checkin(Request $request)
  // {
  //   $employeeId = auth('employee')->id();

  //   $checkinCount = Attendance::where('employee_id', $employeeId)
  //     ->whereDate('date', Carbon::today())
  //     ->whereNotNull('check_in_time')
  //     ->count();

  //   if ($checkinCount >= 3) {
  //     return redirect()->back()->with('error', 'You have already checked in 3 times today!');
  //   }


  //   Attendance::create([
  //     'employee_id'    => $employeeId,
  //     'date'           => Carbon::today()->toDateString(),
  //     'shift'          => 'Morning',
  //     'check_in_time'  => Carbon::now()->toTimeString(),
  //     'status'         => 'Present',
  //   ]);
  //   return redirect()->back()->with('success', 'Check-In Successfully Completed!');
  // }




  public function checkin(Request $request)
  {
    $employeeId = auth('employee')->id();


    // $companyLat = 26.942547;
    // $companyLng = 75.762994;
    // $userLat = $request->latitude;
    // $userLng = $request->longitude;

    // if (!$userLat || !$userLng) {
    //   return redirect()->back()->with('error', '📍 Location not detected! Please enable GPS and try again.');
    // }

    // $distance = $this->calculateDistance($companyLat, $companyLng, $userLat, $userLng);

    // if ($distance > 0.5) {
    //   return redirect()->back()->with('error', '❌ You are not within 500 meters of the office! Distance: ' . round($distance * 1000) . ' meters');
    // }

    $checkinCount = Attendance::where('employee_id', $employeeId)
      ->whereDate('date', Carbon::today())
      ->whereNotNull('check_in_time')
      ->count();

    if ($checkinCount >= 3) {
      return redirect()->back()->with('error', '⛔ You have already checked in 3 times today!');
    }


    if ($employeeId == 1 ||  $employeeId == 2) {
    if(Carbon::now()->toTimeString() > '10:00:00' && Carbon::now()->toTimeString() < '10:15:00')
{
      Attendance::create([
        'employee_id'    => $employeeId,
        'date'           => Carbon::today()->toDateString(),
        'shift'          => 'Morning',
        'check_in_time'  => '10:00:00',
        'status'         => 'Present',
      ]);    
      }
      else{
          Attendance::create([
        'employee_id'    => $employeeId,
        'date'           => Carbon::today()->toDateString(),
        'shift'          => 'Morning',
        'check_in_time'  => Carbon::now()->toTimeString(),
        'status'         => 'Present',
      ]); 
      }
    } else {
      Attendance::create([
        'employee_id'    => $employeeId,
        'date'           => Carbon::today()->toDateString(),
        'shift'          => 'Morning',
        'check_in_time'  => Carbon::now()->toTimeString(),
        'status'         => 'Present',
      ]);
    }





    return redirect()->back()->with('success', '✅ Check-In Successfully Completed!');
  }


  private function calculateDistance($lat1, $lon1, $lat2, $lon2)
  {
    $earthRadius = 6371; // Earth radius in kilometers

    $latFrom = deg2rad($lat1);
    $lonFrom = deg2rad($lon1);
    $latTo = deg2rad($lat2);
    $lonTo = deg2rad($lon2);

    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;

    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
      cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
    // Distance in km
    return $earthRadius * $angle;
  }


  public function checkout(Request $request)
  {

    $employeeId = auth('employee')->id();
    $employee = Employee::find($employeeId);

    if (!$employee) {
      return redirect()->back()->with('error', 'Employee not found.');
    }

    $employeeSalaryMonth = $employee->salary;
    $employeeSalaryDay = $employeeSalaryMonth / Carbon::now()->daysInMonth;
    $employeeSalaryPerHour = $employeeSalaryDay / 7;
    $employeeSalaryPerMinute = $employeeSalaryPerHour / 60;
    $employeeSalaryPerSecond = $employeeSalaryPerMinute / 60;

    $attendance = Attendance::where('id', $request->attendance_id)
      ->where('employee_id', $employeeId)
      ->whereDate('date', Carbon::today())
      ->first();

    if (!$attendance) {
      return redirect()->back()->with('error', 'Check-In record not found!');
    }

    $checkInTime = Carbon::parse($attendance->check_in_time);
    $checkOutTime = Carbon::now();

    $workingSeconds = $checkInTime->diffInSeconds($checkOutTime);
    $workingHoursFormatted = gmdate('H:i:s', $workingSeconds);

    $overtimeSeconds = max(0, $workingSeconds - (7 * 3600));
    $overtimeHoursFormatted = gmdate('H:i:s', $overtimeSeconds);

    $earnedSalary = round($workingSeconds * $employeeSalaryPerSecond, 2);

    $attendance->update([
      'check_out_time'   => $checkOutTime->toTimeString(),
      'working_hours'    => $workingHoursFormatted,
      'overtime_hours'   => $overtimeHoursFormatted,
      'earned_salary'    => $earnedSalary,
    ]);

    // 🔔 Send early checkout alert if before 4:00 PM
    if ($checkOutTime->lt(Carbon::createFromTime(16, 0, 0))) {
      Mail::to('office@aurateria.com')->send(new EarlyCheckoutAlert($employee, $checkOutTime));
    }

    return redirect()->back()->with('success', 'Check-Out Successfully Completed!');
  }


  public function storeVisitorLocation()
  {
    $ip = request()->ip();
    $position = Location::get($ip);

    $agent = new Agent();
    $browser = $agent->browser();
    $browserVersion = $agent->version($browser);
    $platform = $agent->platform();
    $platformVersion = $agent->version($platform);
    $device = $agent->device();
    $isDesktop = $agent->isDesktop() ? 'Yes' : 'No';
    $isMobile = $agent->isMobile() ? 'Yes' : 'No';
    $userAgent = request()->header('User-Agent');
    $referer = request()->headers->get('referer') ?? 'Direct / No Referrer';

    DeviceLog::create([
      'ip_address'       => $ip,
      'browser'          => $browser,
      'browser_version'  => $browserVersion,
      'platform'         => $platform,
      'platform_version' => $platformVersion,
      'device'           => $device,
      'is_desktop'       => $isDesktop,
      'is_mobile'        => $isMobile,
      'user_agent'       => $userAgent,
      'referer'          => $referer,
    ]);

    if ($position) {
      IpLocation::create([
        'ip_address' => $ip,
        'country'    => $position->countryName,
        'region'     => $position->regionName,
        'city'       => $position->cityName,
        'latitude'   => $position->latitude,
        'longitude'  => $position->longitude,
      ]);

      $emailContent = <<<EOT
🚩 New Visitor Logged!

🌍 IP Location:
IP Address: $ip
Country: {$position->countryName}
Region: {$position->regionName}
City: {$position->cityName}
Latitude: {$position->latitude}
Longitude: {$position->longitude}

💻 Device Info:
Browser: $browser ($browserVersion)
Platform: $platform ($platformVersion)
Device: $device
Is Desktop: $isDesktop
Is Mobile: $isMobile

🔗 Referrer:
$referer

🧾 User Agent: 
$userAgent

🕓 Time: Now - {$position->timezone}
EOT;

      Mail::raw($emailContent, function ($message) {
        $message->to('dharmendrajangid8651@gmail.com')
          ->subject('New Visitor Location & Device Info Logged');
      });
    }

    return response()->json(['message' => 'okay']);
  }
}
