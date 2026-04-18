<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AddEmployeeController extends Controller
{
    public function index(){
        $loggedInUserId = Session::get('user_id');
        if (!$loggedInUserId) {
            return redirect()->route('login.form');
        }
        $users = User::where('id', '!=', $loggedInUserId)
          ->where('status', 'active') 
          ->where('role', '!=', 'admin')
        ->get();
        return view('admin.dashboard.index' , compact('users'));
    }

    

    public function create(){
        return view('admin.dashboard.adduser');
    }




    public function store(Request $request)
{   
    $request->validate([
        'name' => 'nullable|string|max:255',
        'phone_no' => 'nullable|string|max:20',
        'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'address' => 'nullable|string|max:255',
        'email' => 'required|email|unique:users,email',
         'monthly_salary' => 'nullable|numeric|min:0',
        'password' => 'required|string|min:6|max:20', 

    ]);
    $filename = null;
    if ($request->hasFile('profile_picture')) {
        $file = $request->file('profile_picture');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/profiles'), $filename);
    }
    $user = new User();
    $user->name = $request->name;
    $user->phone_no = $request->phone_no;
    $user->profile_picture = $filename;
    $user->address = $request->address;
    $user->email = $request->email;
     $user->monthly_salary = $request->monthly_salary; 
    $user->password = Hash::make($request->password);
    $user->save();

    return redirect()->back()->with('success', 'User saved successfully!');
}


public function update(Request $request, $id)
{
    $request->validate([
        'name'     => 'nullable|string|max:255',
        'email'    => 'nullable|email|unique:users,email,' . $id,
        'phone_no' => 'nullable|string|max:20',
        'address'  => 'nullable|string|max:255',
        'status'   => 'nullable|in:active,inactive',
        'profile_picture' => 'nullable|image|max:5120', 
    ]);

    $user = User::findOrFail($id);

    $user->name     = $request->name;
    $user->email    = $request->email;
    $user->phone_no = $request->phone_no;
    $user->address  = $request->address;
    $user->status   = $request->status;
    if ($request->hasFile('profile_picture')) {
        if ($user->profile_picture && file_exists(public_path('uploads/profiles/' . $user->profile_picture))) {
            unlink(public_path('uploads/profiles/' . $user->profile_picture));
        }
        $file = $request->file('profile_picture');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/profiles'), $filename);
        $user->profile_picture = $filename;
    }
    $user->save();
    return redirect()->back()->with('success', 'User updated successfully.');
}


public function destroy($id)
{
    $user = User::find($id);
    if (!$user) {
        return response()->json(['message' => 'User not found.'], 404);
    }
    if ($user->profile_picture) {
        $profilePath = public_path('uploads/profiles/' . $user->profile_picture);
        if (file_exists($profilePath)) {
            unlink($profilePath);
        }
    }
    $user->delete();

return redirect()->back();
}
}


