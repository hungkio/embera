<?php

namespace App\Services;

use App\DataTables\Export\OrderExportHandler;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class OrderExportEmailService
{
    public function send(
        Collection $orders,
        string $email,
        string $title,
        ?string $content = null,
        ?string $date = null,
        ?string $originalDataPath = null
    ): void {
        $fileName = 'Orders_' . now()->format('Ymd_His') . '.xlsx';
        $filePath = 'exports/' . $fileName;

        Log::info('Bat dau tao file export orders de gui email', [
            'email' => $email,
            'title' => $title,
            'date' => $date,
            'orders_count' => $orders->count(),
            'original_data_path' => $originalDataPath,
        ]);

        Storage::disk('local')->makeDirectory('exports');

        Excel::store(
            new OrderExportHandler($orders, $date, $date, $date),
            $filePath,
            'local'
        );

        $fullPath = Storage::disk('local')->path($filePath);

        Log::info('Da tao file export orders', [
            'email' => $email,
            'title' => $title,
            'export_path' => $fullPath,
        ]);

        Mail::send([], [], function ($message) use ($email, $title, $content, $fullPath, $fileName, $originalDataPath) {
            $message->to($email)
                ->subject($title)
                ->attach($fullPath, [
                    'as' => $fileName,
                ]);

            if (filled($content)) {
                $message->setBody($content, 'text/html');
            }

            if ($originalDataPath && file_exists($originalDataPath)) {
                $message->attach($originalDataPath, [
                    'as' => basename($originalDataPath),
                    'mime' => mime_content_type($originalDataPath) ?: 'text/csv',
                ]);
            }
        });

        Log::info('Da gui email export orders', [
            'email' => $email,
            'title' => $title,
            'attachment' => $fullPath,
            'original_data_path' => $originalDataPath,
        ]);

        Storage::disk('local')->delete($filePath);

        Log::info('Da xoa file export orders tam sau khi gui email', [
            'email' => $email,
            'title' => $title,
            'export_path' => $fullPath,
        ]);
    }

    public function defaultEmail(): string
    {
        return env('ORDER_DAILY_EXPORT_EMAIL', 'goatn4b1@gmail.com');
    }

    public function defaultTitle(Carbon $date): string
    {
        return 'BC doanh thu ngày ' . $date->format('d/m/Y');
    }
}
