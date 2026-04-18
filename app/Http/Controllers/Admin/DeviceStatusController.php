<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DataTables\DeviceStatusDataTable;
use App\Jobs\SyncDeviceStatusJob;
use Illuminate\Http\Request;

class DeviceStatusController extends Controller
{
    public function index(DeviceStatusDataTable $dataTable, Request $request)
    {
        return $dataTable->with([
            'filters' => $request->only(['filter_mode', 'status', 'shop_keyword', 'product_type']),
        ])->render('admin.devices.index', [
            'filters' => $request->only([
                'filter_mode',
                'status',
                'shop_keyword',
                'product_type',
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
