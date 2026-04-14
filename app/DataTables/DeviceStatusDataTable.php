<?php

namespace App\DataTables;

use App\DataTables\Core\BaseDatable;
use App\Models\DeviceStatus;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class DeviceStatusDataTable extends BaseDatable
{
    private const ADVANCED_EXCLUDED_SHOP_CODES = [
        'VNS011A00607',
        'VNSBABP00049',
        'VNSBABP00048',
        'VNS011A00003',
    ];

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('status', fn (DeviceStatus $device) => $device->status === 'online'
                ? '<span class="badge bg-success">Online</span>'
                : '<span class="badge bg-danger">Offline</span>'
            )
            ->filterColumn('shop', function ($query, $keyword) {
                $query->whereHas('shop', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })
            ->addColumn('shop', fn (DeviceStatus $device) => $device->shop->name ?? '')
            ->editColumn('updated_at', fn ($d) => $d->updated_at?->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') ?? '-')
            ->rawColumns(['status']);
    }

    public function query(DeviceStatus $model)
    {
        $filters = $this->request->all();
        $query = $model->newQuery()->with('shop');

        if (!empty($filters['shop_name'])) {
            $query->whereHas('shop', function ($q) use ($filters) {
                $q->whereIn('name', $filters['shop_name']);
            });
        }

        if (!empty($filters['shop_keyword'])) {
            $query->whereHas('shop', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['shop_keyword'] . '%')
                    ->orWhere('code', 'like', '%' . $filters['shop_keyword'] . '%');
            });
        }

        if (($filters['filter_mode'] ?? 'default') === 'advanced') {
            $query->whereNotIn('shop_code', self::ADVANCED_EXCLUDED_SHOP_CODES);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    protected function getColumns(): array
    {
        return [
            Column::make('code')->title('Mã thiết bị'),
            Column::make('status')->title('Trạng thái'),
            Column::make('shop')->title('Cửa hàng'),
            Column::make('updated_at')->title('Thời gian cập nhật'),
        ];
    }

    protected function getBuilderParameters(): array
    {
        return [
            'order' => [1, 'desc'],
            'pageLength' => 25,
            'stateSave' => false,
            'destroy' => true,
        ];
    }

    protected function getTableButton(): array
    {
        return [
            Button::make('reload')
                ->addClass('btn bg-primary')
                ->text('<i class="fal fa-sync-alt mr-2"></i>' . __('Làm mới')),
            Button::make('export')->addClass('btn btn-primary')->text('<i class="fal fa-download mr-2"></i>' . __('Xuất')),
        ];
    }

    protected function filename(): string
    {
        return 'DeviceStatus_' . now()->format('YmdHis');
    }
}
