<?php

namespace Database\Seeders;

use App\Models\FinanceContact;
use Illuminate\Database\Seeder;

class FinanceContactSeeder extends Seeder
{
    public function run(): void
    {
        $contacts = [
            'Misc / Cash',
            'Artisan (Client)',
            'Chitransh Advertising',
            'Dharmendra (Team)',
            'AWS',
            'Hostinger',
            'Google / Gemini',
            'Razorpay',
            'Ads / Marketing',
            'Office / Misc Expense',
        ];

        foreach ($contacts as $name) {
            FinanceContact::firstOrCreate(
                ['name' => $name],
                ['is_active' => true]
            );
        }
    }
}
