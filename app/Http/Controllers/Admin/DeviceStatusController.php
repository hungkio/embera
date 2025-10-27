<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DataTables\DeviceStatusDataTable;
use App\Jobs\SyncDeviceStatusJob;

class DeviceStatusController extends Controller
{
    public function index(DeviceStatusDataTable $dataTable)
    {
        return $dataTable->render('admin.devices.index');
    }

    public function syncNow()
    {
        dispatch(new SyncDeviceStatusJob()); // chạy nền
        return response()->json([
            'message' => 'Đã gửi yêu cầu đồng bộ! Hệ thống sẽ chạy nền trong vài phút.'
        ]);
    }
}
