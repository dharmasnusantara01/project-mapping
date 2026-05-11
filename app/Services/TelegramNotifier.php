<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotifier
{
    public function isConfigured(): bool
    {
        return ! empty(config('services.telegram.bot_token'));
    }

    public function sendMessage(string $chatId, string $text, array $opts = []): bool
    {
        if (! $this->isConfigured()) {
            Log::warning('TelegramNotifier: bot_token not configured.');
            return false;
        }

        $token = config('services.telegram.bot_token');
        $base  = rtrim((string) config('services.telegram.api_base'), '/');

        $payload = array_merge([
            'chat_id'                  => $chatId,
            'text'                     => $text,
            'parse_mode'               => 'HTML',
            'disable_web_page_preview' => true,
        ], $opts);

        try {
            $response = Http::timeout(10)
                ->asJson()
                ->post("{$base}/bot{$token}/sendMessage", $payload);

            if ($response->ok() && ($response->json('ok') === true)) {
                return true;
            }

            Log::warning('TelegramNotifier: send failed', [
                'chat_id' => $chatId,
                'status'  => $response->status(),
                'body'    => $response->body(),
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error('TelegramNotifier: exception', [
                'chat_id' => $chatId,
                'error'   => $e->getMessage(),
            ]);
            return false;
        }
    }
}
