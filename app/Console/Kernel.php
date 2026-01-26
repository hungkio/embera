<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // 🔁 Sync trạng thái online/offline thiết bị
        $schedule->command('device:sync-status')
            ->everyThirtyMinutes()
            ->withoutOverlapping();

        // 🔁 Sync trạng thái thuê pin cho MAP (5 phút)
        $schedule->call(function () {
                app(\App\Services\ShopRentalSyncService::class)->sync();
            })
            ->name('sync-shop-rental-stats')
            ->everyFiveMinutes()
            ->withoutOverlapping();

        // 📊 Báo cáo doanh thu hàng ngày
        $schedule->job(new \App\Jobs\SendDailyRevenueReportJob)
            ->dailyAt('08:30')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
