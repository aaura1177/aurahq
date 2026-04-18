<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRequests;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;



class AdminController extends Controller
{
    public function register(AdminRequests $request)
    {
        try {
            $validatedData = $request->validated();

            $user = User::create([
                'username' => $validatedData['username'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            return response()->json([
                'message' => 'User registered successfully',
                'data' => $user,
            ]);
        } catch (Throwable $exception) {
            return handleDatabaseError($exception);
        }
    }


    public function login(AdminRequests $request)
    {
        
        $validatedData = $request->validated();
    
        $credentials = [
            'email' => $validatedData['email'],
            'password' => $validatedData['password']
        ];
    
        $user = User::where('email', $validatedData['email'])->first();
    
        if ($user && $user->account_locked) {
            return response()->json([
                'error' => true,
                'message' => 'Your account is locked due to multiple failed login attempts. Please contact support.',
                'login_attempts' => 'Account is locked - Please contact By Admin '
            ], 403);
        }
    
        if (!$token = JWTAuth::attempt($credentials)) {
            if ($user) { 
                $user->login_attempts += 1;
    
                if ($user->login_attempts >= 10) {
                    $user->account_locked = 1;
                }    
                $user->save(); 
            }
            return response()->json([
                 'error' => true,
                'message' => 'The provided credentials do not match our records.',
                'login_attempts' => $user ? $user->login_attempts : null,
                'account_locked' => $user ? $user->account_locked : null,
            ], 401);
        }
    
        $user->login_attempts = 0;
        $user->account_locked = 0;
        $user->last_login = Carbon::now();
        $user->save();    
        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'last_login' => Carbon::parse($user->last_login)->format('Y-m-d H:i:s'),
                'last_loginssss' => $user->login_attempts,
                'account_locked' => $user->account_locked,
            ],
        ], 200);
    }
    
















}
