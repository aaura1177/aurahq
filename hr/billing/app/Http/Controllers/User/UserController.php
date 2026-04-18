<?php

namespace App\Http\Controllers\User;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json(['status' => true, 'users' => $users]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'nullable|min:6'
        ]);

        $user = User::find($request->id);
        
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();
        return response()->json(['status' => true, 'message' => 'User updated successfully']);
    }



    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id'
        ]);

        $user = User::find($request->id);

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['status' => true, 'message' => 'User deleted successfully']);
    }
}
