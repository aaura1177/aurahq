<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {


        if (!Session::has('user_id')) {
            return redirect('/user/login')->with('error', 'Access denied.');
        }
    
        $user = User::find(Session::get('user_id'));

        
        if (!$user || $user->role !== 'user'|| $user->status !== 'active') {

            // Session::forget('user_id');
            return redirect('/user/login')->with('error', 'Your account is inactive.');
        }

        return $next($request);
    }
}
