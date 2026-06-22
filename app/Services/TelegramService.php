<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    /**
     * Gửi tin nhắn đến Telegram.
     *
     * @param string $message
     * @param string|null $chatId
     * @return bool
     */
    public function sendMessage(string $message, ?string $chatId = null): bool
    {
        $token = config('services.telegram.bot_token') ?: env('TELEGRAM_BOT_TOKEN');
        $defaultChatId = config('services.telegram.chat_id') ?: env('TELEGRAM_CHAT_ID');
        $targetChatId = $chatId ?: $defaultChatId;

        if (!$token || !$targetChatId) {
            Log::warning('Telegram bot token hoặc chat ID chưa được cấu hình.');
            return false;
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $targetChatId,
                'text' => $message,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ]);

            if ($response->failed()) {
                Log::error('Gửi tin nhắn Telegram thất bại', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Lỗi khi kết nối API Telegram: ' . $e->getMessage(), ['exception' => $e]);
            return false;
        }
    }
}
