<?php

namespace App\Console\Commands;

use App\Models\DeviceStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class RefreshDeviceStatus extends Command
{
    protected $signature = 'device:refresh-status';
    protected $description = 'Refresh device status from ChargeKingdom API every 30 minutes';

    public function handle()
    {
        // Bước 1: Lấy access_token
        $username = env('CHARGEKINGDOM_USERNAME');
        $password = env('CHARGEKINGDOM_PASSWORD');

        $tokenResponse = Http::post('https://beta-api.chargekingdom.net/api/open/get_token', [
            'username' => $username,
            'password' => $password,
        ]);

        if (!$tokenResponse->successful() || !isset($tokenResponse->json()['data']['access_token'])) {
            $this->error('Failed to get access token: ' . $tokenResponse->body());
            return 1;
        }

        $accessToken = $tokenResponse->json()['data']['access_token'];

        // Bước 2: Gọi API equipment/list
        $page = 1;
        $perPage = 10;
        $totalPages = 1;

        do {
            $equipmentResponse = Http::withHeaders(['api-token' => $accessToken])
                ->get('https://beta-api.chargekingdom.net/api/open/equipment/list', [
                    'page' => $page,
                ]);

            if (!$equipmentResponse->successful()) {
                $this->error('Failed to fetch equipment list: ' . $equipmentResponse->body());
                break;
            }

            $data = $equipmentResponse->json()['data'] ?? [];
            $equipmentList = $data['list'] ?? [];

            // Bước 3: Cập nhật trạng thái vào device_status
            foreach ($equipmentList as $equipment) {
                DeviceStatus::updateOrCreate(
                    ['equip_id' => $equipment['equip_id']],
                    ['status' => $equipment['status']]
                );
            }

            $pagination = $data['pagination'] ?? [];
            $totalPages = $pagination['total_pages'] ?? 1;
            $page++;

        } while ($page <= $totalPages);

        $this->info('Device statuses refreshed successfully.');
        return 0;
    }
}
