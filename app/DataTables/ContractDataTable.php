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
            ->addColumn('shop_name', function (Contract $c) {
                return $c->shops->pluck('shop_name')->join(', ') ?: '-';
            })
            ->editColumn('contract_number', fn(Contract $c) => $c->contract_number)
            ->editColumn('sign_date', fn(Contract $c) => optional($c->sign_date)->format('d/m/Y'))
            ->editColumn('expired_date', fn(Contract $c) => optional($c->expired_date)->format('d/m/Y'))
            ->editColumn('status', function (Contract $c) {
                return match ($c->status) {
                    'đã_ký' => 'Đã ký',
                    'chưa_ký' => 'Chưa ký',
                    'chỉ_có_BBNT' => 'Chỉ có BBNT',
                    default => ucfirst($c->status),
                };
            })
            ->editColumn('email', fn(Contract $c) => $c->email)
            ->editColumn('phone', fn(Contract $c) => $c->phone)
            ->editColumn('download_count', fn(Contract $c) => $c->download_count . ' lượt')
            ->editColumn('bank_info', fn(Contract $c) => $c->bank_info)
            ->editColumn('bank_account_number', fn(Contract $c) => $c->bank_account_number ?? '-')
            ->editColumn('bank_account_name', fn(Contract $c) => $c->bank_account_name ?? '-')
            ->filterColumn('bank_account_number', fn($query, $keyword) => $query->where('bank_account_number', 'like', "%$keyword%"))
            ->filterColumn('bank_account_name', fn($query, $keyword) => $query->where('bank_account_name', 'like', "%$keyword%"))
            ->editColumn('title', fn(Contract $c) => $c->title ?? '-')
            ->editColumn('ceo_sign', fn(Contract $c) => $c->ceo_sign)
            ->editColumn('location', fn(Contract $c) => $c->location)
            ->editColumn('note', fn(Contract $c) => $c->note)
            ->editColumn('expired_time', function (Contract $c) {
                if ($c->sign_date && $c->expired_date) {
                    return $c->sign_date->diffInMonths($c->expired_date) . ' tháng';
                }
                return '-';
            })
            ->editColumn('created_at', fn(Contract $c) => optional($c->created_at)->format('d/m/Y H:i'))
            ->editColumn('updated_at', fn(Contract $c) => optional($c->updated_at)->format('d/m/Y H:i'))
            ->filterColumn('contract_number', fn($query, $keyword) => $query->where('contract_number', 'like', "%$keyword%"))
            ->filterColumn('email', fn($query, $keyword) => $query->where('email', 'like', "%$keyword%"))
            ->filterColumn('customer_name', fn($query, $keyword) => $query->where('customer_name', 'like', "%$keyword%"))
            ->filterColumn('status', fn($query, $keyword) => $query->where('status', 'like', "%$keyword%"))
            ->orderColumn('sign_date', 'sign_date $1')
            ->filterColumn('shop_name', function ($query, $keyword) {
                $query->whereHas('shops', function ($q) use ($keyword) {
                    $q->where('shop_name', 'like', "%$keyword%");
                });
            })
            ->orderColumn('shop_name', function ($query, $direction) {
                $query->leftJoin('shops', 'shops.contract_id', '=', 'contracts.id')
                    ->select('contracts.*')
                    ->orderBy('shops.shop_name', $direction);
            })
            ->filterColumn('expired_time', fn($query, $keyword) => $query->where('expired_time', 'like', "%$keyword%")) // Text search
            ->rawColumns(['action']);
    }

    public function query(Contract $model)
    {
        $query = $model->newQuery()
            ->with(['shops.merchant']);

        if ($this->request->get('show_deleted', 'no') === 'yes') {
            $query->where('contracts.is_deleted', 1);
        } else {
            $query->where('contracts.is_deleted', 0);
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
            Column::make('expired_time')->title('Thời hạn'),
            Column::make('status')->title('Trạng thái'),
            Column::make('email')->title('Email'),
            Column::make('customer_name')->title('Tên khách hàng'),
            Column::make('phone')->title('SĐT'),
            Column::make('title')->title('Tiêu đề'),
            Column::make('download_count')->title('Lượt tải'),
            Column::make('bank_info')->title('Ngân hàng'),
            Column::make('bank_account_number')->title('STK'),
            Column::make('bank_account_name')->title('Chủ tài khoản'),
            Column::make('ceo_sign')->title('Giám đốc ký'),
            Column::make('location')->title('Địa điểm'),
            Column::make('note')->title('Ghi chú'),
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
            Button::make('create')->addClass('btn btn-success')->text('<i class="fal fa-plus-circle mr-2"></i>' . __('Tạo mới')),
            Button::make('bulkDelete')->addClass('btn bg-danger')->text('<i class="fal fa-trash-alt mr-2"></i>' . __('Xóa')),
            Button::make('export')->addClass('btn bg-blue')->text('<i class="fal fa-download mr-2"></i>' . __('Xuất')),
            Button::make('print')->addClass('btn bg-blue')->text('<i class="fal fa-print mr-2"></i>' . __('In'))->action('function() { printContracts(); }'),
            Button::make('reset')->addClass('btn bg-blue')->text('<i class="fal fa-undo mr-2"></i>' . __('Thiết lập lại')),
            Button::make('selected')->addClass('btn bg-teal-400 import')
                ->text('<i class="icon-compose mr-2"></i>' . __('Import')
                ),
        ];
    }

    protected function filename(): string
    {
        return 'Contracts_' . now()->format('YmdHis');
    }

}
