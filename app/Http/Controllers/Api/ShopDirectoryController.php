<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShopDirectoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Shop::query()
            ->with([
                'contract.merchant',
                'merchant',
            ]);
            // ->where('is_deleted', false);

        if ($request->filled('keyword')) {
            $keyword = trim((string) $request->input('keyword'));

            $query->where(function ($subQuery) use ($keyword) {
                $subQuery->where('shop_name', 'like', '%' . $keyword . '%')
                    ->orWhere('address', 'like', '%' . $keyword . '%')
                    ->orWhere('contact_phone', 'like', '%' . $keyword . '%');
            });
        }

        if ($request->boolean('has_contract')) {
            $query->whereNotNull('contract_id');
        }

        $shops = $query
            ->orderBy('shop_name')
            ->get()
            ->map(function (Shop $shop) {
                $devices = collect($shop->device_json['devices'] ?? [])
                    ->filter(fn ($device) => is_array($device))
                    ->map(function (array $device) {
                        return [
                            'name' => $device['name'] ?? null,
                            'code' => $device['code'] ?? null,
                            'pin' => isset($device['pin']) ? (int) $device['pin'] : null,
                        ];
                    })
                    ->values()
                    ->all();

                return [
                    'shop_name' => $shop->shop_name,
                    'address' => $shop->address,
                    'has_contract' => (bool) $shop->contract,
                    'contract_representative' => $shop->contract?->customer_name,
                    'phone' => $shop->contract?->phone ?: $shop->contact_phone,
                    'device_codes' => collect($devices)->pluck('code')->filter()->values()->all(),
                ];
            })
            ->values();

        return response()->json([
            'total' => $shops->count(),
            'data' => $shops,
        ]);
    }
}
