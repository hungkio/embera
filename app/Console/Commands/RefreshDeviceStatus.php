<?php

namespace App\Console\Commands;

use App\Models\DeviceStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        // Bước 2: Gọi API equipment/list và thu thập dữ liệu
        $page = 1;
        $perPage = 1000; // Tăng perPage để giảm số lần gọi API
        $totalPages = 1;
        $equipmentData = []; // Mảng để lưu equip_id và status

        do {
            $equipmentResponse = Http::withHeaders(['api-token' => $accessToken])
                ->get('https://beta-api.chargekingdom.net/api/open/equipment/list', [
                    'page' => $page,
                    'per_page' => $perPage,
                ]);

            if (!$equipmentResponse->successful()) {
                $this->error('Failed to fetch equipment list: ' . $equipmentResponse->body());
                break;
            }

            $data = $equipmentResponse->json()['data'] ?? [];
            $equipmentList = $data['list'] ?? [];

            // Thu thập equip_id và status vào mảng
            foreach ($equipmentList as $equipment) {
                $equipmentData[$equipment['equip_id']] = $equipment['status'];
            }

            $pagination = $data['pagination'] ?? [];
            $totalPages = $pagination['total_pages'] ?? 1;
            $page++;

        } while ($page <= $totalPages);

        // Bước 3: Cập nhật chỉ các bản ghi có status thay đổi
        if (!empty($equipmentData)) {
            Log::info('Processing ' . count($equipmentData) . ' equipment records');
            $startTime = microtime(true);

            // Query 1: Lấy tất cả bản ghi hiện có theo equip_id
            $existingDevices = DeviceStatus::whereIn('equip_id', array_keys($equipmentData))
                ->pluck('status', 'equip_id')
                ->toArray();

            // Tìm các bản ghi cần cập nhật (status khác hoặc bản ghi mới)
            $toUpdate = [];
            foreach ($equipmentData as $equipId => $status) {
                if (!isset($existingDevices[$equipId]) || $existingDevices[$equipId] !== $status) {
                    $toUpdate[] = [
                        'equip_id' => $equipId,
                        'status' => $status,
                        'updated_at' => now(),
                        'created_at' => now(), // Để chèn mới nếu cần
                    ];
                }
            }

            // Query 2: Cập nhật hàng loạt theo batch
            if (!empty($toUpdate)) {
                $chunks = array_chunk($toUpdate, 2000); // Chia thành batch 2,000 bản ghi
                foreach ($chunks as $chunk) {
                    DeviceStatus::upsert(
                        $chunk,
                        ['equip_id'],
                        ['status', 'updated_at'],
                        ['created_at', 'updated_at']
                    );
                }
            }

            Log::info('Step 3 completed in ' . (microtime(true) - $startTime) . ' seconds');
        }

        $this->info('Device statuses refreshed successfully.');
        return 0;
    }
}
