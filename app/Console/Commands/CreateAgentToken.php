<?php
namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateAgentToken extends Command
{
    protected $signature = 'agent:token {--email=agent@aurateria.com} {--name=Agent Bot}';
    protected $description = 'Create/refresh an agent service user and print a Sanctum token';

    public function handle()
    {
        $email = $this->option('email');
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $this->option('name'),
                'password' => Hash::make(Str::random(32)),
                'is_active' => true,
            ]
        );

        if (! $user->hasRole('super-admin')) {
            $user->assignRole('super-admin');
        }

        // Revoke old agent tokens to keep things clean
        $user->tokens()->where('name', 'n8n-agent')->delete();

        $token = $user->createToken('n8n-agent')->plainTextToken;

        $this->info('Agent user ready: ' . $email);
        $this->warn('TOKEN (save to n8n, shown once):');
        $this->line($token);

        return self::SUCCESS;
    }
}
