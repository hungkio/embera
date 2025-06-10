<?php

namespace App\DataTables;

use App\DataTables\Core\BaseDatable;
use App\DataTables\Export\OrderExportHandler;
use App\Models\Order;
use Carbon\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class OrderDataTable extends BaseDatable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', 'admin.orders._tableAction')
            ->editColumn('order_number', fn(Order $order) => $order->order_number)
            ->editColumn('order_amount', fn(Order $order) => number_format($order->order_amount, 0, ',', '.') . ' VND')
            ->editColumn('refund_amount', fn(Order $order) => number_format($order->refund_amount, 0, ',', '.') . ' VND')
            ->editColumn('shop_name', fn(Order $order) => $order->rental_shop)
            ->editColumn('merchant_name', fn(Order $order) => $order->merchant_name)
            ->editColumn('employee_name', fn(Order $order) => $order->employee_name)
            ->editColumn('payment_time', fn(Order $order) => formatDate($order->payment_time))
            ->filterColumn('rental_shop', function ($query, $keyword) {
                $query->where('rental_shop', 'like', "%$keyword%");
            })
            ->filterColumn('merchant_name', function ($query, $keyword) {
                $query->where('merchant_name', 'like', "%$keyword%");
            })
            ->filterColumn('employee_name', function ($query, $keyword) {
                $query->where('employee_name', 'like', "%$keyword%");
            })
            ->filterColumn('order_status', function ($query, $keyword) {
                $query->where('order_status', 'like', "%$keyword%");
            })
            ->orderColumn('payment_time', 'payment_time $1')
            ->rawColumns(['action']);
    }

    public function query(Order $model)
    {
        $query = $model->newQuery();

        $filters = $this->request->all();

        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $query->whereBetween('payment_time', [
                Carbon::parse($filters['date_from'])->startOfDay(),
                Carbon::parse($filters['date_to'])->endOfDay(),
            ]);
        }

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['rental_shop_type'])) {
            $query->where('rental_shop_type', $filters['rental_shop_type']);
        }

        if (!empty($filters['rental_shop'])) {
            $query->where('rental_shop', $filters['rental_shop']);
        }

        if (!empty($filters['merchant_name'])) {
            $query->where('merchant_name', $filters['merchant_name']);
        }

        if (!empty($filters['payment_channel'])) {
            $query->where('payment_channels', $filters['payment_channel']);
        }

        if (!empty($filters['order_amount'])) {
            if ($filters['order_amount'] == 1) {
                $query->where('order_amount', '>', 0);
            }

            if ($filters['order_amount'] == 2) {
                $query->where('order_amount', '<', 0);
            }
        }

        return $query;
    }

    protected function getColumns(): array
    {
        return [
            Column::checkbox(''),
            Column::make('payment_id')->title('Payment ID'),
            Column::make('rental_time')->title('Rental Time'),
            Column::make('return_time')->title('Return Time'),
            Column::make('rental_shop')->title('Rental Shop'),
            Column::make('return_shop')->title('Return Shop'),
            Column::make('order_amount')->title('Order Amount'),
            Column::make('order_status')->title('Order Status'),
            Column::make('merchant_name')->title('Merchant Name'),
            Column::make('employee_name')->title('Employee Name'),
            Column::make('payment_time')->title('Payment Time'),
            Column::make('payment_channels')->title('Payment Channel'),
            Column::computed('action')
                ->title(__('Actions'))
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    protected function getBuilderParameters(): array
    {
        return [
            'order' => [2, 'desc'],
            'pageLength' => 50,
        ];
    }

    protected function filename(): string
    {
        return 'Orders_' . date('YmdHis');
    }

    protected function getTableButton(): array
    {
        return [
            Button::make('bulkDelete')->addClass('btn btn-danger')->text('<i class="fal fa-trash-alt mr-2"></i>'.__('Delete')),
            Button::make('export')->addClass('btn btn-primary')->text('<i class="fal fa-download mr-2"></i>'.__('Export')),
            Button::make('reset')->addClass('btn bg-primary')->text('<i class="fal fa-undo mr-2"></i>'.__('Reset')),
        ];
    }

    protected function buildExcelFile()
    {
        $this->request()->merge(['length' => -1]);
        $source = app()->call([$this, 'query']);
        $source = $this->applyScopes($source);

        return new OrderExportHandler($source->get());
    }
}
