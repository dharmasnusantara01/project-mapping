<?php

namespace App\Console\Commands;

use App\Models\AccountManager;
use App\Services\AmReminderBuilder;
use App\Services\TelegramNotifier;
use Illuminate\Console\Command;

class SendAmReminder extends Command
{
    protected $signature = 'am:remind
                            {--am= : Kirim hanya ke AccountManager ID tertentu}
                            {--dry-run : Tampilkan pesan ke console tanpa kirim}';

    protected $description = 'Alert AM via Telegram saat ada project yang stagnant >7 hari (tidak ada progress).';

    public function handle(TelegramNotifier $telegram, AmReminderBuilder $builder): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if (! $dryRun && ! $telegram->isConfigured()) {
            $this->error('TELEGRAM_BOT_TOKEN tidak diset di .env. Set dulu, atau jalankan dengan --dry-run.');
            return self::FAILURE;
        }

        $query = AccountManager::query()->whereNotNull('telegram_chat_id');
        if ($amId = $this->option('am')) {
            $query->where('id', $amId);
        }

        $ams = $query->get();
        if ($ams->isEmpty()) {
            $this->warn('Tidak ada AM dengan telegram_chat_id terisi.');
            return self::SUCCESS;
        }

        $sent = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($ams as $am) {
            $stale = $builder->staleProjects($am);
            $message = $builder->build($am);

            if ($message === null) {
                $this->line("[SKIP] {$am->name} — tidak ada project stale.");
                $skipped++;
                continue;
            }

            if ($dryRun) {
                $this->info("─── {$am->name} (chat: {$am->telegram_chat_id}, stale: {$stale->count()}) ───");
                $this->line($message);
                $this->line('');
                continue;
            }

            $ok = $telegram->sendMessage($am->telegram_chat_id, $message);
            if ($ok) {
                $now = now();
                $am->update(['last_reminded_at' => $now]);
                // Tandai per project tanpa touch updated_at — supaya project tetap dianggap stale
                // sampai Sales benar-benar update progress.
                \App\Models\Project::withoutTimestamps(function () use ($stale, $now) {
                    foreach ($stale as $project) {
                        $project->update(['last_stale_alert_at' => $now]);
                    }
                });
                $this->info("[OK] {$am->name} — {$stale->count()} project di-alert");
                $sent++;
            } else {
                $this->error("[FAIL] {$am->name} — cek storage/logs/laravel.log");
                $failed++;
            }
        }

        $this->newLine();
        if ($dryRun) {
            $this->info("Dry run selesai. Skipped: {$skipped}");
        } else {
            $this->info("Selesai. Sent: {$sent}, Skipped: {$skipped}, Failed: {$failed}");
        }

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
