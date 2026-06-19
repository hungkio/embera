<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceDirectoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DeviceStatus::query()
            ->whereNotNull('shop_code')
            ->where('shop_code', '!=', '')
            ->with('shop');

        if ($request->filled('keyword')) {
            $keyword = trim((string) $request->input('keyword'));
            $query->where(function ($subQuery) use ($keyword) {
                $subQuery->where('code', 'like', '%' . $keyword . '%')
                    ->orWhere('equip_id', 'like', '%' . $keyword . '%')
                    ->orWhere('shop_code', 'like', '%' . $keyword . '%')
                    ->orWhereHas('shop', function ($q) use ($keyword) {
                        $q->where('name', 'like', '%' . $keyword . '%');
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $devices = $query
            ->orderBy('code')
            ->get()
            ->map(function (DeviceStatus $device) {
                return [
                    'device_code' => $device->code,
                    'equip_id' => $device->equip_id,
                    'status' => $device->status,
                    'shop_code' => $device->shop_code,
                    'shop_name' => $device->shop?->name,
                    'updated_at' => $device->updated_at?->toDateTimeString(),
                ];
            })
            ->values();

        return response()->json([
            'total' => $devices->count(),
            'data' => $devices,
        ]);
    }
}
