<?php
namespace Database\Seeders;
use App\Models\ContentTopic;
use Illuminate\Database\Seeder;
class ContentTopicSeeder extends Seeder
{
    public function run(): void
    {
        $topics = [
            // TECHNICAL
            ['title' => 'Why we run our AI agent stack on a single €4/mo server', 'angle' => 'n8n + Gemini + Caddy on one Hetzner box; cost discipline as a feature', 'content_type' => 'technical'],
            ['title' => 'Upgrading a production EC2 to Ubuntu 24.04 with zero downtime', 'angle' => 'nightly backups, IAM least-privilege, the checklist that matters', 'content_type' => 'technical'],
            ['title' => 'Filament v3 as an internal ops backbone', 'angle' => 'building HRMS/CRM/task modules fast on Laravel', 'content_type' => 'technical'],
            ['title' => 'The .env leak attack vector every Laravel dev should close', 'angle' => 'gitignore, push protection, IAM — non-negotiable baselines', 'content_type' => 'technical'],
            ['title' => 'GST invoice generation in Laravel: the India-specific gotchas', 'angle' => 'CGST/SGST/IGST logic, Indian digit formatting, auto numbering', 'content_type' => 'technical'],
            // WINS (anonymized — never revenue+sector+market-count together)
            ['title' => 'Modernising a legacy ERP for an international brand', 'angle' => 'what legacy modernisation actually involves; anonymized', 'content_type' => 'win'],
            ['title' => 'Cutting a client\'s manual reporting from hours to minutes', 'angle' => 'automation ROI story, no client name', 'content_type' => 'win'],
            ['title' => 'Shipping a Flutter app to the Play Store solo', 'angle' => 'DevBrief app journey, solo dev + automation', 'content_type' => 'win'],
            // FOUNDER
            ['title' => 'Running a Laravel studio solo with AI amplification', 'angle' => 'the push/hold venture model; how one person ships like a team', 'content_type' => 'founder'],
            ['title' => 'Why I sell written-first instead of on live calls', 'angle' => 'async sales as a system, playing to strengths', 'content_type' => 'founder'],
            ['title' => 'Building in public: our internal ops app AURATERIA HQ', 'angle' => 'why we built our own command center instead of buying SaaS', 'content_type' => 'founder'],
            ['title' => 'The 3-task daily rule that keeps a solo founder sane', 'angle' => 'OCD-friendly structures, 80% ship rule, protected focus block', 'content_type' => 'founder'],
            ['title' => 'Replacing a $29/mo SaaS with an afternoon of Laravel', 'angle' => 'build vs buy for a bootstrapped studio', 'content_type' => 'founder'],
        ];
        foreach ($topics as $t) {
            ContentTopic::create($t + ['status' => 'available', 'is_active' => true]);
        }
    }
}
