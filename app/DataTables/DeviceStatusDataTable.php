<?php

namespace App\DataTables;

use App\DataTables\Core\BaseDatable;
use App\Models\DeviceStatus;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class DeviceStatusDataTable extends BaseDatable
{
    private const ASSIGNMENT_ASSIGNED = 'assigned';
    private const ASSIGNMENT_UNASSIGNED = 'unassigned';
    private const PRODUCT_TYPE_8_PIN = '8_pin';
    private const PRODUCT_TYPE_8_PIN_SCREEN = '8_pin_screen';
    private const PRODUCT_TYPE_32_PIN = '32_pin';

    private const ADVANCED_EXCLUDED_SHOP_CODES = [
        'VNS011A00607',
        'VNSBABP00049',
        'VNSBABP00048',
        'VNS011A00003',
        'CNSE09727',
        'VNS011A00005',
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
            ->addColumn('shop_id', fn (DeviceStatus $device) => $device->shop_code ?? '')
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
            $query->whereNotIn('code', self::ADVANCED_EXCLUDED_SHOP_CODES);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['product_type'])) {
            $this->applyProductTypeFilter($query, $filters['product_type']);
        }

        if (!empty($filters['assignment_status'])) {
            $this->applyAssignmentStatusFilter($query, $filters['assignment_status']);
        }

        return $query;
    }

    private function applyAssignmentStatusFilter($query, string $assignmentStatus): void
    {
        if ($assignmentStatus === self::ASSIGNMENT_ASSIGNED) {
            $query->whereNotNull('shop_code')
                ->where('shop_code', '!=', '');
            return;
        }

        if ($assignmentStatus === self::ASSIGNMENT_UNASSIGNED) {
            $query->where(function ($subQuery) {
                $subQuery->whereNull('shop_code')
                    ->orWhere('shop_code', '');
            });
        }
    }

    private function applyProductTypeFilter($query, string $productType): void
    {
        if ($productType === self::PRODUCT_TYPE_8_PIN) {
            $query->where('code', 'like', 'VN%')
                ->where('code', 'not like', 'VNS%');
            return;
        }

        if ($productType === self::PRODUCT_TYPE_8_PIN_SCREEN) {
            $query->where('code', 'like', 'VNS%')
                ->where('code', 'not like', 'VNSBABP%');
            return;
        }

        if ($productType === self::PRODUCT_TYPE_32_PIN) {
            $query->where('code', 'like', 'VNSBABP%');
        }
    }

    protected function getColumns(): array
    {
        return [
            Column::make('code')->title('Mã thiết bị'),
            Column::make('status')->title('Trạng thái'),
            Column::computed('shop_id')->title('Shop ID')->orderable(false)->searchable(false),
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
