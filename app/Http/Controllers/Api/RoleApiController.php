<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleApiController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->withCount('users')->get()->map(fn ($r) => [
            'id' => $r->id,
            'name' => $r->name,
            'permissions' => $r->permissions->pluck('name'),
            'users_count' => $r->users_count,
        ]);
        return response()->json(['data' => $roles]);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:roles,name']);
        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
        if ($request->filled('permissions')) {
            $role->syncPermissions($request->permissions);
        }
        return response()->json(['message' => 'Created', 'data' => ['id' => $role->id, 'name' => $role->name]], 201);
    }

    public function show(Role $role)
    {
        $role->load('permissions');
        return response()->json(['data' => [
            'id' => $role->id,
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('name'),
        ]]);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate(['name' => 'required|string|unique:roles,name,' . $role->id]);
        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);
        return response()->json(['message' => 'Updated']);
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function permissionsList()
    {
        return response()->json(['data' => Permission::orderBy('name')->pluck('name')]);
    }

    /** Lightweight list for user form dropdowns (names only). */
    public function roleNames()
    {
        return response()->json(['data' => Role::orderBy('name')->pluck('name')->values()->all()]);
    }
}
