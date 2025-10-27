<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DeviceStatusService;

class SyncDeviceStatus extends Command
{
    /**
     * Tên lệnh chạy cron
     */
    protected $signature = 'device:sync-status';

    /**
     * Mô tả lệnh
     */
    protected $description = 'Đồng bộ trạng thái thiết bị từ API ChargeKingdom';

    /**
     * Xử lý khi chạy lệnh
     */
    public function handle(DeviceStatusService $service)
    {
        $this->info('🔄 Bắt đầu đồng bộ trạng thái thiết bị...');
        $service->syncFromApi();
        $this->info('✅ Hoàn tất đồng bộ trạng thái thiết bị!');
    }
}
