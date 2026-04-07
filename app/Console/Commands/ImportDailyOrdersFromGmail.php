<?php

namespace App\Console\Commands;

use App\Imports\OrderImport;
use App\Models\GmailAccount;
use App\Models\Order;
use App\Services\GmailService;
use App\Services\OrderExportEmailService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportDailyOrdersFromGmail extends Command
{
    protected $signature = 'gmail:import-daily-orders {--date=}';

    private const SYNC_SCAN_LIMIT = 5;

    protected $description = 'Tải file CSV daily từ Gmail, import vào orders và xóa file sau khi import thành công.';

    public function handle(GmailService $gmailService, OrderExportEmailService $emailService): int
    {
        $targetDate = $this->option('date')
            ? Carbon::parse($this->option('date'), 'Asia/Ho_Chi_Minh')
            : Carbon::yesterday('Asia/Ho_Chi_Minh');

        Log::info('Bat dau cron import daily orders tu Gmail', [
            'target_date' => $targetDate->format('Y-m-d'),
            'manual_date' => $this->option('date'),
        ]);

        $accounts = GmailAccount::query()->get();

        if ($accounts->isEmpty()) {
            Log::warning('Cron import daily orders dung vi chua co tai khoan Gmail nao duoc ket noi');
            $this->warn('Chưa có tài khoản Gmail nào được kết nối.');
            return self::SUCCESS;
        }

        foreach ($accounts as $account) {
            try {
                $synced = $gmailService->syncRecentMessages($account, self::SYNC_SCAN_LIMIT, true);
                Log::info('Da quet Gmail cho cron import orders', [
                    'gmail_account_id' => $account->id,
                    'email' => $account->email,
                    'synced_messages' => $synced,
                    'last_scanned_at' => optional($account->fresh()->last_scanned_at)?->toDateTimeString(),
                ]);
                $this->info("Đã quét Gmail {$account->email}: {$synced} thư.");

                $attachments = $gmailService->syncDailyCsvAttachmentsForDate($account, $targetDate);
                Log::info('Ket qua tim file daily CSV tu Gmail', [
                    'gmail_account_id' => $account->id,
                    'email' => $account->email,
                    'target_date' => $targetDate->format('Y-m-d'),
                    'attachments_count' => $attachments->count(),
                    'attachments' => $attachments->map(fn ($attachment) => [
                        'id' => $attachment->id,
                        'filename' => $attachment->filename,
                        'storage_path' => $attachment->storage_path,
                        'imported_at' => optional($attachment->imported_at)?->toDateTimeString(),
                    ])->values()->all(),
                ]);

                if ($attachments->isEmpty()) {
                    $this->info("Không tìm thấy file daily CSV cho {$targetDate->format('Y-m-d')} ở {$account->email}.");
                    continue;
                }

                foreach ($attachments as $attachment) {
                    if ($attachment->imported_at) {
                        Log::info('Bo qua file da import truoc do', [
                            'gmail_account_id' => $account->id,
                            'attachment_id' => $attachment->id,
                            'filename' => $attachment->filename,
                            'imported_at' => optional($attachment->imported_at)?->toDateTimeString(),
                        ]);
                        continue;
                    }

                    $fullPath = Storage::disk($attachment->storage_disk)->path($attachment->storage_path);
                    Log::info('Chuan bi import file daily CSV', [
                        'gmail_account_id' => $account->id,
                        'email' => $account->email,
                        'attachment_id' => $attachment->id,
                        'filename' => $attachment->filename,
                        'storage_disk' => $attachment->storage_disk,
                        'storage_path' => $attachment->storage_path,
                        'full_path' => $fullPath,
                    ]);

                    if (!file_exists($fullPath)) {
                        $attachment->update([
                            'import_status' => 'missing_file',
                            'import_error' => 'Không tìm thấy file local để import.',
                        ]);
                        continue;
                    }

                    try {
                        Excel::import(new OrderImport(), $fullPath);
                        Log::info('Da import file CSV vao orders', [
                            'gmail_account_id' => $account->id,
                            'attachment_id' => $attachment->id,
                            'filename' => $attachment->filename,
                            'target_date' => $targetDate->format('Y-m-d'),
                        ]);

                        $orders = Order::query()
                            ->whereBetween('return_time', [
                                $targetDate->copy()->startOfDay(),
                                $targetDate->copy()->endOfDay(),
                            ])
                            ->orderByDesc('return_time')
                            ->get();

                        if ($orders->isNotEmpty()) {
                            Log::info('Chuan bi gui email bao cao orders sau import', [
                                'gmail_account_id' => $account->id,
                                'attachment_id' => $attachment->id,
                                'filename' => $attachment->filename,
                                'email' => $emailService->defaultEmail(),
                                'title' => $emailService->defaultTitle($targetDate),
                                'orders_count' => $orders->count(),
                            ]);
                            $emailService->send(
                                $orders,
                                $emailService->defaultEmail(),
                                $emailService->defaultTitle($targetDate),
                                null,
                                $targetDate->format('Y-m-d'),
                                $fullPath
                            );
                        }

                        $attachment->update([
                            'imported_at' => now(),
                            'import_status' => 'imported',
                            'import_error' => null,
                        ]);

                        Storage::disk($attachment->storage_disk)->delete($attachment->storage_path);
                        Log::info('Da xoa file CSV local sau khi import', [
                            'gmail_account_id' => $account->id,
                            'attachment_id' => $attachment->id,
                            'filename' => $attachment->filename,
                            'storage_path' => $attachment->storage_path,
                        ]);

                        $this->info("Đã import và xóa file {$attachment->filename}.");
                    } catch (\Throwable $exception) {
                        report($exception);

                        $attachment->update([
                            'import_status' => 'failed',
                            'import_error' => $exception->getMessage(),
                        ]);

                        Log::error('Import daily CSV từ Gmail thất bại', [
                            'gmail_account_id' => $account->id,
                            'attachment_id' => $attachment->id,
                            'filename' => $attachment->filename,
                            'error' => $exception->getMessage(),
                        ]);

                        $this->error("Import thất bại với file {$attachment->filename}: {$exception->getMessage()}");
                    }
                }
            } catch (\Throwable $exception) {
                report($exception);

                Log::error('Đồng bộ daily CSV từ Gmail thất bại', [
                    'gmail_account_id' => $account->id,
                    'email' => $account->email,
                    'date' => $targetDate->format('Y-m-d'),
                    'error' => $exception->getMessage(),
                ]);

                $this->error("Xử lý Gmail {$account->email} thất bại: {$exception->getMessage()}");
            }
        }

        Log::info('Ket thuc cron import daily orders tu Gmail', [
            'target_date' => $targetDate->format('Y-m-d'),
        ]);

        return self::SUCCESS;
    }
}
