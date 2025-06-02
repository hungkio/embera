<?php

namespace App\DataTables;

use App\DataTables\Core\BaseDatable;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use App\Models\SubscribeGroup;

class SubscribeGroupDataTable extends BaseDatable
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
            ->editColumn('created_at', fn (SubscribeGroup $subscribe_group) => formatDate($subscribe_group->created_at))
            ->addColumn('action', 'admin.subscribe_groups._tableAction')
            ->filterColumn('title', function($query, $keyword) {
                $query->where('title', 'like', "%{$keyword}%");
            })
            ->orderColumn('title', 'title $1')
            ->rawColumns(['action']);
    }

    public function query(SubscribeGroup $model): Builder
    {
        return $model->newQuery();
    }

    protected function getColumns(): array
    {
        return [
            Column::checkbox(''),
            Column::make('id')->title(__('STT'))->data('DT_RowIndex')->searchable(false),
            Column::make('name')->title(__('Tên group')),
            Column::make('created_at')->title(__('Thời gian tạo'))->searchable(false),
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
            'order' => [1, 'desc'],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'SubscribeGroup_'.date('YmdHis');
    }

    protected function getTableButton(): array
    {
        return [
            Button::make('create')->addClass('btn btn-success')->text('<i class="fal fa-plus-circle mr-2"></i>'.__('Tạo mới')),
            Button::make('bulkDelete')->addClass('btn btn-danger')->text('<i class="fal fa-trash-alt mr-2"></i>'.__('Xóa')),
            Button::make('reset')->addClass('btn bg-primary')->text('<i class="fal fa-undo mr-2"></i>'.__('Thiết lập lại')),
        ];
    }

}
