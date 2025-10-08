<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\MerchantShareLogDataTable;

class MerchantShareLogController
{
    public function index(MerchantShareLogDataTable $dataTable)
    {
        return $dataTable->render('admin.merchants.share-logs');
    }

   public function detail($id)
   {
       $log = \App\Models\MerchantShareLog::with(['merchant'])->findOrFail($id);

       return view('admin.merchants.share-log-detail', compact('log'));
   }

}
