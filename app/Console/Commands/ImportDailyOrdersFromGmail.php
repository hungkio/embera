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
    protected $signature = 'gmail:import-daily-orders {--date=} {--manual}';

    private const SYNC_SCAN_LIMIT = 5;

    protected $description = 'Tải file CSV daily từ Gmail, import vào orders và lưu file vào storage.';

    private $customLogger;

    private function logInfo(string $message, array $context = []): void
    {
        $this->customLogger->info($message, $context);
    }

    private function logWarning(string $message, array $context = []): void
    {
        $this->customLogger->warning($message, $context);
    }

    private function logError(string $message, array $context = []): void
    {
        $this->customLogger->error($message, $context);
    }

    public function handle(GmailService $gmailService, OrderExportEmailService $emailService): int
    {
        $targetDate = $this->option('date')
            ? Carbon::parse($this->option('date'), 'Asia/Ho_Chi_Minh')
            : Carbon::yesterday('Asia/Ho_Chi_Minh');

        $isManual = (bool) $this->option('manual');
        $logFile = $isManual ? 'gmail_manual.log' : 'gmail_cron.log';
        $this->customLogger = \Illuminate\Support\Facades\Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/' . $logFile),
        ]);

        $this->logInfo('Bat dau import daily orders tu Gmail', [
            'target_date' => $targetDate->format('Y-m-d'),
            'manual_date' => $this->option('date'),
            'is_manual' => $isManual,
        ]);

        $accounts = GmailAccount::query()->get();

        if ($accounts->isEmpty()) {
            $this->logWarning('Import daily orders dung vi chua co tai khoan Gmail nao duoc ket noi');
            $this->warn('Chưa có tài khoản Gmail nào được kết nối.');
            return self::SUCCESS;
        }

        foreach ($accounts as $account) {
            try {
                $synced = $gmailService->syncRecentMessages($account, self::SYNC_SCAN_LIMIT, true);
                $this->logInfo('Da quet Gmail cho import orders', [
                    'gmail_account_id' => $account->id,
                    'email' => $account->email,
                    'synced_messages' => $synced,
                    'last_scanned_at' => optional($account->fresh()->last_scanned_at)?->toDateTimeString(),
                ]);
                $this->info("Đã quét Gmail {$account->email}: {$synced} thư.");

                $attachments = $gmailService->syncDailyCsvAttachmentsForDate($account, $targetDate);
                $this->logInfo('Ket qua tim file daily CSV tu Gmail', [
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
                        $this->logInfo('Bo qua file da import truoc do', [
                            'gmail_account_id' => $account->id,
                            'attachment_id' => $attachment->id,
                            'filename' => $attachment->filename,
                            'imported_at' => optional($attachment->imported_at)?->toDateTimeString(),
                        ]);
                        continue;
                    }

                    $fullPath = Storage::disk($attachment->storage_disk)->path($attachment->storage_path);
                    $this->logInfo('Chuan bi import file daily CSV', [
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

                    // Tự động chuyển đổi CSV sang XLSX nếu file là CSV
                    if (\Illuminate\Support\Str::endsWith(\Illuminate\Support\Str::lower($attachment->storage_path), '.csv')) {
                        try {
                            $csvContent = file_get_contents($fullPath);

                            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                            $reader->setInputEncoding('UTF-8');

                            // Nhận diện delimiter
                            $firstLine = strtok($csvContent, "\r\n");
                            if ($firstLine !== false) {
                                $semicolons = substr_count($firstLine, ';');
                                $commas = substr_count($firstLine, ',');
                                if ($semicolons > $commas) {
                                    $reader->setDelimiter(';');
                                } else {
                                    $reader->setDelimiter(',');
                                }
                            }

                            $spreadsheet = $reader->load($fullPath);
                            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

                            $newStoragePath = substr($attachment->storage_path, 0, -4) . '.xlsx';
                            $newFilename = substr($attachment->filename, 0, -4) . '.xlsx';
                            $newFullPath = Storage::disk($attachment->storage_disk)->path($newStoragePath);

                            $dir = dirname($newFullPath);
                            if (!is_dir($dir)) {
                                mkdir($dir, 0755, true);
                            }

                            $writer->save($newFullPath);

                            // Xóa file CSV cũ
                            unlink($fullPath);

                            // Cập nhật model
                            $attachment->update([
                                'storage_path' => $newStoragePath,
                                'filename' => $newFilename,
                            ]);

                            $fullPath = $newFullPath;

                            $this->logInfo('Da tu dong convert file daily CSV sang XLSX trong import', [
                                'attachment_id' => $attachment->id,
                                'new_path' => $newStoragePath,
                            ]);
                        } catch (\Throwable $e) {
                            $this->logError('Loi khi convert CSV sang XLSX trong import: ' . $e->getMessage(), [
                                'attachment_id' => $attachment->id,
                                'exception' => $e,
                            ]);
                        }
                    }

                    try {
                        Excel::import(new OrderImport(), $fullPath);
                        $this->logInfo('Da import file vao orders', [
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

                        if (!$isManual && $orders->isNotEmpty()) {
                            $this->logInfo('Chuan bi gui email bao cao orders sau import', [
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

                        // Lưu lại luôn không xóa nữa theo yêu cầu của user
                        $this->logInfo('Da import file thanh cong va luu lai trong storage', [
                            'gmail_account_id' => $account->id,
                            'attachment_id' => $attachment->id,
                            'filename' => $attachment->filename,
                            'storage_path' => $attachment->storage_path,
                        ]);

                        $this->info("Đã import và lưu file {$attachment->filename}.");
                    } catch (\Throwable $exception) {
                        report($exception);

                        $attachment->update([
                            'import_status' => 'failed',
                            'import_error' => $exception->getMessage(),
                        ]);

                        $this->logError('Import daily CSV từ Gmail thất bại', [
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

                $this->logError('Đồng bộ daily CSV từ Gmail thất bại', [
                    'gmail_account_id' => $account->id,
                    'email' => $account->email,
                    'date' => $targetDate->format('Y-m-d'),
                    'error' => $exception->getMessage(),
                ]);

                $this->error("Xử lý Gmail {$account->email} thất bại: {$exception->getMessage()}");
            }
        }

        $this->logInfo('Ket thuc import daily orders tu Gmail', [
            'target_date' => $targetDate->format('Y-m-d'),
        ]);

        return self::SUCCESS;
    }
}
