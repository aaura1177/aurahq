<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiJson;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleApiController extends Controller
{
    public function index()
    {
        $paginator = Role::with('permissions')->withCount('users')->orderBy('name')->paginate(25);

        return ApiJson::paginated($paginator, fn ($r) => [
            'id' => $r->id,
            'name' => $r->name,
            'permissions' => $r->permissions->pluck('name'),
            'users_count' => $r->users_count,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:roles,name']);
        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
        if ($request->filled('permissions')) {
            $role->syncPermissions($request->permissions);
        }
        return ApiJson::created(['id' => $role->id, 'name' => $role->name], 'Role created successfully');
    }

    public function show(Role $role)
    {
        $role->load('permissions');
        return ApiJson::ok([
            'id' => $role->id,
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('name'),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate(['name' => 'required|string|unique:roles,name,' . $role->id]);
        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);
        return ApiJson::ok(['id' => $role->id, 'name' => $role->name], 'Updated');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return ApiJson::noContent();
    }

    public function permissionsList()
    {
        return ApiJson::ok(Permission::orderBy('name')->pluck('name')->values()->all());
    }

    /** Lightweight list for user form dropdowns (names only). */
    public function roleNames()
    {
        return ApiJson::ok(Role::orderBy('name')->pluck('name')->values()->all());
    }
}
