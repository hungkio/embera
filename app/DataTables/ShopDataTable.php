<?php

namespace App\DataTables;

use App\DataTables\Core\BaseDatable;
use App\Models\Shop;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class ShopDataTable extends BaseDatable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function (Shop $shop) {
                return view('admin.shops._tableAction', ['id' => $shop->id])->render();
            })
            ->editColumn('shop_name', fn(Shop $shop) => $shop->shop_name)
            ->editColumn('address', fn(Shop $shop) => $shop->address)
            ->editColumn('shop_type', fn(Shop $shop) => $shop->shop_type)
            ->editColumn('contact_phone', fn(Shop $shop) => $shop->contact_phone)
            ->editColumn('share_rate', function (Shop $shop) {
                return $shop->share_rate_type === 'fixed'
                    ? number_format($shop->share_rate, 0) . ' VNĐ'
                    : number_format($shop->share_rate, 0) . ' %';
            })
            ->editColumn('share_rate_type', fn(Shop $shop) =>
            $shop->share_rate_type === 'fixed' ? 'Doanh thu (VNĐ)' : 'Phần trăm (%)'
            )
            ->editColumn('strategy', fn(Shop $shop) => $shop->strategy ?? '-')
            ->editColumn('area', fn(Shop $shop) => $shop->area ?? '-')
            ->editColumn('city', fn(Shop $shop) => $shop->city ?? '-')
            ->editColumn('region', fn(Shop $shop) => $shop->region ?? '-')
            ->addColumn('contract_id', fn(Shop $shop) => $shop->contract_id ?? '-')
            ->editColumn('device_json', function (Shop $shop) {
                if (!$shop->device_json) return '-';
                $devices = $shop->device_json['devices'] ?? [];
                if (!is_array($devices) || empty($devices)) return '-';

                $html = '<div class="table-responsive">
                <table class="table table-bordered table-sm mb-0 text-center">
                    <thead class="thead-light">
                        <tr>
                            <th>Tên</th>
                            <th>Mã máy</th>
                            <th>Số lượng</th>
                            <th>Pin</th>
                        </tr>
                    </thead>
                    <tbody>';

                foreach ($devices as $device) {
                    $html .= '<tr>
                    <td>' . e($device['name'] ?? '-') . '</td>
                    <td>' . e($device['code'] ?? '-') . '</td>
                    <td>' . e($device['quantity'] ?? '-') . '</td>
                    <td>' . e($device['pin'] ?? '-') . '</td>
                  </tr>';
                }

                $html .= '</tbody></table></div>';

                return $html;
            })
            ->editColumn('is_bound', fn($shop) => $shop->is_bound ? 'Đã bind' : 'Chưa bind')
            ->filterColumn('is_bound', function ($query, $keyword) {
                if (str_contains($keyword, 'đã')) {
                    $query->where('is_bound', true);
                } elseif (str_contains($keyword, 'chưa')) {
                    $query->where('is_bound', false);
                }
            })
            ->addColumn('is_deleted', fn(Shop $shop) => $shop->is_deleted ? 'Đã xóa' : 'Hoạt động')
            ->filterColumn('is_deleted', fn($query, $keyword) => $query->where('is_deleted', $keyword === 'Đã xóa' ? 1 : 0))
            ->rawColumns(['action', 'device_json']);
    }

    public function query(Shop $model)
    {
        return $model->newQuery()
            ->with('contract')
            ->when(request('show_deleted') === 'yes', fn($q) => $q->where('is_deleted', 1))
            ->when(request('show_deleted') !== 'yes', fn($q) => $q->where('is_deleted', 0));
    }

    protected function getColumns(): array
    {
        return [
            Column::checkbox(''),
            Column::make('shop_name')->title('Tên cửa hàng'),
            Column::make('address')->title('Địa chỉ'),
            Column::make('shop_type')->title('Loại cửa hàng'),
            Column::make('contact_phone')->title('Số điện thoại'),
            Column::make('share_rate')->title('Lợi nhuận'),
            Column::make('share_rate_type')->title('Loại chia'),
            Column::make('strategy')->title('Chiến lược'),
            Column::make('area')->title('Khu vực'),
            Column::make('city')->title('Thành phố'),
            Column::make('region')->title('Vùng'),
            Column::make('contract_id')->title('Hợp đồng'),
            Column::make('is_bound')->title('Bind thiết bị'),
            Column::make('device_json')->title('Thiết bị'),
            Column::computed('action')->title('Tác vụ')->exportable(false)->printable(false)->width(60)->addClass('text-center'),
        ];
    }

    protected function getBuilderParameters(): array
    {
        return ['order' => [1, 'desc'], 'pageLength' => 25];
    }

    protected function getTableButton(): array
    {
        return [
            Button::make('create')->addClass('btn btn-success')->text('<i class="fal fa-plus-circle mr-2"></i>Tạo mới'),
            Button::make('bulkDelete')->addClass('btn bg-danger')->text('<i class="fal fa-trash-alt mr-2"></i>Xóa'),
            Button::make('export')->addClass('btn bg-blue')->text('<i class="fal fa-download mr-2"></i>Xuất'),
            Button::make('print')->addClass('btn bg-blue')->text('<i class="fal fa-print mr-2"></i>In'),
            Button::make('reset')->addClass('btn bg-blue')->text('<i class="fal fa-undo mr-2"></i>Thiết lập lại'),
        ];
    }

    protected function filename(): string
    {
        return 'Shops_' . now()->format('YmdHis');
    }
}
