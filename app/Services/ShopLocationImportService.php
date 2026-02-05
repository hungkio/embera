<?php

namespace App\Services;

use App\Models\ShopLocation;
use Illuminate\Support\Facades\DB;

class ShopLocationImportService
{
    public function import(array $json)
    {
        $shops = $json['data']['shops'] ?? [];

        DB::transaction(function () use ($shops) {

            $incomingShopIds = [];

            foreach ($shops as $shop) {
                $profile = $shop['profile'] ?? [];

                if (empty($profile['shop_id'])) {
                    continue;
                }

                $incomingShopIds[] = $profile['shop_id'];

                ShopLocation::updateOrCreate(
                    ['shop_id' => $profile['shop_id']],
                    [
                        'shop_name' => $profile['shop_name'] ?? null,
                        'lat'       => $profile['lat'] ?? null,
                        'lng'       => $profile['lng'] ?? null,
                        'active'    => true,
                    ]
                );
            }

            ShopLocation::whereNotIn('shop_id', $incomingShopIds)->delete();
        });
    }
}
