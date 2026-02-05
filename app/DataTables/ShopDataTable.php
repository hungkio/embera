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
            ->addColumn('contract', function (Shop $shop) {
                if (!$shop->contract) {
                    return '';
                }
                $url = route('admin.contracts.show', $shop->contract->id);
                $num = e($shop->contract->contract_number);
                return "<a href=\"{$url}\" target=\"_blank\">{$num}</a>";
            })
            ->addColumn('merchant', function (Shop $shop) {
                if (!$shop->contract && !@$shop->contract->merchant) {
                    return '';
                }
                if (@$shop->contract->merchant) {
                    $url = route('admin.merchants.edit', $shop->contract->merchant->id);
                } else {
                    return '';
                }
                $num = e($shop->contract->merchant->username);
                return "<a href=\"{$url}\" target=\"_blank\">{$num}</a>";
            })->addColumn('action', function (Shop $shop) {
                return $shop ? view('admin.shops._tableAction', ['id' => $shop->id])->render() : '';
            })
            ->filterColumn('contract', function ($query, $keyword) {
                $query->whereHas('contract', function ($q) use ($keyword) {
                    $q->where('contract_number', 'like', "%$keyword%");
                });
            })
            ->filterColumn('merchant', function ($query, $keyword) {
                $query->where('merchants.username', 'like', "%{$keyword}%");
            })
            ->editColumn('shop_name', fn(Shop $shop) => $shop->shop_name)
            ->editColumn('address', fn(Shop $shop) => $shop->address)
            ->editColumn('shop_type', fn(Shop $shop) => $shop->shop_type)
            ->editColumn('share_rate', function (Shop $shop) {
                return $shop->share_rate_type === 'fixed'
                    ? number_format($shop->share_rate, 0) . ' VNĐ'
                    : number_format($shop->share_rate, 0) . ' %';
            })
            ->editColumn('share_rate_type', fn(Shop $shop) => $shop->share_rate_type === 'fixed' ? 'Doanh thu (VNĐ)' : 'Phần trăm (%)'
            )
            ->editColumn('strategy', fn(Shop $shop) => $shop->strategy ?? '-')
            ->editColumn('area', fn(Shop $shop) => $shop->area ?? '-')
            ->editColumn('city', fn(Shop $shop) => $shop->city ?? '-')
            ->editColumn('region', fn(Shop $shop) => $shop->region ?? '-')
            ->editColumn('device_json', function (Shop $shop) {
                if (!$shop->device_json) return '-';

                $devices = $shop->device_json['devices'] ?? [];
                if (!is_array($devices) || empty($devices)) return '-';

                $summary = [];

                foreach ($devices as $device) {
                    $name = strtoupper(trim($device['name'] ?? ''));
                    $code = trim($device['code'] ?? '');
                    $pin = (int)($device['pin'] ?? 0);

                    if (!$name) continue;

                    if (!isset($summary[$name])) {
                        $summary[$name] = [
                            'codes' => [],
                            'total_pin' => 0,
                            'count' => 0,
                        ];
                    }

                    if ($code) $summary[$name]['codes'][] = $code;
                    $summary[$name]['total_pin'] += $pin;
                    $summary[$name]['count']++;
                }

                $html = '<div class="table-responsive">
    <table class="table table-bordered table-sm mb-0 text-center">
        <thead class="thead-light">
            <tr>
                <th class="text-center">Tên thiết bị</th>
                <th class="text-center">Mã máy</th>
                <th class="text-center">Tổng số pin</th>
                <th class="text-center">Số lượng</th>
            </tr>
        </thead>
        <tbody>';


                foreach ($summary as $deviceName => $row) {
                    $codes = implode(', ', array_unique($row['codes']));
                    $html .= '<tr>
            <td>' . e($deviceName) . '</td>
            <td>' . e($codes) . '</td>
            <td>' . $row['total_pin'] . '</td>
            <td>' . $row['count'] . '</td>
        </tr>';
                }

                $html .= '</tbody></table></div>';

                return $html;
            })
            ->editColumn('is_bound', function ($shop) {
                $buttonClass = $shop->is_bound ? 'btn-success' : 'btn-warning';
                $label = $shop->is_bound ? 'Đã bind' : 'Chưa bind';
                $nextState = $shop->is_bound ? 0 : 1;

                return "<button class=\"dt-button btn $buttonClass toggle-bind\"
        data-id=\"$shop->id\" data-state=\"$nextState\">$label</button>";
            })
            ->filterColumn('is_bound', function ($query, $keyword) {
                if (str_contains($keyword, 'đã')) {
                    $query->where('is_bound', true);
                } elseif (str_contains($keyword, 'chưa')) {
                    $query->where('is_bound', false);
                }
            })
            ->addColumn('is_deleted', fn(Shop $shop) => $shop->is_deleted ? 'Đã xóa' : 'Hoạt động')
            ->filterColumn('is_deleted', fn($query, $keyword) => $query->where('is_deleted', $keyword === 'Đã xóa' ? 1 : 0))
            ->rawColumns([
                'action',
                'device_json',
                'is_bound',
                'contract',
                'merchant',
            ]);
    }

    public function query(Shop $model)
    {
        return $model->newQuery()
            ->with(['contract'])
            ->leftJoin('contracts', 'contracts.id', '=', 'shops.contract_id')
            ->leftJoin('merchants', 'merchants.id', '=', 'contracts.merchant_id')
            ->select('shops.*')
            ->when(request('show_deleted') === 'yes', fn($q) => $q->where('shops.is_deleted', 1))
            ->when(request('show_deleted') !== 'yes', fn($q) => $q->where('shops.is_deleted', 0));
    }

    protected function getColumns(): array
    {
        return [
            Column::computed('action')->title('Tác vụ')->exportable(false)->printable(false)->width(60)->addClass('text-center'),
            Column::make('contract')->title('Số hợp đồng'),
            Column::make('merchant')->title('Đối tác'),
            Column::make('shop_name')->title('Tên cửa hàng'),
            Column::make('is_bound')->title('Bind thiết bị'),
            Column::make('device_json')->title('Thiết bị'),
            Column::make('address')->title('Địa chỉ'),
            Column::make('shop_type')->title('Loại cửa hàng'),
            Column::make('share_rate')->title('Lợi nhuận'),
            Column::make('share_rate_type')->title('Loại chia'),
            Column::make('strategy')->title('Chiến lược'),
            Column::make('area')->title('Khu vực'),
            Column::make('city')->title('Thành phố'),
            Column::make('region')->title('Vùng'),
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

            Button::raw('<i class="fal fa-download mr-2"></i>Xuất danh sách shop')
                ->addClass('btn btn-primary')
                ->action("window.location='" . route('admin.shops.export_all') . "';"),

            Button::make('reset')->addClass('btn bg-blue')->text('<i class="fal fa-undo mr-2"></i>Thiết lập lại'),
        ];
    }

    protected function filename(): string
    {
        return 'Shops_' . now()->format('YmdHis');
    }
}
