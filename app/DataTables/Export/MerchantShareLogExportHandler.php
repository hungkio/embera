<?php

namespace App\DataTables\Export;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MerchantShareLogExportHandler implements ShouldAutoSize, FromView
{
    use Exportable;

    protected Collection $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function view(): View
    {
        return view('admin.merchants.exports.share-logs-export', [
            'logs' => $this->collection
        ]);
    }
}
