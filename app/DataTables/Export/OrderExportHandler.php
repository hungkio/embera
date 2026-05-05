<?php

namespace App\DataTables\Export;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OrderExportHandler implements \Maatwebsite\Excel\Concerns\FromView, ShouldAutoSize
{
    use Exportable;
    protected $collection;
    protected $date;
    protected $date_from;
    protected $date_to;

    /**
     * OrderExportHandler constructor.
     *
     * @param Collection $collection
     */
    public function __construct(Collection $collection, $date = null, $date_from = null, $date_to = null)
    {
        $this->collection = $collection;
        $this->date = $date;
        $this->date_from = $date_from;
        $this->date_to = $date_to;
    }

    /**
     * Define the view for the Excel file.
     *
     * @return View
     */
    public function view(): View
    {
        return view('admin.orders.export-detail', [
            'orders' => $this->collection,
            'date' => $this->date,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
        ]);
    }
}
