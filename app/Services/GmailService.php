<?php

namespace App\Services;

use App\Models\GmailAccount;
use App\Models\GmailAttachment;
use App\Models\GmailMessage;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class GmailService
{
    private const AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';
    private const API_BASE_URL = 'https://gmail.googleapis.com/gmail/v1';

    public function isConfigured(): bool
    {
        return filled(config('services.gmail.client_id'))
            && filled(config('services.gmail.client_secret'))
            && filled(config('services.gmail.redirect_uri'));
    }

    public function getAuthorizationUrl(string $state): string
    {
        $this->assertConfigured();

        return self::AUTH_URL . '?' . http_build_query([
            'client_id' => config('services.gmail.client_id'),
            'redirect_uri' => config('services.gmail.redirect_uri'),
            'response_type' => 'code',
            'access_type' => 'offline',
            'include_granted_scopes' => 'true',
            'prompt' => 'consent',
            'scope' => implode(' ', $this->scopes()),
            'state' => $state,
        ]);
    }

    public function scopes(): array
    {
        return config('services.gmail.scopes', []);
    }

    public function exchangeCodeForTokens(string $code): array
    {
        return $this->requestTokens([
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => config('services.gmail.redirect_uri'),
        ]);
    }

    public function refreshAccessToken(string $refreshToken): array
    {
        return $this->requestTokens([
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
        ]);
    }

    public function fetchProfile(string $accessToken): array
    {
        return $this->apiRequest('GET', '/users/me/profile', $accessToken);
    }

    public function syncRecentMessages(GmailAccount $account, int $maxResults = 20, bool $forceFullScan = false): int
    {
        $query = 'in:anywhere';

        return $this->syncMessagesByQuery($account, $query, $maxResults, true, !$forceFullScan, true);
    }

    public function syncDailyMessagesForDate(GmailAccount $account, Carbon $date, int $maxResults = 50): int
    {
        $query = 'in:anywhere subject:daily_' . $date->format('Ymd');

        return $this->syncMessagesByQuery($account, $query, $maxResults, false);
    }

    private function syncMessagesByQuery(
        GmailAccount $account,
        string $query,
        int $maxResults,
        bool $updateLastScannedAt,
        bool $filterByLastScannedAt = false,
        bool $onlyDailySubjects = false
    ): int {
        $accessToken = $this->getValidAccessToken($account);

        $response = $this->apiRequest('GET', '/users/me/messages', $accessToken, [
            'query' => [
                'maxResults' => $maxResults,
                'q' => $query,
            ],
        ]);

        $messages = Arr::get($response, 'messages', []);
        $synced = 0;

        foreach ($messages as $message) {
            $details = $this->apiRequest(
                'GET',
                '/users/me/messages/' . $message['id'],
                $accessToken,
                ['query' => ['format' => 'full']]
            );

            if ($filterByLastScannedAt && !$this->shouldSyncMessageByLastScannedAt($account, $details)) {
                continue;
            }

            if ($onlyDailySubjects && !$this->hasDailySubject($details)) {
                continue;
            }

            $this->storeMessage($account, $details);
            $synced++;
        }

        if ($updateLastScannedAt) {
            $account->forceFill([
                'last_scanned_at' => now(),
            ])->save();
        }

        return $synced;
    }

    private function hasDailySubject(array $details): bool
    {
        $headers = collect(Arr::get($details, 'payload.headers', []))
            ->mapWithKeys(fn (array $header) => [Str::lower($header['name']) => $header['value']]);

        $subject = (string) $headers->get('subject', '');

        return Str::contains(Str::lower($subject), 'daily_');
    }

    private function shouldSyncMessageByLastScannedAt(GmailAccount $account, array $details): bool
    {
        if (!$account->last_scanned_at) {
            return true;
        }

        $internalDate = Arr::get($details, 'internalDate');
        if (!$internalDate) {
            return true;
        }

        $messageReceivedAt = Carbon::createFromTimestampMs((int) $internalDate);

        return $messageReceivedAt->greaterThan($account->last_scanned_at);
    }

    public function syncDailyCsvAttachments(GmailAccount $account, int $maxResults = 20): int
    {
        return $this->syncDailyCsvAttachmentsForDate($account, Carbon::yesterday('Asia/Ho_Chi_Minh'), $maxResults)->count();
    }

    public function syncDailyCsvAttachmentsForDate(
        GmailAccount $account,
        Carbon $date,
        int $maxResults = 20
    ): Collection {
        $accessToken = $this->getValidAccessToken($account);
        $subjectPrefix = 'daily_' . $date->format('Ymd');
        $response = $this->apiRequest('GET', '/users/me/messages', $accessToken, [
            'query' => [
                'maxResults' => $maxResults,
                'q' => 'in:anywhere subject:' . $subjectPrefix . ' has:attachment',
            ],
        ]);

        $messages = Arr::get($response, 'messages', []);
        if (empty($messages)) {
            $messages = GmailMessage::query()
                ->where('gmail_account_id', $account->id)
                ->where('subject', 'like', $subjectPrefix . '%')
                ->orderByDesc('received_at')
                ->limit($maxResults)
                ->pluck('gmail_message_id')
                ->filter()
                ->unique()
                ->values()
                ->map(fn (string $gmailMessageId) => ['id' => $gmailMessageId])
                ->all();
        }

        $attachments = collect();

        foreach ($messages as $message) {
            $details = $this->apiRequest(
                'GET',
                '/users/me/messages/' . $message['id'],
                $accessToken,
                ['query' => ['format' => 'full']]
            );

            $gmailMessage = $this->storeMessage($account, $details);
            $attachments = $attachments->merge(
                $this->downloadCsvAttachments($account, $gmailMessage, Arr::get($details, 'payload', []), $accessToken)
            );
        }

        return $attachments;
    }

    private function requestTokens(array $formParams): array
    {
        $this->assertConfigured();

        $response = $this->http()->post(self::TOKEN_URL, [
            'form_params' => array_merge($formParams, [
                'client_id' => config('services.gmail.client_id'),
                'client_secret' => config('services.gmail.client_secret'),
            ]),
        ]);

        return $this->decodeJsonResponse((string) $response->getBody());
    }

    private function apiRequest(string $method, string $path, string $accessToken, array $options = []): array
    {
        $response = $this->http()->request($method, self::API_BASE_URL . $path, array_replace_recursive([
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json',
            ],
        ], $options));

        return $this->decodeJsonResponse((string) $response->getBody());
    }

    private function storeMessage(GmailAccount $account, array $details): GmailMessage
    {
        $payload = Arr::get($details, 'payload', []);
        $headers = collect(Arr::get($payload, 'headers', []))
            ->mapWithKeys(fn (array $header) => [Str::lower($header['name']) => $header['value']]);
        $from = $this->parseFromHeader($headers->get('from'));
        $body = $this->extractBody($payload);
        $receivedAt = $this->resolveReceivedAt($headers->get('date'), Arr::get($details, 'internalDate'));
        $labels = Arr::get($details, 'labelIds', []);

        return GmailMessage::updateOrCreate(
            [
                'gmail_account_id' => $account->id,
                'gmail_message_id' => $details['id'],
            ],
            [
                'thread_id' => Arr::get($details, 'threadId'),
                'subject' => $headers->get('subject'),
                'from_name' => $from['name'],
                'from_email' => $from['email'],
                'snippet' => Arr::get($details, 'snippet'),
                'body_text' => $body['text'],
                'body_html' => $body['html'],
                'labels' => $labels,
                'is_unread' => in_array('UNREAD', $labels, true),
                'received_at' => $receivedAt,
            ]
        );
    }

    private function downloadCsvAttachments(
        GmailAccount $account,
        GmailMessage $message,
        array $payload,
        string $accessToken
    ): Collection
    {
        $parts = $this->flattenParts($payload);
        $attachments = collect();

        foreach ($parts as $part) {
            $filename = (string) Arr::get($part, 'filename', '');
            $attachmentId = Arr::get($part, 'body.attachmentId');
            $mimeType = Arr::get($part, 'mimeType');

            if (!$attachmentId) {
                continue;
            }

            if (!$this->isCsvFile($filename) && !$this->isCsvMimeType($mimeType)) {
                continue;
            }

            if ($filename === '') {
                $filename = $message->subject ?: ('attachment_' . $message->gmail_message_id . '.csv');
                if (!Str::endsWith(Str::lower($filename), '.csv')) {
                    $filename .= '.csv';
                }
            }

            $attachment = $this->apiRequest(
                'GET',
                '/users/me/messages/' . $message->gmail_message_id . '/attachments/' . $attachmentId,
                $accessToken
            );

            $content = $this->decodeBase64Url((string) Arr::get($attachment, 'data', ''));
            $storagePath = $this->buildAttachmentPath($account, $message, $filename);

            Storage::disk('local')->put($storagePath, $content);

            $attachmentModel = GmailAttachment::updateOrCreate(
                [
                    'gmail_message_id' => $message->id,
                    'filename' => $filename,
                ],
                [
                    'mime_type' => $mimeType,
                    'gmail_attachment_id' => $attachmentId,
                    'storage_disk' => 'local',
                    'storage_path' => $storagePath,
                    'size' => strlen($content),
                    'downloaded_at' => now(),
                ]
            );

            $attachments->push($attachmentModel);
        }

        return $attachments;
    }

    private function flattenParts(array $payload): array
    {
        $parts = [];

        foreach (Arr::get($payload, 'parts', []) as $part) {
            $parts[] = $part;

            if (!empty($part['parts'])) {
                $parts = array_merge($parts, $this->flattenParts($part));
            }
        }

        return $parts;
    }

    private function isCsvFile(string $filename): bool
    {
        return Str::endsWith(Str::lower($filename), '.csv');
    }

    private function isCsvMimeType(?string $mimeType): bool
    {
        if (!$mimeType) {
            return false;
        }

        return in_array(Str::lower($mimeType), [
            'text/csv',
            'application/csv',
            'application/vnd.ms-excel',
            'application/octet-stream',
            'text/plain',
        ], true);
    }

    private function buildAttachmentPath(GmailAccount $account, GmailMessage $message, string $filename): string
    {
        $filename = trim($filename);
        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $filename);
        $safeName = trim($safeName, '_');

        if (empty($safeName) || preg_match('/^[\s_.-]+$/', $safeName)) {
            $safeName = 'attachment_' . $message->gmail_message_id . '.csv';
        }

        if (!Str::endsWith(Str::lower($safeName), '.csv')) {
            $safeName .= '.csv';
        }

        $datePath = optional($message->received_at)->format('Ymd') ?: now()->format('Ymd');

        return 'gmail/daily/' . $account->id . '/' . $datePath . '/' . $message->gmail_message_id . '_' . $safeName;
    }

    private function extractBody(array $payload): array
    {
        $result = ['text' => null, 'html' => null];

        $mimeType = Arr::get($payload, 'mimeType');
        $data = Arr::get($payload, 'body.data');

        if ($data && in_array($mimeType, ['text/plain', 'text/html'], true)) {
            $decoded = $this->decodeBase64Url($data);
            $result[$mimeType === 'text/html' ? 'html' : 'text'] = $decoded;
        }

        foreach (Arr::get($payload, 'parts', []) as $part) {
            $partBody = $this->extractBody($part);
            $result['text'] ??= $partBody['text'];
            $result['html'] ??= $partBody['html'];
        }

        if (!$result['text'] && $result['html']) {
            $result['text'] = trim(strip_tags($result['html']));
        }

        return $result;
    }

    private function decodeBase64Url(string $value): string
    {
        $padding = strlen($value) % 4;
        if ($padding > 0) {
            $value .= str_repeat('=', 4 - $padding);
        }

        $decoded = base64_decode(strtr($value, '-_', '+/'));

        return $decoded === false ? '' : $decoded;
    }

    private function parseFromHeader(?string $from): array
    {
        if (!$from) {
            return ['name' => null, 'email' => null];
        }

        if (preg_match('/^(.*)<(.+)>$/', $from, $matches) === 1) {
            return [
                'name' => trim(trim($matches[1]), '" '),
                'email' => trim($matches[2]),
            ];
        }

        return ['name' => $from, 'email' => $from];
    }

    private function resolveReceivedAt(?string $headerDate, $internalDate): ?Carbon
    {
        try {
            if ($headerDate) {
                return Carbon::parse($headerDate);
            }
        } catch (\Throwable) {
        }

        if ($internalDate) {
            return Carbon::createFromTimestampMs((int) $internalDate);
        }

        return null;
    }

    private function getValidAccessToken(GmailAccount $account): string
    {
        if (!$account->isExpired()) {
            return $account->access_token;
        }

        if (!$account->refresh_token) {
            throw new RuntimeException('Tài khoản Gmail đã hết hạn và không có refresh token. Vui lòng kết nối lại.');
        }

        $tokens = $this->refreshAccessToken($account->refresh_token);
        $account->forceFill([
            'access_token' => $tokens['access_token'],
            'token_type' => Arr::get($tokens, 'token_type'),
            'expires_at' => now()->addSeconds((int) Arr::get($tokens, 'expires_in', 3600)),
        ])->save();

        return $account->access_token;
    }

    private function decodeJsonResponse(string $body): array
    {
        $payload = json_decode($body, true);

        if (!is_array($payload)) {
            throw new RuntimeException('Không đọc được phản hồi từ Gmail.');
        }

        if (isset($payload['error'])) {
            $message = is_array($payload['error'])
                ? Arr::get($payload, 'error.message', 'Gmail trả về lỗi không xác định.')
                : (string) $payload['error'];

            throw new RuntimeException($message);
        }

        return $payload;
    }

    public function downloadAttachmentContent(GmailAccount $account, string $messageId, string $attachmentId): string
    {
        $accessToken = $this->getValidAccessToken($account);
        $attachment = $this->apiRequest(
            'GET',
            '/users/me/messages/' . $messageId . '/attachments/' . $attachmentId,
            $accessToken
        );

        return $this->decodeBase64Url((string) Arr::get($attachment, 'data', ''));
    }

    private function assertConfigured(): void
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('Thiếu cấu hình Gmail OAuth trong file .env.');
        }
    }

    private function http(): Client
    {
        return new Client([
            'http_errors' => false,
            'timeout' => 20,
        ]);
    }
}
