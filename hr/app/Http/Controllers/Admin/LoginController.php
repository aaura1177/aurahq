<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;



class LoginController extends Controller
{
    public function login(Request $request)
    {

        try {
            if ($request->isMethod('post')) {
                $request->validate([
                    'email' => 'required|email',
                    'password' => 'required|min:6',
                ]);
    
                $credentials = $request->only('email', 'password');
    
                if (Auth::attempt($credentials)) {
                    return redirect()->intended('/admin/dashboard')->with('success', 'Login Successful');
                }    
                return back()->withInput()->with('error', 'Invalid Email or Password');
            }
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->validator);
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong! ' . $e->getMessage());
        }

        return view('admin.auth.login');
    }
}
