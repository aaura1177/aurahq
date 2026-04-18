<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserApiController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('roles')->latest()->paginate($request->query('per_page', 15));
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|string|exists:roles,name',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);
        $user->assignRole($request->role);
        return response()->json(['message' => 'Created', 'data' => $this->userJson($user)], 201);
    }

    public function show(User $user)
    {
        $user->load('roles');
        return response()->json(['data' => $this->userJson($user)]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|string|exists:roles,name',
            'password' => 'nullable|min:6',
        ]);
        if ($user->id === $request->user()->id && $request->filled('is_active') && !$request->boolean('is_active')) {
            return response()->json(['message' => 'Cannot deactivate yourself'], 422);
        }
        $user->update($request->only('name', 'email'));
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }
        $user->syncRoles([$request->role]);
        return response()->json(['message' => 'Updated', 'data' => $this->userJson($user->fresh('roles'))]);
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Cannot delete yourself'], 422);
        }
        $user->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function toggle(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Cannot block yourself'], 422);
        }
        $user->is_active = !$user->is_active;
        $user->save();
        return response()->json(['message' => 'OK', 'data' => ['is_active' => $user->is_active]]);
    }

    private function userJson(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'is_active' => $user->is_active,
            'roles' => $user->getRoleNames(),
        ];
    }
}
