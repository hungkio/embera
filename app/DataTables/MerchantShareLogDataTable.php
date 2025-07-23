<?php

namespace App\DataTables;

use App\DataTables\Core\BaseDatable;
use App\DataTables\Core\BaseBuilder;
use App\Models\MerchantShareLog;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Carbon\Carbon;

class MerchantShareLogDataTable extends BaseDatable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent(
                $query->with(['merchant'])
            )
            ->addIndexColumn()

            // Merchant username
            ->addColumn('merchant', fn($log) => $log->merchant->username ?? '')

            // Contract and customer
            ->editColumn('contract_no', fn($log) => $log->contract_no)
            ->editColumn('customer_name', fn($log) => $log->customer_name)

            // Period MM/YYYY
            ->addColumn('period', fn($log) => str_pad($log->month, 2, '0', STR_PAD_LEFT) . '/' . $log->year
            )

            // Total revenue (hide for fixed share_type)
            ->editColumn('total', fn($log) => $log->share_type === 'fixed'
                ? ''
                : number_format($log->total, 0, ',', '.') . ' VNĐ'
            )

            // Share type displayed as 'Số đơn' for fixed, 'Phần trăm' for percentage
            ->addColumn('share_type', fn($log) => $log->share_type === 'fixed'
                ? 'Số đơn'
                : 'Phần trăm'
            )

            /// Share value: show per-shop fixed rate for fixed, % for percentage
            ->addColumn('share', function ($log) {
                if ($log->share_type === 'fixed') {
                    return number_format($log->share_percent, 0, ',', '.') . ' VNĐ';
                }

                // Nếu >1 thì coi là percent đã lưu, còn <=1 thì phải *100
                $pct = $log->share_percent > 1
                    ? $log->share_percent
                    : $log->share_percent * 100;

                return number_format($pct, 0) . '%';
            })


            // Share money formatted for clarity
            ->editColumn('share_money', fn($log) => number_format($log->share_money, 0, ',', '.') . ' VNĐ'
            )

            // Log date (parse string to Carbon)
            ->editColumn('date', fn($log) => Carbon::parse($log->date)->format('d/m/Y')
            )

            // Number of orders
            ->editColumn('number_of_order', fn($log) => $log->number_of_order
            )

            // Action button
            ->addColumn('action', fn($log) => '<a href="' . route('admin.merchants-history.detail', $log->id) . '" class="btn btn-sm btn-info"><i class="fal fa-eye"></i></a>'
            )
            ->rawColumns(['action']);
    }

    public function html(): BaseBuilder
    {
        return $this->builder()
            ->setTableId('merchant-share-logs-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->buttons($this->getTableButton());
    }

    protected function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')
                ->title('#')
                ->orderable(false)
                ->searchable(false),

            Column::make('merchant')->title('Merchant'),
            Column::make('contract_no')->title('Mã HĐ')->addClass('text-center'),
            Column::make('customer_name')->title('Khách hàng'),

            Column::computed('period')
                ->title('Kỳ')
                ->addClass('text-center'),

            Column::make('total')->title('Tổng thu nhập'),
            Column::make('share_type')->title('Loại chia sẻ')->addClass('text-center'),
            Column::make('share')->title('Share')->addClass('text-center'),
            Column::make('share_money')->title('Số tiền share'),
            Column::make('date')->title('Ngày ghi log'),
            Column::make('number_of_order')->title('Số đơn')->addClass('text-center'),

            Column::computed('action')
                ->title('Tác vụ')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    protected function getBuilderParameters(): array
    {
        return [
            'order' => [0, 'desc'],
            'pageLength' => 25,
        ];
    }

    protected function getTableButton(): array
    {
        return [
            Button::make('export')->addClass('btn bg-blue')
                ->text('<i class="fal fa-download mr-2"></i>Xuất'),
            Button::make('print')->addClass('btn bg-blue')
                ->text('<i class="fal fa-print mr-2"></i>In'),
            Button::make('reset')->addClass('btn bg-blue')
                ->text('<i class="fal fa-undo mr-2"></i>Thiết lập lại'),
        ];
    }

    protected function filename(): string
    {
        return 'Merchant_Share_Logs_' . now()->format('YmdHis');
    }

    public function query(MerchantShareLog $model)
    {
        return $model->newQuery()
            ->withoutGlobalScopes()
            ->orderBy('created_at', 'desc')
            ->select([
                'id',
                'merchant_id',
                'year',
                'month',
                'contract_no',
                'customer_name',
                'total',
                'share_percent',
                'share_money',
                'share_type',
                'date',
                'number_of_order',
            ]);
    }
}
