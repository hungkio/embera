<?php

namespace App\DataTables;

use App\DataTables\Core\BaseDatable;
use App\Domain\Admin\Models\Admin;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;

class AdminDataTable extends BaseDatable
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
            ->addColumn('full_name', fn (Admin $admin) => view('admin.admins._tableFullName', compact('admin')))
            ->addColumn('roles', fn (Admin $admin) => $admin->roles->implode('display_name', ', '))
            ->editColumn('created_at', fn (Admin $admin) => formatDate($admin->created_at))
            ->orderColumn('full_name',
                fn($query, $direction) => $query->orderByRaw("CONCAT(first_name, ' ', last_name) $direction")
            )
            ->filterColumn('full_name', function($query, $keyword) {
                $query->whereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$keyword}%"]);
            })
            ->addColumn('action', 'admin.admins._tableAction');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Admin $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Admin $model)
    {
        return $model->newQuery()->with('media', 'roles.translation');
    }

    protected function getColumns(): array
    {
        return [
            Column::checkbox(''),
            Column::make('id')->title(__('STT'))->data('DT_RowIndex')->searchable(false),
            Column::make('full_name')->title(__('Họ tên'))->searchable(),
            Column::make('email')->title(__('Email')),
            Column::make('roles')->title(__('Quyền')),
            Column::make('created_at')->title(__('Thời gian tạo'))->searchable(false),
            Column::computed('action')
                ->title(__('Tác vụ'))
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center'),
        ];
    }

    protected function getBuilderParameters(): array
    {
        return [
            'order' => [5, 'desc'],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Admin_'.date('YmdHis');
    }

    protected function getTableButton(): array
    {
        return [
            Button::make('create')->addClass('btn btn-success d-none')->text('<i class="fal fa-plus-circle mr-2"></i>'.__('Tạo mới')),
            Button::make('bulkDelete')->addClass('btn bg-danger d-none')->text('<i class="fal fa-trash-alt mr-2"></i>'.__('Xóa')),
            Button::make('export')->addClass('btn bg-blue')->text('<i class="fal fa-download mr-2"></i>'.__('Xuất')),
            Button::make('print')->addClass('btn bg-blue')->text('<i class="fal fa-print mr-2"></i>'.__('In')),
            Button::make('reset')->addClass('btn bg-blue')->text('<i class="fal fa-undo mr-2"></i>'.__('Thiết lập lại')),
        ];
    }
}
