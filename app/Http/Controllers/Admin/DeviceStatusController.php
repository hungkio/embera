<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DataTables\DeviceStatusDataTable;
use App\Jobs\SyncDeviceStatusJob;
use App\Models\Order;
use Illuminate\Http\Request;

class DeviceStatusController extends Controller
{
    public function index(DeviceStatusDataTable $dataTable, Request $request)
    {
        $shopList = Order::distinct()->pluck('rental_shop')->filter()->sort()->toArray();

        return $dataTable->with([
            'filters' => $request->only(['employee_name', 'rental_shop', 'merchant_name', 'date_from', 'date_to']),
        ])->render('admin.devices.index', [
            'shopNameList' => $shopList,
            'filters' => $request->only([
                'shop_name'
            ]),
        ]);
    }

    public function syncNow()
    {
        dispatch(new SyncDeviceStatusJob()); // chạy nền
        return response()->json([
            'message' => 'Đã gửi yêu cầu đồng bộ! Hệ thống sẽ chạy nền trong vài phút.'
        ]);
    }
}
