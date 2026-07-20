<?php

namespace Database\Seeders;

use App\Models\RevenueTarget;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RevenueTargetSeeder extends Seeder
{
    public function run(): void
    {
        // Ramped plan to ₹8.5 crore cumulative by May 2027 (₹ = INR)
        $plan = [
            '2026-08-01' => 3400000,   // 34L
            '2026-09-01' => 5100000,   // 51L  Diwali ramp
            '2026-10-01' => 7650000,   // 76.5L peak
            '2026-11-01' => 7650000,   // 76.5L
            '2026-12-01' => 8500000,   // 85L
            '2027-01-01' => 9350000,   // 93.5L
            '2027-02-01' => 10200000,  // 1.02Cr
            '2027-03-01' => 10625000,  // 1.06Cr
            '2027-04-01' => 11050000,  // 1.10Cr
            '2027-05-01' => 11475000,  // 1.14Cr
        ];
        // Total = 85,000,000 (₹8.5 crore)

        foreach ($plan as $month => $amount) {
            RevenueTarget::updateOrCreate(
                ['month' => Carbon::parse($month)],
                ['target_amount' => $amount, 'notes' => 'Path to ₹8.5 Cr ($1M) by May 2027']
            );
        }
    }
}
