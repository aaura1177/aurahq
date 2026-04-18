<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view users', only: ['index']),
            new Middleware('permission:create users', only: ['create', 'store']),
            new Middleware('permission:edit users', only: ['edit', 'update', 'toggleStatus']),
            new Middleware('permission:delete users', only: ['destroy']),
        ];
    }

    public function index() {
        $users = User::with('roles')->latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create() {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);
        $user->assignRole($request->role);
        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    public function toggleStatus(User $user) {
        if($user->id === auth()->id()) {
            return back()->with('error', 'Cannot block yourself.');
        }
        $user->is_active = !$user->is_active;
        $user->save();
        $status = $user->is_active ? 'activated' : 'blocked';
        return back()->with('success', "User $status.");
    }

    public function edit(User $user) {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required'
        ]);
        
        $user->update($request->only('name', 'email'));
        if($request->password) {
            $user->update(['password' => Hash::make($request->password)]);
        }
        $user->syncRoles($request->role);
        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function destroy(User $user) {
        if($user->id === auth()->id()) {
            return back()->with('error', 'Cannot delete yourself.');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
}
