<?php

namespace App\DataTables;

use App\DataTables\Core\BaseDatable;
use App\Models\DeviceStatus;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class DeviceStatusDataTable extends BaseDatable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('equip_id', fn(DeviceStatus $device) => e($device->equip_id ?? '-'))
            ->editColumn('status', fn(DeviceStatus $device) =>
                $device->status === 'online'
                    ? '<span class="badge bg-success">Online</span>'
                    : '<span class="badge bg-danger">Offline</span>'
            )
            ->editColumn('updated_at', fn($d) => $d->updated_at?->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') ?? '-')
            ->rawColumns(['status']);
    }

    public function query(DeviceStatus $model)
    {
        return $model->newQuery()->orderBy('id', 'asc'); // Hiển thị từ ID 1 trở đi
    }

    protected function getColumns(): array
    {
        return [
            Column::checkbox(''),
            Column::make('id')->title('ID'),
            Column::make('equip_id')->title('Mã thiết bị (Equip ID)'),
            Column::make('status')->title('Trạng thái'),
            Column::make('updated_at')->title('Thời gian cập nhật'),
        ];
    }

    protected function getBuilderParameters(): array
    {
        return [
            'order' => [1, 'asc'], // sắp xếp theo ID tăng dần
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
            Button::make('selected')
                ->addClass('btn bg-teal-400 js-sync-now')
                ->text('<i class="fal fa-bolt mr-2"></i>' . __('Đồng bộ ngay')),
        ];
    }

    protected function filename(): string
    {
        return 'DeviceStatus_' . now()->format('YmdHis');
    }
}
