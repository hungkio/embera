<?php

namespace App\DataTables\Export;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PageExportHandler implements ShouldAutoSize, FromView
{
    use Exportable;
    protected $collection;

    /**
     * DataTablesExportHandler constructor.
     *
     * @param Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function view(): View
    {
        return view('admin.pages.export', [
            'data' => $this->collection
        ]);
    }
}
