<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiJson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileApiController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        return ApiJson::ok([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->user()->id,
        ]);
        $user = $request->user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return ApiJson::ok([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ], 'Profile updated');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        if (! Hash::check($request->current_password, $request->user()->password)) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => ['current_password' => ['Current password is incorrect.']],
            ], 422);
        }
        $request->user()->update(['password' => $request->password]);

        return ApiJson::ok([], 'Password updated');
    }
}
