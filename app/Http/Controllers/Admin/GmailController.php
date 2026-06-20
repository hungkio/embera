<?php

namespace App\Http\Controllers\Admin;

use App\Domain\MailSetting\Models\MailSetting;
use App\Models\GmailAccount;
use App\Models\GmailAttachment;
use App\Models\GmailMessage;
use App\Services\GmailService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GmailController
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $this->authorize('view', MailSetting::class);

        $account = $this->account();
        $messages = $account?->messages()->paginate(20);
        $selectedMessage = null;

        if ($account && $request->filled('message')) {
            $selectedMessage = $account->messages()->whereKey($request->integer('message'))->first();
        }

        if (!$selectedMessage && $messages && $messages->count() > 0) {
            $selectedMessage = $messages->first();
        }

        return view('admin.gmail.index', [
            'isConfigured' => app(GmailService::class)->isConfigured(),
            'account' => $account,
            'messages' => $messages,
            'selectedMessage' => $selectedMessage,
        ]);
    }

    public function connect(GmailService $gmailService): RedirectResponse
    {
        $this->authorize('view', MailSetting::class);

        $state = Str::random(40);
        session(['gmail_oauth_state' => $state]);

        return redirect()->away($gmailService->getAuthorizationUrl($state));
    }

    public function callback(Request $request, GmailService $gmailService): RedirectResponse
    {
        $this->authorize('view', MailSetting::class);

        abort_unless(
            $request->filled('code')
                && hash_equals((string) session('gmail_oauth_state'), (string) $request->string('state')),
            403
        );

        try {
            $tokens = $gmailService->exchangeCodeForTokens((string) $request->string('code'));
            $profile = $gmailService->fetchProfile($tokens['access_token']);
            $account = GmailAccount::firstOrNew(['admin_id' => auth()->id()]);

            $account->fill([
                'email' => Arr::get($profile, 'emailAddress'),
                'access_token' => $tokens['access_token'],
                'refresh_token' => Arr::get($tokens, 'refresh_token', $account->refresh_token),
                'token_type' => Arr::get($tokens, 'token_type'),
                'scopes' => $gmailService->scopes(),
                'expires_at' => now()->addSeconds((int) Arr::get($tokens, 'expires_in', 3600)),
            ]);
            $account->save();

            $gmailService->syncRecentMessages($account, 5, true);

            flash()->success(__('Kết nối Gmail thành công.'));
        } catch (\Throwable $exception) {
            report($exception);
            flash()->error($exception->getMessage());
        } finally {
            session()->forget('gmail_oauth_state');
        }

        return redirect()->route('admin.gmail.index');
    }

    public function sync(GmailService $gmailService): RedirectResponse
    {
        $this->authorize('view', MailSetting::class);

        $account = $this->account();
        abort_unless($account, 404);

        try {
            $synced = $gmailService->syncRecentMessages($account, 5, true);
            flash()->success(__('Đã đồng bộ :count email gần nhất.', ['count' => $synced]));
        } catch (\Throwable $exception) {
            report($exception);
            flash()->error($exception->getMessage());
        }

        return redirect()->route('admin.gmail.index');
    }

    public function disconnect(): RedirectResponse
    {
        $this->authorize('view', MailSetting::class);

        $account = $this->account();
        abort_unless($account, 404);

        GmailMessage::where('gmail_account_id', $account->id)->delete();
        $account->delete();

        flash()->success(__('Đã ngắt kết nối Gmail.'));

        return redirect()->route('admin.gmail.index');
    }

    public function syncDailyCsv(Request $request, GmailService $gmailService)
    {
        $this->authorize('view', MailSetting::class);

        $account = $this->account();
        abort_unless($account, 404);

        $dateStr = $request->input('date');
        try {
            $date = $dateStr ? Carbon::parse($dateStr) : Carbon::yesterday('Asia/Ho_Chi_Minh');

            // Tìm trong local database/storage trước (nếu không force sync)
            $attachment = null;
            $forceSync = $request->boolean('force_sync');

            if (!$forceSync) {
                $subjectPrefix = 'daily_' . $date->format('Ymd');
                $attachment = GmailAttachment::query()
                    ->whereHas('message', function ($query) use ($account, $subjectPrefix) {
                        $query->where('gmail_account_id', $account->id)
                              ->where('subject', 'like', $subjectPrefix . '%');
                    })
                    ->orderByDesc('id')
                    ->first();
            }

            // Nếu không tìm thấy hoặc file vật lý không tồn tại trong storage, thực hiện sync từ Gmail
            if (!$attachment || !\Illuminate\Support\Facades\Storage::disk($attachment->storage_disk)->exists($attachment->storage_path)) {
                $attachments = $gmailService->syncDailyCsvAttachmentsForDate($account, $date);

                if ($attachments->isEmpty()) {
                    flash()->error(__('Không tìm thấy file CSV nào từ email daily_ cho ngày :date.', ['date' => $date->format('d/m/Y')]));
                    return redirect()->route('admin.gmail.index');
                }

                $attachment = $attachments->first();
            }

            $disk = \Illuminate\Support\Facades\Storage::disk($attachment->storage_disk);

            // Nếu file đã được chuyển đổi sang XLSX ở local storage, tải trực tiếp luôn
            if (Str::endsWith(Str::lower($attachment->storage_path), '.xlsx')) {
                if ($disk->exists($attachment->storage_path)) {
                    return response()->streamDownload(function () use ($disk, $attachment) {
                        echo $disk->get($attachment->storage_path);
                    }, $attachment->filename, [
                        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'Cache-Control' => 'max-age=0',
                    ]);
                }
            }

            $content = null;

            // 1. Thử lấy nội dung từ disk nếu file tồn tại (đối với file CSV)
            try {
                if ($disk->exists($attachment->storage_path)) {
                    $content = $disk->get($attachment->storage_path);
                }
            } catch (\Throwable $e) {
                report($e);
            }

            // 2. Nếu chưa có nội dung, tải trực tiếp từ Gmail
            if ($content === null || $content === '') {
                if ($attachment->message && $attachment->gmail_attachment_id) {
                    $content = $gmailService->downloadAttachmentContent(
                        $account,
                        $attachment->message->gmail_message_id,
                        $attachment->gmail_attachment_id
                    );

                    // Tự động sửa đường dẫn lưu trữ nếu bị lỗi đuôi gạch dưới
                    if (Str::endsWith($attachment->storage_path, '_')) {
                        $datePath = optional($attachment->message->received_at)->format('Ymd') ?: now()->format('Ymd');
                        $newPath = 'gmail/daily/' . $account->id . '/' . $datePath . '/' . $attachment->message->gmail_message_id . '_attachment_' . $attachment->message->gmail_message_id . '.csv';

                        $attachment->storage_path = $newPath;
                        $attachment->save();
                    }

                    // Lưu tạm vào disk làm cache
                    try {
                        $disk->put($attachment->storage_path, $content);
                    } catch (\Throwable $e) {
                        report($e);
                    }
                }
            }

            if ($content === null) {
                throw new \RuntimeException('Không thể lấy nội dung file CSV từ Gmail.');
            }

            // Chuyển đổi nội dung CSV sang định dạng XLSX
            $tempCsvFile = tempnam(sys_get_temp_dir(), 'csv_');
            file_put_contents($tempCsvFile, $content);

            try {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                $reader->setInputEncoding('UTF-8');

                // Tự động nhận diện dấu phân cách (delimiter)
                $firstLine = strtok($content, "\r\n");
                if ($firstLine !== false) {
                    $semicolons = substr_count($firstLine, ';');
                    $commas = substr_count($firstLine, ',');
                    if ($semicolons > $commas) {
                        $reader->setDelimiter(';');
                    } else {
                        $reader->setDelimiter(',');
                    }
                }

                $spreadsheet = $reader->load($tempCsvFile);
                if (file_exists($tempCsvFile)) {
                    unlink($tempCsvFile);
                }

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

                $filename = $attachment->filename;
                if (Str::endsWith(Str::lower($filename), '.csv')) {
                    $xlsxFilename = substr($filename, 0, -4) . '.xlsx';
                } else {
                    $xlsxFilename = $filename . '.xlsx';
                }

                return response()->streamDownload(function () use ($writer) {
                    $writer->save('php://output');
                }, $xlsxFilename, [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Cache-Control' => 'max-age=0',
                ]);
            } catch (\Throwable $e) {
                // Fallback về tải CSV nguyên bản nếu có lỗi xảy ra khi chuyển đổi
                if (file_exists($tempCsvFile)) {
                    unlink($tempCsvFile);
                }
                report($e);

                return response()->streamDownload(function () use ($content) {
                    echo $content;
                }, $attachment->filename, [
                    'Content-Type' => 'text/csv',
                ]);
            }
        } catch (\Throwable $exception) {
            report($exception);
            flash()->error($exception->getMessage());
        }

        return redirect()->route('admin.gmail.index');
    }

    public function importDailyOrdersToday(Request $request): RedirectResponse
    {
        $this->authorize('view', MailSetting::class);

        $account = $this->account();
        abort_unless($account, 404);

        $dateStr = $request->input('date');
        $importDate = $dateStr ? Carbon::parse($dateStr)->toDateString() : Carbon::yesterday('Asia/Ho_Chi_Minh')->toDateString();

        try {
            $exitCode = Artisan::call('gmail:import-daily-orders', [
                '--date' => $importDate,
                '--manual' => true,
            ]);

            if ($exitCode === 0) {
                flash()->success(__('Đã chạy import daily orders cho ngày :date.', ['date' => Carbon::parse($importDate)->format('d/m/Y')]));
            } else {
                flash()->error(__('Import daily orders kết thúc với mã lỗi :code.', ['code' => $exitCode]));
            }
        } catch (\Throwable $exception) {
            report($exception);
            flash()->error($exception->getMessage());
        }

        return redirect()->route('admin.gmail.index');
    }


    private function account(): ?GmailAccount
    {
        return GmailAccount::where('admin_id', auth()->id())->first();
    }
}
