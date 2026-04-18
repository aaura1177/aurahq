<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

    
    if (!Session::has('user_id')) {
            return redirect('/admin/login')->with('error', 'Access denied.');
        }


        $user = User::find(Session::get('user_id'));

        if (!$user || $user->role !== 'admin') {
            return redirect('/admin/login')->with('error', 'You are not authorized to access this page.');
        }

        return $next($request);
    }
}
