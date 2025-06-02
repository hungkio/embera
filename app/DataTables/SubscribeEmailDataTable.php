<?php

namespace App\DataTables;

use App\DataTables\Core\BaseDatable;
use App\Domain\SubscribeEmail\Models\SubscribeEmail;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class SubscribeEmailDataTable extends BaseDatable
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
            ->addColumn('email', fn (SubscribeEmail $subscribeEmail) => view('admin.contacts._tableTitleEmail', compact('subscribeEmail')))
            ->orderColumn('email', 'email $1')
            ->filterColumn('email', function($query, $keyword) {
                $query->where('email', 'like', "%{$keyword}%");
            })
            ->editColumn('created_at', fn (SubscribeEmail $subscribeEmail) => formatDate($subscribeEmail->created_at));
    }

    public function query(SubscribeEmail $model): Builder
    {
        return $model->newQuery();
    }

    protected function getColumns(): array
    {
        return [
            Column::make('id')->title(__('STT'))->data('DT_RowIndex')->searchable(false),
            Column::make('email')->title(__('Email'))->searchable(true),
            Column::make('created_at')->title(__('Thời gian tạo'))->searchable(true),
        ];
    }

    protected function getBuilderParameters(): array
    {
        return [
            'order' => [2, 'desc'],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Subscribe_email_'.date('YmdHis');
    }

    protected function getTableButton(): array
    {
        return [
            Button::make('export')->addClass('btn bg-blue')->text('<i class="fal fa-download mr-2"></i>'.__('Xuất')),
            Button::make('print')->addClass('btn bg-blue')->text('<i class="fal fa-print mr-2"></i>'.__('In')),
            Button::make('reset')->addClass('btn bg-blue')->text('<i class="fal fa-undo mr-2"></i>'.__('Thiết lập lại')),
        ];
    }
}
