<?php

namespace App\Services;

use App\Models\ShopRentalStat;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ShopRentalSyncService
{
    public function sync(): void
    {
        // 1) Lấy token
        $tokenRes = Http::asForm()->post('https://api.chargekingdom.com/api/open/get_token', [
            'username' => config('services.chargekingdom.username'),
            'password' => config('services.chargekingdom.password'),
        ]);

        $token = data_get($tokenRes->json(), 'data.access_token');
        if (!$token) {
            Log::error('❌ Cannot get API token', ['status' => $tokenRes->status(), 'body' => $tokenRes->body()]);
            return; // ❗ KHÔNG XOÁ DB
        }

        $page = 1;
        $stats = [];

        do {
            $res = Http::withHeaders(['api-token' => $token])
                ->get('https://api.chargekingdom.com/api/open/equipment/list', ['page' => $page]);

            if (!$res->ok()) {
                Log::error('❌ equipment/list failed', ['page' => $page, 'status' => $res->status(), 'body' => $res->body()]);
                return; // ❗ KHÔNG XOÁ DB
            }

            $json = $res->json();
            $items = data_get($json, 'data.list', []);
            if (empty($items)) break;

            foreach ($items as $item) {
                $shopId = $item['shop_id'] ?? null;
                if (!$shopId) continue;

                $slotOn  = (int) data_get($item, 'slots.slot_status.slot_on', 0);
                $slotOff = (int) data_get($item, 'slots.slot_status.slot_off', 0);

                $stats[$shopId]['renting'] = ($stats[$shopId]['renting'] ?? 0) + $slotOn;
                $stats[$shopId]['total']   = ($stats[$shopId]['total'] ?? 0) + ($slotOn + $slotOff);
            }

            $page++;
        } while (true);

        if (empty($stats)) {
            Log::warning('⚠ CK returned empty stats - keep old data');
            return; // ❗ KHÔNG XOÁ DB
        }

        // 2) Upsert trước
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

        // 3) Xoá những shop không còn trong API (nếu bạn muốn)
        ShopRentalStat::whereNotIn('shop_id', array_keys($stats))->delete();

        Log::info('✅ Shop rental stats synced: ' . count($stats) . ' shops');
    }

}
