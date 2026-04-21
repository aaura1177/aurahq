<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiJson;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserApiController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 25);
        $perPage = $perPage > 0 && $perPage <= 100 ? $perPage : 25;
        $users = User::with('roles')->latest()->paginate($perPage);

        return ApiJson::paginated($users, fn (User $u) => $this->userJson($u));
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
        return ApiJson::created($this->userJson($user), 'User created successfully');
    }

    public function show(User $user)
    {
        $user->load('roles');
        return ApiJson::ok($this->userJson($user));
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
            return response()->json([
                'message' => 'Validation failed',
                'errors' => ['is_active' => ['You cannot deactivate yourself.']],
            ], 422);
        }
        $user->update($request->only('name', 'email'));
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }
        $user->syncRoles([$request->role]);
        return ApiJson::ok($this->userJson($user->fresh('roles')), 'Updated');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => ['user' => ['You cannot delete yourself.']],
            ], 422);
        }
        $user->delete();

        return ApiJson::noContent();
    }

    public function toggle(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => ['user' => ['You cannot block yourself.']],
            ], 422);
        }
        $user->is_active = !$user->is_active;
        $user->save();
        return ApiJson::ok(['is_active' => $user->is_active], 'Updated');
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
