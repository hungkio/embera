<?php

namespace App\DataTables;

use App\Models\MBTransaction;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use App\DataTables\Core\BaseDatable;
use Carbon\Carbon;

class MBTransactionDataTable extends BaseDatable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('date_in', fn($row) => optional($row->date_in)->format('Y-m-d H:i:s'))
            ->editColumn('date_out', fn($row) => optional($row->date_out)->format('Y-m-d H:i:s'))
            ->editColumn('amount_in', fn($row) => number_format($row->amount_in))
            ->editColumn('amount_out', fn($row) => number_format($row->amount_out))
            ->editColumn('revenue', fn($row) => '<strong>' . number_format($row->revenue) . '</strong>')
            ->rawColumns(['revenue']);
    }

public function query(MBTransaction $model)
    {
        $query = $model->newQuery();
        $filters = $this->request->all();


        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $query->whereBetween('date_in', [
                Carbon::parse($filters['date_from'])->startOfDay(),
                Carbon::parse($filters['date_to'])->endOfDay(),
            ]);
        }

        if (!empty($filters['code'])) {
            $query->where('code_in', 'like', '%' . $filters['code'] . '%');
        }

        return $query;
    }

    protected function getColumns(): array
{
    return [
        Column::checkbox(''),
        Column::make('code_in')->title('Mã GD nạp'),
        Column::make('date_in')->title('Thời gian nạp'),
        Column::make('ft_code_in')->title('FT nạp'),
        Column::make('amount_in')->title('Tiền nạp'),
        Column::make('ft_code_out')->title('FT hoàn'),
        Column::make('date_out')->title('Thời gian hoàn'),
        Column::make('amount_out')->title('Tiền hoàn'),
        Column::make('revenue')->title('Doanh thu'),
    ];
}

    protected function getBuilderParameters(): array
{
    return [
        'order' => [[2, 'desc']],
        'pageLength' => 50,
    ];
}

    protected function filename(): string
{
    return 'MB_Transactions_' . date('YmdHis');
}

    protected function getTableButton(): array
{
    return [
        Button::make('export')->addClass('btn btn-primary')->text('<i class="fal fa-download mr-2"></i>Xuất'),
        Button::make('reset')->addClass('btn bg-primary')->text('<i class="fal fa-undo mr-2"></i>Thiết lập lại'),
    ];
}
}
