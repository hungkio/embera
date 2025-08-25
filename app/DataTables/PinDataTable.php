<?php

namespace App\DataTables;

use App\DataTables\Core\BaseDatable;
use App\Models\Pin;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class PinDataTable extends BaseDatable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', 'admin.pins._tableAction')
            ->editColumn('imei', fn(Pin $pin) => e($pin->imei ?? '-'))
            ->editColumn('serial_number', fn(Pin $pin) => e($pin->serial_number ?? '-'))
            ->addColumn('created_at', fn(Pin $pin) => optional($pin->created_at)->format('d/m/Y H:i'))
            ->filterColumn('imei', fn($query, $keyword) => $query->where('imei', 'like', "%$keyword%"))
            ->filterColumn('serial_number', fn($query, $keyword) => $query->where('serial_number', 'like', "%$keyword%"))
            ->orderColumn('created_at', 'created_at $1')
            ->rawColumns(['action']);
    }

    public function query(Pin $model)
    {
        return $model->newQuery()
            ->where('is_deleted', 0)
            ->when($this->request->filled('date_from') && $this->request->filled('date_to'), function ($q) {
                $q->whereBetween('created_at', [
                    $this->request->date_from,
                    $this->request->date_to,
                ]);
            });
    }

    protected function getColumns(): array
    {
        return [
            Column::checkbox(''),
            Column::make('imei')->title('IMEI'),
            Column::make('serial_number')->title('Serial Number'),
            Column::make('created_at')->title('Tạo lúc'),
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
            'order' => [4, 'desc'],
            'pageLength' => 25,
        ];
    }

    protected function getTableButton(): array
    {
        return [
            Button::make('create')->addClass('btn btn-success')->text('<i class="fal fa-plus-circle mr-2"></i>' . __('Tạo mới')),
            Button::make('selected')
                ->addClass('btn bg-teal-400 import')
                ->text('<i class="icon-compose mr-2"></i>' . __('Import')),
        ];
    }

    protected function filename(): string
    {
        return 'Pins_' . now()->format('YmdHis');
    }
}
