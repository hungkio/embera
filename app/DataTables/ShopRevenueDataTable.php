<?php

namespace App\DataTables;

use App\DataTables\Core\BaseDatable;
use App\Models\Shop;
use App\Models\Order;
use App\Models\DeviceStatus;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\DB;

class ShopRevenueDataTable extends BaseDatable
{
    protected $startDate;
    protected $endDate;

    public function __construct()
    {
        parent::__construct();
        $this->startDate = request()->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $this->endDate = request()->input('end_date', now()->format('Y-m-d'));
    }

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('revenue', function (Shop $shop) {
                $totalRevenue = Order::where('rental_shop', $shop->shop_name)
                    ->whereBetween('payment_time', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
                    ->sum('order_amount');
                return number_format($totalRevenue, 0, ',', '.') . ' VNĐ';
            })
            ->addColumn('device_summary', function (Shop $shop) {
                $deviceData = Order::where('rental_shop', $shop->shop_name)
                    ->whereBetween('payment_time', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
                    ->groupBy('rental_equipment_id')
                    ->select('rental_equipment_id', DB::raw('SUM(order_amount) as total_revenue'))
                    ->get()
                    ->map(function ($item) {
                        $deviceStatus = DeviceStatus::where('equip_id', $item->rental_equipment_id)->first();
                        $status = $deviceStatus ? $deviceStatus->status : 'offline';

                        return [
                            'code' => $item->rental_equipment_id,
                            'status' => $status,
                            'revenue' => number_format($item->total_revenue, 0, ',', '.') . ' VNĐ',
                        ];
                    });

                if ($deviceData->isEmpty()) {
                    return '-';
                }

                $html = '<div class="table-responsive"><table class="table table-bordered table-sm mb-0 text-center"><thead class="thead-light"><tr><th>Mã máy</th><th>Trạng thái</th><th>Doanh thu</th></tr></thead><tbody>';
                foreach ($deviceData as $row) {
                    $html .= '<tr><td>' . e($row['code']) . '</td><td>' . e($row['status']) . '</td><td>' . e($row['revenue']) . '</td></tr>';
                }
                $html .= '</tbody></table></div>';

                return $html;
            })
            ->editColumn('shop_name', fn(Shop $shop) => e($shop->shop_name))
            ->filterColumn('revenue', function ($query, $keyword) {
                $query->whereHas('orders', function ($q) use ($keyword) {
                    $q->where('order_amount', 'like', "%$keyword%");
                });
            })
            ->filterColumn('device_summary', function ($query, $keyword) {
                $query->whereHas('orders', function ($q) use ($keyword) {
                    $q->where('rental_equipment_id', 'like', "%$keyword%")
                        ->orWhere('order_amount', 'like', "%$keyword%");
                });
            })
            ->rawColumns(['revenue', 'device_summary'])
            ->with('start_date', $this->startDate)
            ->with('end_date', $this->endDate);
    }

    public function query(Shop $model)
    {
        return $model->newQuery()
            ->where('is_deleted', 0);
    }

    protected function getColumns(): array
    {
        return [
            Column::checkbox(''),
            Column::make('shop_name')->title('Tên cửa hàng'),
            Column::make('revenue')->title('Doanh thu'),
            Column::make('device_summary')->title('Thiết bị'),
        ];
    }

    protected function getBuilderParameters(): array
    {
        return ['order' => [1, 'asc'], 'pageLength' => 25];
    }

    protected function getTableButton(): array
    {
        return [
            Button::make('export')->addClass('btn bg-blue')->text('<i class="fal fa-download mr-2"></i>Xuất'),
            Button::make('print')->addClass('btn bg-blue')->text('<i class="fal fa-print mr-2"></i>In'),
            Button::make('reset')->addClass('btn bg-blue')->text('<i class="fal fa-undo mr-2"></i>Thiết lập lại'),
        ];
    }

    protected function filename(): string
    {
        return 'ShopRevenue_' . now()->format('YmdHis');
    }
}
