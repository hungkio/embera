<?php

namespace App\Services;

use App\Models\DeviceStatus;
use App\Models\DeviceTurnOnHistory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class DeviceStatusService
{
    public function syncFromApi(): void
    {
        try {
            set_time_limit(1000); // cho phép chạy tối đa 17 phút

            Log::info('🔄 Bắt đầu đồng bộ trạng thái thiết bị...');

            // 1️⃣ Lấy token
            $tokenResponse = Http::asForm()->post('https://api.chargekingdom.com/api/open/get_token', [
                'username' => 'embera',
                'password' => 'vA5P1eHZy2ZV1jTN',
            ]);

            if ($tokenResponse->failed()) {
                Log::error('❌ Lấy token thất bại', ['response' => $tokenResponse->body()]);
                return;
            }

            $token = $tokenResponse->json('data.access_token');
            if (!$token) {
                Log::error('❌ Không có token trong response', ['response' => $tokenResponse->json()]);
                return;
            }

            // 2️⃣ Lặp qua từng trang dữ liệu
            $page = 1;
            do {
                $response = Http::withHeaders(['api-token' => $token])
                    ->get("https://api.chargekingdom.com/api/open/equipment/list?page={$page}")
                    ->json();

                $items = $response['data']['list'] ?? [];
                if (empty($items)) break;

                foreach ($items as $item) {
                    $equipId = $item['equip_id'] ?? null;
                    if (!$equipId) continue;

                    $status = null;
                    if (isset($item['net_status'])) {
                        $status = ($item['net_status'] == 1) ? 'online' : 'offline';
                    } elseif (isset($item['status'])) {
                        $status = ($item['status'] == 1) ? 'online' : 'offline';
                    }

                    if ($status === null) continue;

                    $device = \App\Models\DeviceStatus::updateOrCreate(
                        ['equip_id' => $equipId],
                        ['status' => $status]
                    );

                    // ✅ luôn cập nhật updated_at
                    $device->touch();
                }

                $page++;
            } while (!empty($items));

            $count = DeviceStatus::count();
            Log::info("✅ Đồng bộ hoàn tất vào lúc ".now()->format('d/m/Y H:i:s')." - Tổng số: {$count} thiết bị.");
        } catch (\Throwable $e) {
            Log::error('🔥 Lỗi đồng bộ thiết bị', ['error' => $e->getMessage()]);
        }
    }

    public function recordTodayHistory(): void
    {
        if (!Schema::hasTable('device_turn_on_histories')) {
            return;
        }

        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $recordedDate = $now->toDateString();

        DeviceStatus::query()
            ->select(['equip_id', 'code', 'shop_code', 'status'])
            ->orderBy('id')
            ->chunk(500, function ($devices) use ($recordedDate, $now) {
                $rows = $devices
                    ->map(function (DeviceStatus $device) use ($recordedDate, $now) {
                        $equipId = $device->equip_id ?: $device->code;

                        if (!$equipId) {
                            return null;
                        }

                        return [
                            'recorded_date' => $recordedDate,
                            'equip_id' => $equipId,
                            'code' => $device->code,
                            'shop_code' => $device->shop_code,
                            'status' => $device->status === 'online' ? 'online' : 'offline',
                            'recorded_at' => $now,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    })
                    ->filter()
                    ->values()
                    ->all();

                if (!empty($rows)) {
                    DeviceTurnOnHistory::upsert(
                        $rows,
                        ['recorded_date', 'equip_id'],
                        ['code', 'shop_code', 'status', 'recorded_at', 'updated_at']
                    );
                }
            });
    }
}
