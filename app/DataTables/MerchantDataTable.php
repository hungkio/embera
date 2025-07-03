<?php

namespace App\DataTables;

use App\DataTables\Core\BaseDatable;
use App\Models\Merchant;
use Carbon\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class MerchantDataTable extends BaseDatable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->setRowId(function (Merchant $m) {
                return 'merchant_' . $m->id; // để bạn tách được ID dễ dàng
            })
            ->addColumn('action', 'admin.merchants._tableAction')
            ->editColumn('admin_id', fn(Merchant $m) => $m->admin->full_name ?? '')
            ->editColumn('status', function (Merchant $m) {
                return match ($m->status ?? 'active') {
                    'active' => 'Hoạt động',
                    'inactive' => 'Không hoạt động',
                    default => ucfirst($m->status ?? 'Hoạt động'),
                };
            })
            ->editColumn('created_at', fn(Merchant $m) => optional($m->created_at)->format('d/m/Y H:i'))
            ->editColumn('updated_at', fn(Merchant $m) => optional($m->updated_at)->format('d/m/Y H:i'))
            ->filterColumn('username', fn($query, $keyword) => $query->where('username', 'like', "%$keyword%"))
            ->filterColumn('email', fn($query, $keyword) => $query->where('email', 'like', "%$keyword%"))
            ->orderColumn('created_at', 'created_at $1')
            ->rawColumns(['action']);
    }


    public function query(Merchant $model)
    {
        $query = $model->newQuery()->with('admin');

        if ($this->request->get('show_deleted', 'no') === 'yes') {
            $query->withDeleted();
        } else {
            $query->active();
        }

        return $query;
    }

    protected function getColumns(): array
    {
        return [
            Column::checkbox(''),
            Column::make('username')->title('Tên đăng nhập'),
            Column::make('email')->title('Email'),
            Column::make('phone')->title('Số điện thoại'),
            Column::make('admin_id')->title('BD'),
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
            Button::make('create')->addClass('btn btn-success')->text('<i class="fal fa-plus-circle mr-2"></i>'.__('Tạo mới')),
            Button::make('bulkDelete')->addClass('btn bg-danger')->text('<i class="fal fa-trash-alt mr-2"></i>'.__('Xóa')),
            Button::make('export')->addClass('btn bg-blue')->text('<i class="fal fa-download mr-2"></i>'.__('Xuất')),
            Button::make('print')->addClass('btn bg-blue')->text('<i class="fal fa-print mr-2"></i>'.__('In')),
            Button::make('reset')->addClass('btn bg-blue')->text('<i class="fal fa-undo mr-2"></i>'.__('Thiết lập lại')),
            Button::make('selected')
                ->addClass('btn bg-orange btn-send-email sendmail') // thêm 'sendmail'
                ->text('<i class="fal fa-envelope mr-2"></i> Gửi Mail')


        ];
    }

    protected function filename(): string
    {
        return 'Merchants_' . now()->format('YmdHis');
    }

}
