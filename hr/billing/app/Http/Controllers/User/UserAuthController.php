<?php

namespace App\Http\Controllers\User;
use App\Models\User;
use App\Models\PasswordOtp;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Validator;


class UserAuthController extends Controller
{

    public function index(){
       
        return view('user.user-login.user-login');
    }


public function login(Request $request)
{
    
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required|min:6',
    ]);

    $user = User::where('email', $request->email)->first();

    if ($user && Hash::check($request->password, $user->password)) {
        
        Session::put('user_id', $user->id);
        Session::put('user_name', $user->name);
        Session::put('profile_picture', $user->profile_picture);

        return redirect()->route('project.listing');
    }

    return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
}




public function logout(Request $request)
{
    
    $request->session()->forget('user_id');


    // $request->session()->flush();


    return redirect('/user/login')->with('success', 'You have been logged out successfully.');
}




    public function showForgotForm()
    {
        return view('user.user-login.user_forget_password');
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Email not registered.']);
        }

        $otp = rand(100000, 999999);
        PasswordOtp::updateOrCreate(
            ['email' => $user->email],
            ['otp' => $otp, 'expires_at' => now()->addMinutes(10)]
        );

        Mail::raw("Your OTP for resetting password is: $otp", function ($msg) use ($user) {
            $msg->to($user->email)->subject('Reset Password OTP');
        });

        session(['reset_email' => $user->email]);

        return response()->json(['success' => true]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);
        $otpRecord = PasswordOtp::where('email', session('reset_email'))
            ->where('otp', $request->otp)
            ->where('expires_at', '>=', now())
            ->first();

        if (!$otpRecord) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired OTP.']);
        }

        session(['otp_verified' => true]);
        return response()->json(['success' => true]);
    }

    public function resetPassword(Request $request)
    {
        if (!session('otp_verified') || !session('reset_email')) {
            return redirect()->route('forgot.form')->withErrors(['otp' => 'OTP verification required.']);
        }

        $request->validate([
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = User::where('email', session('reset_email'))->first();
        if (!$user) {
            return redirect()->route('forgot.form')->withErrors(['email' => 'User not found.']);
        }

        $user->update(['password' => Hash::make($request->new_password)]);
        PasswordOtp::where('email', $user->email)->delete();
        session()->forget(['reset_email', 'otp_verified']);

        return redirect()->route('user')->with('success', 'Password reset successfully.');
    }


}





