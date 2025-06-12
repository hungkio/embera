<?php

namespace App\DataTables;

use App\DataTables\Core\BaseDatable;
use App\Models\Contract;
use Carbon\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class ContractDataTable extends BaseDatable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', 'admin.contracts._tableAction')
            ->editColumn('contract_number', fn(Contract $c) => $c->contract_number)
            ->editColumn('sign_date', fn(Contract $c) => optional($c->sign_date)->format('d/m/Y'))
            ->editColumn('expired_date', fn(Contract $c) => optional($c->expired_date)->format('d/m/Y'))
            ->editColumn('status', fn(Contract $c) => ucfirst($c->status))
            ->editColumn('email', fn(Contract $c) => $c->email)
            ->editColumn('phone', fn(Contract $c) => $c->phone)
            ->editColumn('download_count', fn(Contract $c) => $c->download_count . ' lượt')
            ->editColumn('bank_info', fn(Contract $c) => $c->bank_info)
            ->editColumn('title', fn(Contract $c) => $c->title ?? '-')
            ->editColumn('ceo_sign', fn(Contract $c) => $c->ceo_sign)
            ->editColumn('location', fn(Contract $c) => $c->location)
            ->editColumn('note', fn(Contract $c) => $c->note)
            ->editColumn('expired_time', fn(Contract $c) => $c->expired_time ?? '-')
            ->editColumn('created_at', fn(Contract $c) => optional($c->created_at)->format('d/m/Y H:i'))
            ->editColumn('updated_at', fn(Contract $c) => optional($c->updated_at)->format('d/m/Y H:i'))
            ->filterColumn('contract_number', fn($query, $keyword) => $query->where('contract_number', 'like', "%$keyword%"))
            ->filterColumn('email', fn($query, $keyword) => $query->where('email', 'like', "%$keyword%"))
            ->filterColumn('status', fn($query, $keyword) => $query->where('status', 'like', "%$keyword%"))
            ->orderColumn('sign_date', 'sign_date $1')
            ->rawColumns(['action']);
    }

    public function query(Contract $model)
    {
        $query = $model->newQuery();

        // Include deleted records if requested
        if ($this->request->get('show_deleted', 'no') === 'yes') {
            $query->withDeleted();
        } else {
            $query->active(); // Only non-deleted records by default
        }

        $filters = $this->request->all();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        }

        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $query->whereBetween('sign_date', [
                Carbon::parse($filters['date_from'])->startOfDay(),
                Carbon::parse($filters['date_to'])->endOfDay(),
            ]);
        }

        return $query;
    }

    protected function getColumns(): array
    {
        return [
            Column::checkbox(''),
            Column::make('contract_number')->title('Mã hợp đồng'),
            Column::make('sign_date')->title('Ngày ký'),
            Column::make('expired_date')->title('Ngày hết hạn'),
            Column::make('status')->title('Trạng thái'),
            Column::make('email')->title('Email'),
            Column::make('phone')->title('SĐT'),
            Column::make('title')->title('Tiêu đề'),
            Column::make('download_count')->title('Lượt tải'),
            Column::make('bank_info')->title('Ngân hàng'),
            Column::make('ceo_sign')->title('Giám đốc ký'),
            Column::make('location')->title('Địa điểm'),
            Column::make('note')->title('Ghi chú'),
            Column::make('expired_time')->title('Thời hạn'),
            Column::make('created_at')->title('Tạo lúc'),
            Column::make('updated_at')->title('Cập nhật lúc'),
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
            'pageLength' => 25,
        ];
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

    protected function filename(): string
    {
        return 'Contracts_' . now()->format('YmdHis');
    }

    protected function buildExcelFile()
    {
        $this->request()->merge(['length' => -1]);
        $source = app()->call([$this, 'query']);
        $source = $this->applyScopes($source);

        return new \App\DataTables\Export\ContractExportHandler($source->get());
    }
}
