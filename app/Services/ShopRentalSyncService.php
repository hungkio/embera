<?php

namespace App\Services;

use App\Models\ShopRentalStat;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShopRentalSyncService
{
    public function sync(): void
    {
        ShopRentalStat::query()->delete(); // ❗ chỉ giữ shop còn trong API
        // 1️⃣ Lấy token
        $tokenRes = Http::asForm()->post(
            'https://api.chargekingdom.com/api/open/get_token',
            [
                'username' => 'embera',
                'password' => 'vA5P1eHZy2ZV1jTN'
            ]
        );

        $token = data_get($tokenRes->json(), 'data.access_token');
        if (!$token) {
            Log::error('❌ Cannot get API token');
            return;
        }

        $page = 1;
        $stats = []; // shop_id => [on, total]

        // 2️⃣ Loop all pages
        do {
            $res = Http::withHeaders([
                'api-token' => $token
            ])->get(
                'https://api.chargekingdom.com/api/open/equipment/list',
                ['page' => $page]
            )->json();

            $items = data_get($res, 'data.list', []);
            if (empty($items)) break;

            foreach ($items as $item) {
                $shopId = $item['shop_id'] ?? null;
                if (!$shopId) continue;

                $slotOn  = (int) data_get($item, 'slots.slot_status.slot_on', 0);
                $slotOff = (int) data_get($item, 'slots.slot_status.slot_off', 0);

                if (!isset($stats[$shopId])) {
                    $stats[$shopId] = [
                        'renting' => 0,
                        'total'   => 0,
                    ];
                }

                $stats[$shopId]['renting'] += $slotOn;
                $stats[$shopId]['total']   += ($slotOn + $slotOff);
            }

            $page++;
        } while (true);

        // 3️⃣ Save DB
        foreach ($stats as $shopId => $data) {
            ShopRentalStat::updateOrCreate(
                ['shop_id' => $shopId],
                [
                    'renting_slots' => $data['renting'],
                    'total_slots'   => $data['total'],
                    'last_synced_at'=> now(),
                ]
            );
        }

        Log::info('✅ Shop rental stats synced: '.count($stats).' shops');
    }
}
