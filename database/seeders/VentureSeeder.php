<?php

namespace Database\Seeders;

use App\Models\Venture;
use Illuminate\Database\Seeder;

class VentureSeeder extends Seeder
{
    public function run(): void
    {
        $ventures = [
            [
                'name' => 'Aurateria Services',
                'slug' => 'aurateria',
                'description' => 'Core software services business — Laravel, DevOps, AI integration for clients.',
                'status' => 'active',
                'partner_funded' => false,
                'color' => '#6C63FF',
                'icon' => 'fa-code',
            ],
            [
                'name' => 'GicoGifts',
                'slug' => 'gicogifts',
                'description' => 'Premium hyper-local artisan gift boxes from Rajasthan. E-commerce brand.',
                'status' => 'active',
                'partner_name' => 'Partner',
                'partner_funded' => true,
                'color' => '#E67E22',
                'icon' => 'fa-gift',
            ],
            [
                'name' => 'AIGather',
                'slug' => 'aigather',
                'description' => 'AI tools marketplace and directory platform.',
                'status' => 'planned',
                'partner_name' => 'Partner',
                'partner_funded' => true,
                'color' => '#2980B9',
                'icon' => 'fa-robot',
            ],
            [
                'name' => 'Medical AI Agents',
                'slug' => 'medical_ai',
                'description' => 'AI customer support system for medical shops — call handling, WhatsApp queries, inventory checks.',
                'status' => 'planned',
                'partner_name' => 'Partner',
                'partner_funded' => true,
                'color' => '#2D8F4E',
                'icon' => 'fa-stethoscope',
            ],
        ];

        foreach ($ventures as $v) {
            Venture::firstOrCreate(['slug' => $v['slug']], $v);
        }
    }
}
