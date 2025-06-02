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
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', 'admin.orders._tableAction')
            ->editColumn('order_number', fn(Order $order) => $order->order_number)
            ->editColumn('order_bills_vnd', fn(Order $order) => number_format($order->order_bills_vnd, 0, ',', '.') . ' VND')
            ->editColumn('shop_name', fn(Order $order) => $order->shop_name)
            ->editColumn('region', fn(Order $order) => $order->region)
            ->editColumn('city', fn(Order $order) => $order->city)
            ->editColumn('area', fn(Order $order) => $order->area)
            ->editColumn('location', fn(Order $order) => $order->location)
            ->editColumn('status_of_order', fn(Order $order) => $order->status_of_order)
            ->filterColumn('order_number', function ($query, $keyword) {
                $query->where('order_number', 'like', "%$keyword%");
            })
            ->filterColumn('shop_name', function ($query, $keyword) {
                $query->where('shop_name', 'like', "%$keyword%");
            })
            ->filterColumn('region', function ($query, $keyword) {
                $query->where('region', 'like', "%$keyword%");
            })
            ->filterColumn('city', function ($query, $keyword) {
                $query->where('city', 'like', "%$keyword%");
            })
            ->filterColumn('area', function ($query, $keyword) {
                $query->where('area', 'like', "%$keyword%");
            })
            ->filterColumn('location', function ($query, $keyword) {
                $query->where('location', 'like', "%$keyword%");
            })
            ->filterColumn('status_of_order', function ($query, $keyword) {
                $query->where('status_of_order', 'like', "%$keyword%");
            })
            ->filterColumn('when_to_rent', function ($query, $keyword) {
                $query->whereDate('when_to_rent', $keyword);
            })
            ->orderColumn('when_to_rent', 'when_to_rent $1')
            ->rawColumns(['total_revenue', 'action']);

    }

    public function query(Order $model)
    {
        $query = $model->newQuery();

        $filters = $this->request->all();

        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $query->whereBetween('when_to_rent', [
                Carbon::parse($filters['date_from'])->startOfDay(),
                Carbon::parse($filters['date_to'])->endOfDay(),
            ]);
        }

        if (!empty($filters['staff'])) {
            $query->where('staff_name', $filters['staff']);
        }

        if (!empty($filters['shop_type'])) {
            $query->where('shop_type', $filters['shop_type']);
        }

        if (!empty($filters['region'])) {
            $query->where('region', $filters['region']);
        }

        if (!empty($filters['city'])) {
            $query->where('city', $filters['city']);
        }

        if (!empty($filters['shop_name'])) {
            $query->where('shop_name', $filters['shop_name']);
        }

        return $query;
    }

    protected function getColumns(): array
    {
        return [
            Column::checkbox(''),
            Column::make('when_to_rent')->title('Thời gian thuê'),
            Column::make('when_to_return')->title('Thời gian trả'),
            Column::make('shop_name')->title('Tên shop'),
            Column::make('region')->title('Khu vực'),
            Column::make('city')->title('Thành phố'),
            Column::make('area')->title('Địa điểm'),
            Column::make('location')->title('Địa chỉ'),
            Column::make('order_bills_vnd')->title('Doanh thu'),
            Column::make('refund')->title('Hoàn lại'),
            Column::make('payment_channel')->title('Kênh thanh toán'),
            Column::make('status_of_order')->title('Trạng thái đơn'),
            Column::computed('action')
                ->title(__('Tác vụ'))
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

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Orders_'.date('YmdHis');
    }

    protected function getTableButton(): array
    {
        return [
            Button::make('bulkDelete')->addClass('btn btn-danger')->text('<i class="fal fa-trash-alt mr-2"></i>'.__('Xóa')),
            Button::make('export')->addClass('btn btn-primary')->text('<i class="fal fa-download mr-2"></i>'.__('Xuất')),
            Button::make('reset')->addClass('btn bg-primary')->text('<i class="fal fa-undo mr-2"></i>'.__('Thiết lập lại')),
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
