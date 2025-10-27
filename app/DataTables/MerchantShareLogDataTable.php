<?php

namespace App\DataTables;

use App\DataTables\Core\BaseDatable;
use App\Models\MerchantShareLog;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class MerchantShareLogDataTable extends BaseDatable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query->with(['merchant']))
            ->addIndexColumn()

            // Merchant username
            ->addColumn('merchant', fn($log) => $log->merchant->username ?? '-')

            // Mã hợp đồng
            ->editColumn('contract_no', fn($log) => $log->contract_no ?? '-')

            // Khách hàng
            ->editColumn('customer_name', fn($log) => $log->customer_name ?? '-')

            // Kỳ (MM/YYYY)
            ->addColumn('period', fn($log) => str_pad($log->month, 2, '0', STR_PAD_LEFT) . '/' . $log->year)

            // Tổng thu nhập (ẩn nếu loại là fixed)
            ->editColumn('total', fn($log) => $log->share_type === 'fixed'
                ? ''
                : number_format($log->total, 0, ',', '.') . ' VNĐ'
            )

            // Số đơn (từ number_of_order)
            ->editColumn('number_of_order', fn($log) => $log->number_of_order ?? 0)

            // Share (hiển thị VNĐ nếu fixed, % nếu percentage)
            ->addColumn('share', function ($log) {
                if ($log->share_type === 'fixed') {
                    return number_format($log->share_percent, 0, ',', '.') . ' VNĐ';
                }

                // Nếu <= 1 thì nhân 100
                $pct = $log->share_percent > 1 ? $log->share_percent : $log->share_percent * 100;
                return number_format($pct, 0) . '%';
            })

            // Số tiền chia sẻ
            ->editColumn('share_money', fn($log) => number_format($log->share_money, 0, ',', '.') . ' VNĐ')

            // Loại chia sẻ
            ->addColumn('share_type', fn($log) => $log->share_type === 'fixed' ? 'Số đơn' : 'Phần trăm')

            // Nguồn ghi (type)
            ->addColumn('type', fn($log) => $log->type === 'email' ? 'Email' : 'Zalo')

            // Ngày ghi log
            ->editColumn('date', fn($log) => optional($log->created_at)->format('d/m/Y H:i:s'))

            // Trạng thái
            ->editColumn('status', fn($log) => ucfirst($log->status ?? '-'))

            // Cột tác vụ (Xem chi tiết)
            ->addColumn('action', function ($log) {
                $viewUrl = route('admin.share-logs.detail', $log->id);
                return '<a href="' . $viewUrl . '" class="btn btn-sm btn-info" title="Xem chi tiết">
                            <i class="fal fa-eye"></i>
                        </a>';
            })

            ->rawColumns(['action']);
    }

    public function query(MerchantShareLog $model)
    {
        return $model->newQuery()->with('merchant')->orderByDesc('created_at');
    }

    protected function getColumns(): array
    {
        return [
            Column::checkbox(''),
            Column::make('id')->title('#')->data('DT_RowIndex')->searchable(false),

            Column::make('merchant')->title('Merchant'),
            Column::make('contract_no')->title('Mã HĐ'),
            Column::make('customer_name')->title('Khách hàng'),
            Column::computed('period')->title('Kỳ'),

            Column::make('total')->title('Tổng thu nhập'),
            Column::make('number_of_order')->title('Số đơn'),
            Column::computed('share')->title('Share'),
            Column::make('share_money')->title('Số tiền share'),

            Column::computed('share_type')->title('Loại chia sẻ'),
            Column::computed('type')->title('Nguồn ghi'),

            Column::computed('date')->title('Ngày ghi log'),
            Column::computed('status')->title('Trạng thái'),

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
            'order' => [1, 'desc'],
            'pageLength' => 25,
        ];
    }

    protected function filename(): string
    {
        return 'Merchant_Share_Logs_' . date('YmdHis');
    }

    protected function getTableButton(): array
    {
        return [
            Button::make('export')
                ->addClass('btn btn-primary')
                ->text('<i class="fal fa-download mr-2"></i> Xuất Excel'),

            Button::make('reset')
                ->addClass('btn bg-blue')
                ->text('<i class="fal fa-undo mr-2"></i> Thiết lập lại'),
        ];
    }

    protected function buildExcelFile()
    {
        $this->request()->merge(['length' => -1]);
        $source = app()->call([$this, 'query']);
        $source = $this->applyScopes($source);

        return new \App\DataTables\Export\MerchantShareLogExportHandler($source->get());
    }
}
