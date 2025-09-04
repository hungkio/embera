<?php

namespace App\DataTables\Export;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OrderExportHandler implements WithMultipleSheets, ShouldAutoSize
{
    use Exportable;
    protected $collection;

    /**
     * OrderExportHandler constructor.
     *
     * @param Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Define the sheets for the Excel file.
     *
     * @return array
     */
    public function sheets(): array
    {
        return [
            'Order Details' => new OrderDetailsSheet($this->collection),
            'Transaction Summary' => new TransactionSummarySheet($this->collection),
        ];
    }
}

/**
 * Class for the Order Details sheet
 */
class OrderDetailsSheet implements ShouldAutoSize, \Maatwebsite\Excel\Concerns\FromView
{
    protected $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function view(): View
    {
        return view('admin.orders.export-detail', [
            'orders' => $this->collection
        ]);
    }
}

/**
 * Class for the Transaction Summary sheet
 */
class TransactionSummarySheet implements ShouldAutoSize, \Maatwebsite\Excel\Concerns\FromView
{
    protected $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function view(): View
    {
        return view('admin.orders.export-summary', [
            'orders' => $this->collection
        ]);
    }
}
