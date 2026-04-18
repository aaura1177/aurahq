<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Http\Requests\User\UserRequest;

class UserLoginController extends Controller
{

    public function index()
    {
        return view('user.auth.index');
    }
public function login(UserRequest $request)
{
    if ($request->isMethod('post')) {
        $credentials = $request->validated();

        $employee = Employee::where('email', $request->email)->first();

        if (!$employee) {
            return back()->with('error', 'Invalid credentials');
        }

        if ($employee->status != 1) {
            return back()->with('error', 'Your account is inactive');
        }

        if (Auth::guard('employee')->attempt($credentials)) {
            return redirect()->route('user.dashboard')
                             ->with('success', 'Login successful!');
        }

        return back()->with('error', 'Invalid credentials');
    }
}



    public function logout(UserRequest $request)
{

    Auth::guard('employee')->logout();

    // $request->session()->invalidate();
    // $request->session()->regenerateToken();

    return redirect()->route('user.login')->with('success', 'Logged out successfully.');
}
}
