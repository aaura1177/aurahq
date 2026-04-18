<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    //
    
    public function index()
    {
        $userId = session('user_id'); 
        $user = User::findOrFail($userId);
        return view('user.profile.userprofile', compact('user'));
    }

    public function create()
    {
        $userId = session('user_id');
        $user = User::findOrFail($userId);
        return view('user.profile.userprofile', compact('user'));
    }

    
    public function update(Request $request)
    {
        $userId = session('user_id');
        $user = User::findOrFail($userId);

        $request->validate([
            'name' => 'required',
            'phone_no' => 'required',
            'address' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:5120',
        ]);
       
        // if ($request->hasFile('profile_picture')) {
        //     if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
        //         Storage::disk('public')->delete($user->profile_picture);
        //     }
        //     $profilePath = $request->file('profile_picture')->store('profiles', 'public');
        //     $user->profile_picture = $profilePath;
        // }


          if ($request->hasFile('profile_picture')) {
        $oldImagePath = public_path('uploads/profiles/' . $user->profile_picture);
        if ($user->profile_picture && file_exists($oldImagePath)) {
            unlink($oldImagePath);
        }

        $file = $request->file('profile_picture');
        $filename = time() . '_' . $file->getClientOriginalName(); 
        $file->move(public_path('uploads/profiles'), $filename);

        // Save only the filename in DB
        $user->profile_picture = $filename;
    }
        $user->update([
            'name' => $request->name,
            'phone_no' => $request->phone_no,
            'address' => $request->address,
            'email' => $request->email,
        ]);
        return redirect()->route('updated.user.profile')->with('success', 'Profile updated successfully!');
    }

}
