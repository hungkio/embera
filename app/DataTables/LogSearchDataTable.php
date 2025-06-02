<?php

namespace App\DataTables;

use App\DataTables\Core\BaseDatable;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use App\Domain\LogSearch\Models\LogSearch;

class LogSearchDataTable extends BaseDatable
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
            ->editColumn('created_at', fn (LogSearch $logSearch) => formatDate($logSearch->created_at))
            ->editColumn('updated_at', fn (LogSearch $logSearch) => formatDate($logSearch->updated_at));

    }

    public function query(LogSearch $model): Builder
    {
        return $model->newQuery();
    }

    protected function getColumns(): array
    {
        return [
            Column::make('id')->title(__('STT'))->data('DT_RowIndex')->searchable(false),
            Column::make('key_word')->title(__('Từ khoá'))->searchable(true),
            Column::make('hits')->title(__('Số lượt tìm kiếm'))->searchable(true),
            Column::make('created_at')->title(__('Thời gian tìm kiếm lần đầu'))->searchable(true),
            Column::make('updated_at')->title(__('Thời gian tìm kiếm lần cuối cùng'))->searchable(true),
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
        return 'Log_Search_'.date('YmdHis');
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
