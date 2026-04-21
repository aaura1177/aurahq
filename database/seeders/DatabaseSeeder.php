<?php
namespace Database\Seeders;
use App\Models\User;
use App\Models\FinanceContact;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Permissions
        $modules = ['users', 'roles', 'finance', 'finance contacts', 'revenue targets', 'tasks', 'grocery', 'grocery templates', 'grocery expenses', 'reports', 'task reports', 'task todos', 'holidays', 'attendance', 'daily reports', 'leads', 'lead activities', 'clients', 'projects', 'milestones', 'invoices', 'ventures', 'venture updates'];
        $actions = ['view', 'create', 'edit', 'delete'];
        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "$action $module"]);
            }
        }

        // 2. Roles
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $employee = Role::firstOrCreate(['name' => 'employee']);

        $superAdmin->givePermissionTo(Permission::all());
        $admin->givePermissionTo(Permission::where('name', '!=', 'delete users')->get());
        
        // Employee Permissions
        $employee->givePermissionTo([
            'view tasks',
            'create task reports',
            'view task reports',
            'create daily reports',
            'view leads',
            'create leads',
            'edit leads',
            'create lead activities',
            'edit lead activities',
            'delete lead activities',
            'view projects',
            'view clients',
        ]);

        $this->call(VentureSeeder::class);

        // 3. Users
        if (!User::where('email', 'ethanstark041@gmail.com')->exists()) {
            $user = User::create([
                'name' => 'ETHAN',
                'email' => 'ethanstark041@gmail.com',
                'password' => Hash::make('Joy@2025$'),
            ]);
            $user->assignRole($superAdmin);
        }

        
    }
}
