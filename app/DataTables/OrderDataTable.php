<?php

namespace App\DataTables;

use App\DataTables\Core\BaseDatable;
use App\DataTables\Export\OrderExportHandler;
use App\Models\Order;
use Carbon\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\Schema;
use App\DataTables\Export\OrdersFullExport;

class OrderDataTable extends BaseDatable
{
    private const ACCOUNTING_PAYMENT_CHANNELS = ['balance', 'mbpay'];
    private const EXCLUDED_ACCOUNTING_ORDER_AMOUNTS = [240000, 364000];

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
            ->editColumn('payment_time', fn(Order $order) => $order->payment_time ? formatDate($order->payment_time) : '')
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
            ->filterColumn('order_number', function ($query, $keyword) {
                $query->where('order_number', 'like', "%$keyword%");
            })
            ->orderColumn('payment_time', 'payment_time $1')
            ->rawColumns(['action']);
    }

    public function query(Order $model)
    {
        $query = $this->applyAccountingOrderScope($model->newQuery());

        $filters = $this->request->all();

        if (empty($filters['date_from']) && empty($filters['date_to']) && !empty($filters['date_range'])) {
            if (str_contains($filters['date_range'], ' - ')) {
                [$filters['date_from'], $filters['date_to']] = explode(' - ', $filters['date_range']);
            } else {
                $filters['date_from'] = $filters['date_range'];
                $filters['date_to'] = $filters['date_range'];
            }
            $filters['date_from'] = trim($filters['date_from']);
            $filters['date_to'] = trim($filters['date_to']);
            $this->request->merge(['date_from' => $filters['date_from'], 'date_to' => $filters['date_to']]);
        }

        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $query->whereBetween('payment_time', [
                Carbon::parse($filters['date_from'])->startOfDay(),
                Carbon::parse($filters['date_to'])->endOfDay(),
            ]);
        }

        if (!empty($filters['staff'])) {
            $query->where('employee_name', $filters['staff']);
        }

        if (!empty($filters['shop_type'])) {
            $query->where('rental_shop_type', $filters['shop_type']);
        }

        if (!empty($filters['shop_name'])) {
            $query->whereIn('rental_shop', $filters['shop_name']);
        }

        if (!empty($filters['payment_channel'])) {
            $query->where('payment_channels', $filters['payment_channel']);
        }

        if (!empty($filters['order_amount'])) {
            if ($filters['order_amount'] == 1) {
                $query->where('order_amount', '>', 0);
            }

            if ($filters['order_amount'] == 2) {
                $query->where('order_amount', '<=', 0);
            }
        }

        if (@$filters['region']) {
            $query->where('region', $filters['region']);
        }
        if (@$filters['city']) {
            $query->where('city', $filters['city']);
        }
        if (@$filters['area']) {
            $query->where('area', $filters['area']);
        }
        if (@$filters['merchant_name']) {
            $query->whereIn('merchant_name', array_filter((array) $filters['merchant_name']));
        }

        return $query;
    }

    private function applyAccountingOrderScope($query)
    {
        return $query
            ->whereNotNull('payment_time')
            ->where('order_amount', '>', 0)
            ->whereNotIn('order_amount', self::EXCLUDED_ACCOUNTING_ORDER_AMOUNTS)
            ->whereIn('payment_channels', self::ACCOUNTING_PAYMENT_CHANNELS);
    }

    protected function getColumns(): array
    {
        return [
            Column::checkbox(''),
            Column::make('order_number')->title('Order Number'),
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
            Button::make('bulkDelete')->addClass('btn btn-danger')->text('<i class="fal fa-trash-alt mr-2"></i>' . __('Delete')),
            Button::make('export')->addClass('btn btn-primary')->text('<i class="fal fa-download mr-2"></i>' . __('Export')),
            Button::raw()
                ->addClass('btn btn-success')
                ->text('<i class="fal fa-file-excel mr-2"></i>Export Full')
                ->attr([
                    'data-export-full-url' => route('admin.orders.export-full'),
                ])
                ->action("function (e, dt, node, config) {
                var url = $(node).attr('data-export-full-url');
                var query = window.location.search || '';
                window.location.href = url + query;
            }"),
            Button::make('reset')->addClass('btn bg-primary')->text('<i class="fal fa-undo mr-2"></i>' . __('Reset')),
            Button::raw()->addClass('btn bg-primary btn-send-email')->text('<i class="fal fa-envelope mr-2"></i>' . __('Gửi Email Export')),
        ];
    }

    protected function buildExcelFile()
    {
        $this->request()->merge(['length' => -1]);
        $source = app()->call([$this, 'query']);
        $source = $this->applyScopes($source);

        $date = null;
        if ($this->request()->date_from == $this->request()->date_to) {
            $date = $this->request()->date_from;
        }
        return new OrderExportHandler($source->get(), $date, $this->request()->date_from, $this->request()->date_to);
    }

    public function getExportQuery()
    {
        $this->request()->merge(['length' => -1]);

        $query = app()->call([$this, 'query']);
        return $this->applyScopes($query);
    }

    public function buildExcelFileSendMail()
    {
        if (!$this->request()->date_from || !$this->request()->date_to) {
            $today = now()->format('Y-m-d');

            $this->request()->merge([
                'date_from' => $today,
                'date_to'   => $today,
            ]);
        }
        $this->request()->merge(['length' => -1]);
        $source = app()->call([$this, 'query']);
        $source = $this->applyScopes($source);

        $date = null;
        if ($this->request()->date_from == $this->request()->date_to) {
            $date = $this->request()->date_from;
        }
        return new OrderExportHandler($source->get(), $date, $this->request()->date_from, $this->request()->date_to);
    }

    public function buildExcelFileFull()
    {
        $this->request()->merge(['length' => -1]);

        $source = app()->call([$this, 'query']);
        $source = $this->applyScopes($source); // ✅ gọi được vì đang ở trong class

        $columns = Schema::getColumnListing((new \App\Models\Order)->getTable());

        return new OrdersFullExport($source, $columns);
    }
}
