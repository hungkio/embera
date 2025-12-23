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
            ->addColumn('merchant', function (Contract $c) {
                if (!$c->merchant) {
                    return '-';
                }
                $url = route('admin.merchants.edit', ['merchant' => $c->merchant_id]);
                return '<a href="' . $url . '" target="_blank">' . e($c->merchant->username) . '</a>';
            })
            ->addColumn('shop_name', function (Contract $c) {
                if ($c->shops->isEmpty()) {
                    return '-';
                }
                return $c->shops->map(function ($shop) {
                    $url = route('admin.shops.edit', ['shop' => $shop->id]);
                    return '<a href="' . $url . '" target="_blank">' . e($shop->shop_name) . '</a>';
                })->implode('<br>');
            })
            ->editColumn('contract_number', fn(Contract $c) => e($c->full_contract_number))
            ->editColumn('sign_date', fn(Contract $c) => optional($c->sign_date)->format('d/m/Y'))
            ->editColumn('expired_date', fn(Contract $c) => optional($c->expired_date)->format('d/m/Y'))
            ->editColumn('status', fn(Contract $c) => ucfirst(Contract::STATUS[$c->status] ?? 'Chưa ký'))
            ->editColumn('download_count', fn(Contract $c) => $c->download_count . ' lượt')
            ->addColumn('city', function (Contract $c) {
                return Contract::provinces()[$c->city] ?? '-';
            })
            ->editColumn('admin_id', function (Contract $c) {
                if (!$c->admin) {
                    return '';
                }
                return trim($c->admin->first_name . ' ' . $c->admin->last_name);
            })
            ->filterColumn('admin_id', function ($query, $keyword) {
                $query->whereRaw("LOWER(CONCAT(admins.first_name, ' ', admins.last_name)) like ?", ["%" . strtolower($keyword) . "%"]);
            })
            ->editColumn('business_registration', fn(Contract $c) => e($c->business_registration ?? '-'))
            ->filterColumn('bank_account_number', fn($query, $keyword) => $query->where('bank_account_number', 'like', "%$keyword%"))
            ->filterColumn('bank_account_name', fn($query, $keyword) => $query->where('bank_account_name', 'like', "%$keyword%"))
            ->editColumn('expired_time', function (Contract $c) {
                if ($c->sign_date && $c->expired_date) {
                    return $c->sign_date->diffInMonths($c->expired_date) . ' tháng';
                }
                return '-';
            })
            ->editColumn('created_at', fn(Contract $c) => optional($c->created_at)->format('d/m/Y H:i'))
            ->filterColumn('contract_number', fn($query, $keyword) => $query->where('contract_number', 'like', "%$keyword%"))
            ->filterColumn('email', fn($query, $keyword) => $query->where('email', 'like', "%$keyword%"))
            ->filterColumn('customer_name', fn($query, $keyword) => $query->where('customer_name', 'like', "%$keyword%"))
            ->filterColumn('status', function ($query, $keyword) {
                $statusMap = [
                    'Đã ký' => 2,
                    'Chưa ký' => 1,
                    'Chỉ có BBNT' => 0,
                ];
                $keyword = strtolower(trim($keyword));
                if (isset($statusMap[$keyword])) {
                    $query->where('status', $statusMap[$keyword]);
                } else {
                    $query->where('status', 'like', "%$keyword%");
                }
            })
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
            ->filterColumn('expired_time', fn($query, $keyword) => $query->where('expired_time', 'like', "%$keyword%"))
            ->rawColumns(['merchant', 'shop_name', 'action']);
    }

    public function query(Contract $model)
    {
        return $model->newQuery()
            ->with(['merchant', 'shops' => function ($q) {
                $q->where('shops.is_deleted', false);
            }, 'admin'])
            ->leftJoin('admins', 'contracts.admin_id', '=', 'admins.id')
            ->select('contracts.*')
            ->where('contracts.is_deleted', 0)
            ->when($this->request->filled('date_from') && $this->request->filled('date_to'), function ($q) {
                $q->whereBetween('sign_date', [
                    Carbon::parse($this->request->date_from)->format('Y-m-d'),
                    Carbon::parse($this->request->date_to)->format('Y-m-d'),
                ]);
            });
    }

    protected function getColumns(): array
    {
        return [
            Column::checkbox(''),
            Column::make('merchant')->title('Merchant')->addClass('text-center'),
            Column::make('shop_name')->title('Cửa hàng')->data('shop_name'),
            Column::make('customer_name')->title('Tên khách hàng'),
            Column::make('contract_number')->title('Mã hợp đồng'),
            Column::make('business_registration')->title('GĐKKD'),
            Column::make('sign_date')->title('Ngày ký'),
            Column::make('expired_date')->title('Ngày hết hạn'),
            Column::make('expired_time')->title('Thời hạn'),
            Column::make('status')->title('Trạng thái'),
            Column::make('city')->title('Tỉnh/TP'),
            Column::make('download_count')->title('Lượt tải'),
            Column::make('admin_id')->title('BD')->data('admin_id'),
            Column::make('created_at')->title('Tạo lúc'),
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
            Button::make('reset')->addClass('btn bg-blue')->text('<i class="fal fa-undo mr-2"></i>' . __('Thiết lập lại')),
            Button::make('selected')->addClass('btn bg-teal-400 import')->text('<i class="icon-compose mr-2"></i>' . __('Import')),
        ];
    }

    protected function filename(): string
    {
        return 'Contracts_' . now()->format('YmdHis');
    }
}
