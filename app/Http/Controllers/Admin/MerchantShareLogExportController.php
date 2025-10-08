<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MerchantShareLogExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class MerchantShareLogExportController
{
    public function export(Request $request)
    {
        $fileName = 'Merchant_Share_Logs_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new MerchantShareLogExport, $fileName);
    }
}
