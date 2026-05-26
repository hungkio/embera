<?php

namespace App\Console\Commands;

use App\Services\DeviceStatusService;
use Illuminate\Console\Command;

class RecordDeviceTurnOnHistory extends Command
{
    protected $signature = 'device:record-turn-on-history';

    protected $description = 'Ghi snapshot trạng thái online/offline thiết bị theo ngày';

    public function handle(DeviceStatusService $service): int
    {
        $this->info('Bắt đầu ghi snapshot online/offline thiết bị hôm nay...');

        $service->recordTodayHistory();

        $this->info('Hoàn tất ghi snapshot online/offline thiết bị hôm nay.');

        return self::SUCCESS;
    }
}
