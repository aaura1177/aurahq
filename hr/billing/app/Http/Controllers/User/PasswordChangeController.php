<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class PasswordChangeController extends Controller
{
    //
    public function index()
    {
        return view('user.profile.passwordchange');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);


        $userId = session('user_id');
        $user = User::find($userId);

        if (!$user) {
            return back()->withErrors(['user' => 'User not found. Please login again.']);
        }        
        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'Old password does not match.']);
        }


        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

            // session()->forget('user_id');
                session()->flush();

        return  redirect()->route('user')->with('success', 'Password updated successfully.');
    }
}
