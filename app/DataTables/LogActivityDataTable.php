<?php

namespace App\DataTables;

use App\DataTables\Core\BaseDatable;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Spatie\Activitylog\Models\Activity;

class LogActivityDataTable extends BaseDatable
{
    const TABLE = [
        'App\Domain\Acl\Models\Role' => 'Vai trò',
        'admins' => 'Tài khoản',
        'App\Domain\Banner\Models\Banner' => 'Banner',
        'App\Domain\Page\Models\Page' => 'Trang',
        'App\Domain\Post\Models\Post' => 'Bài viết',
        'App\Domain\Taxonomy\Models\Taxonomy' => 'Loại danh mục',
        'App\Domain\Taxonomy\Models\Taxon' => 'Danh mục',
        'App\Domain\MailSetting\Models\MailSetting' => 'Chiến dịch gửi mail',
        'App\Domain\Menu\Models\Menu' => 'Loại menu',
        'App\Domain\Menu\Models\MenuItem' => 'Menu',
        null => 'Cài đặt'
    ];
    const ACTION = [
        'create' => 'Thêm',
        'update' => 'Sửa',
        'delete' => 'Xoá',
    ];
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
            ->addColumn('causer', fn (Activity $activity) => $activity->causer->fullname ?? '')
            ->editColumn('description', fn (Activity $activity) => self::ACTION[$activity->description])
            ->editColumn('subject_type', fn (Activity $activity) => self::TABLE[$activity->subject_type])
            ->editColumn('causer_type', fn (Activity $activity) => $activity->causer ? ($activity->causer->roles->implode('display_name', ', ') ?? $activity->causer_type) : '')
            ->editColumn('created_at', fn (Activity $activity) => formatDate($activity->created_at))
            ->addColumn('action', 'admin.log_activities._tableAction')
            ->filterColumn('causer', function($query, $keyword) {
                $query->whereRaw("CONCAT(admins.first_name, ' ', admins.last_name) like ?", ["%{$keyword}%"]);
            })
            ->orderColumn('causer',
                fn($query, $direction) => $query->orderByRaw("CONCAT(admins.first_name, ' ', admins.last_name) $direction")
            )
            ->rawColumns(['action', 'status']);
    }

    public function query(Activity $model): Builder
    {
        return $model->newQuery()->select('activity_log.*')->join('admins', 'activity_log.causer_id', '=', 'admins.id');
    }

    protected function getColumns(): array
    {
        return [
            Column::checkbox(''),
            Column::make('id')->title(__('STT'))->data('DT_RowIndex')->searchable(false),
            Column::make('causer')->title(__('Người tạo')),
            Column::make('description')->title(__('Hành động')),
            Column::make('subject_type')->title(__('Bảng tác động')),
            Column::make('causer_type')->title(__('Role người thao tác')),
            Column::make('created_at')->title(__('Thời gian thao tác'))->searchable(false),
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
            'order' => [6, 'desc'],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Activity_'.date('YmdHis');
    }

    protected function getTableButton(): array
    {
        return [
            Button::make('bulkDelete')->addClass('btn bg-danger')->text('<i class="fal fa-trash-alt mr-2"></i>'.__('Xóa')),
            Button::make('export')->addClass('btn bg-blue')->text('<i class="fal fa-download mr-2"></i>'.__('Xuất')),
            Button::make('print')->addClass('btn bg-blue')->text('<i class="fal fa-print mr-2"></i>'.__('In')),
            Button::make('reset')->addClass('btn bg-blue')->text('<i class="fal fa-undo mr-2"></i>'.__('Thiết lập lại')),
        ];
    }
}
