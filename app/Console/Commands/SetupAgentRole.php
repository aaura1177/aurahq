<?php
namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SetupAgentRole extends Command
{
    protected $signature = 'agent:setup-role {--email=agent@aurateria.com}';
    protected $description = 'Create least-privilege agent role and assign to agent user';

    public function handle()
    {
        $needed = [
            'view tasks',
            'view leads',
            'create leads',
            'edit leads',
            'view lead activities',
            'create lead activities',
            'view clients',
            'view projects',
            'view invoices',
        ];

        $existing = Permission::whereIn('name', $needed)->pluck('name')->toArray();
        $missing = array_diff($needed, $existing);
        if (! empty($missing)) {
            $this->warn('Skipped (do not exist): ' . implode(', ', $missing));
        }

        $role = Role::firstOrCreate(['name' => 'agent', 'guard_name' => 'web']);
        $role->syncPermissions($existing);

        $user = User::where('email', $this->option('email'))->first();
        if (! $user) {
            $this->error('Agent user not found: ' . $this->option('email'));
            return self::FAILURE;
        }

        $user->syncRoles(['agent']);

        $this->info('Agent role created with ' . count($existing) . ' permissions.');
        $this->info($user->email . ' downgraded to agent role.');

        return self::SUCCESS;
    }
}
