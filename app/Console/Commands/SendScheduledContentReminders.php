<?php

namespace App\Console\Commands;

use App\Models\ContentDraft;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SendScheduledContentReminders extends Command
{
    protected $signature = 'content:remind-scheduled';

    protected $description = 'Telegram reminder for content drafts scheduled to post today';

    public function handle()
    {
        $today = now('Asia/Kolkata')->toDateString();
        $drafts = ContentDraft::where('status', 'scheduled')
            ->whereDate('scheduled_for', $today)
            ->get();

        if ($drafts->isEmpty()) {
            $this->info('No drafts scheduled for today.');
            return self::SUCCESS;
        }

        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (! $token || ! $chatId) {
            $this->error('TELEGRAM_BOT_TOKEN or TELEGRAM_CHAT_ID is not configured.');
            return self::FAILURE;
        }

        $sent = 0;
        foreach ($drafts as $d) {
            $text = "📅 POST TODAY · {$d->platform}\n"
                  . ($d->hook ? $d->hook . "\n\n" : '')
                  . $d->body . "\n\n"
                  . ($d->hashtags ?? '') . "\n\n"
                  . '— Mark as posted in HQ when done.';

            $response = Http::timeout(15)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'disable_web_page_preview' => true,
            ]);

            if ($response->successful()) {
                $sent++;
            } else {
                $this->warn("Failed to send draft #{$d->id}: ".$response->body());
            }
        }

        $this->info("Sent {$sent} reminder(s).");

        return $sent === $drafts->count() ? self::SUCCESS : self::FAILURE;
    }
}
