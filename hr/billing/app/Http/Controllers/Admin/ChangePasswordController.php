<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\PasswordOtp;

class ChangePasswordController extends Controller  
{
    public function index()
    {
         
        return view('admin.profiles.change_password');
    }

    // public function changePassword(Request $request)
    // {
    //     $request->validate([
    //         'old_password' => 'required',
    //         'new_password' => 'required|min:6|confirmed',
    //     ]);
    //     $userId = session('user_id');
    //     $user = User::find($userId);
    //     if (!$user) {
    //         return back()->withErrors(['user' => 'User not found. Please login again.']);
    //     }
    //     if (!Hash::check($request->old_password, $user->password)) {
    //         return back()->withErrors(['old_password' => 'Old password does not match.']);
    //     }
    //     $user->update([
    //         'password' => Hash::make($request->new_password),
    //     ]);
    //     return back()->with('success', 'Password updated successfully.');
    // }




      public function sendOtp(Request $request)
    {
        $userId = session('user_id'); 
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found.']);
        }

        $otp = rand(100000, 999999);

        PasswordOtp::updateOrCreate(
            ['email' => $user->email],
            [
                'otp' => $otp,
                'expires_at' => now()->addMinutes(10),
            ]
        );

        Mail::raw("Your OTP for password change is: $otp", function ($message) use ($user) {
            $message->to($user->email)->subject('Password Change OTP');
        });

        session(['reset_email' => $user->email]);

        return response()->json(['success' => true]);
    }



    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

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



     public function changePassword(Request $request)
    {
        if (!session('otp_verified')) {
            return back()->withErrors(['otp' => 'OTP verification required before resetting password.']);
        }

        $request->validate([
            'new_password' => 'required|min:6|confirmed',
        ]);

        $userId = session('user_id'); 
        $user = User::find($userId);

        if (!$user) {
            return back()->withErrors(['user' => 'User not found.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        // Clean up
        PasswordOtp::where('email', session('reset_email'))->delete();
        session()->forget(['reset_email', 'otp_verified']);

        return back()->with('success', 'Password changed successfully.');
    }




}
