<?php

namespace App\DataTables;

use App\DataTables\Core\BaseDatable;
use App\DataTables\Export\MenuExportHandler;
use Illuminate\Contracts\Support\Renderable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use App\Domain\Menu\Models\Menu;

class MenuDataTable extends BaseDatable
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
            ->editColumn('status','admin.menus._tableStatus')
            ->editColumn('created_at', fn (Menu $menu) => formatDate($menu->created_at))
            ->editColumn('position', fn (Menu $menu) => Menu::POSITION[$menu->position])
            ->editColumn('lang', fn (Menu $menu) => strtoupper($menu->lang))
            ->addColumn('action', 'admin.menus._tableAction')
            ->rawColumns(['action', 'name', 'status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Menu $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Menu $model)
    {
        return $model->newQuery();
    }

    protected function getColumns(): array
    {
        return [
            Column::checkbox(''),
            Column::make('id')->title(__('STT'))->data('DT_RowIndex')->searchable(false),
            Column::make('name')->title(__('Tên'))->width('20%'),
            Column::make('position')->title(__('Vị trí'))->width('20%'),
            Column::make('lang')->title(__('Ngôn ngữ'))->width('20%'),
            Column::make('status')->title(__('Trạng thái'))->width('10%'),
            Column::make('created_at')->title(__('Thời gian tạo'))->searchable(false),
                Column::computed('action')
                ->title(__('Tác vụ'))
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    protected function getTableButton(): array
    {
        return [
            Button::make('create')->addClass('btn btn-success')->text('<i class="fal fa-plus-circle mr-2"></i>'.__('Tạo mới'))
            ->action("$('#createMenu').modal('show')"),
            Button::make('selected')->addClass('btn btn-warning')
                ->text('<i class="fal fa-tasks mr-2"></i>'.__('Cập nhật trạng thái'))
                ->action("
                    var selectedRow = dt.rows( { selected: true } ).data();
                    var selectedId = [];
                    for (var i=0; i < selectedRow.length ;i++){
                        selectedId.push(selectedRow[i].id);
                    }

                    var bulkUrl = window.location.href.replace(/\/+$/, \"\") + '/bulk-status';

                    bootbox.dialog({
                    title: 'Cập nhật trạng thái',
                    message: '<div class=\"row\">  ' +
                        '<div class=\"col-md-12\">' +
                            '<form action=\"\">' +
                                '<div class=\"form-group row\">' +
                                    '<label class=\"col-md-3 col-form-label\">Trạng thái</label>' +
                                    '<div class=\"col-md-9\">' +
                                        '<select class=\"form-control\" id=\"change-state\">' +
			                                '<option value=\"0\">Ẩn</option>' +
			                                '<option value=\"1\">Hiển thị</option>' +
			                            '</select>' +
                                    '</div>' +
                                '</div>' +
                            '</form>' +
                        '</div>' +
                    '</div>',
                    buttons: {
                        success: {
                            label: 'Lưu',
                            className: 'btn-success',
                            callback: function () {
                                var status = $('#change-state').val();
                                $.ajax({
                                    type: 'POST',
                                    data: {
                                        id: selectedId,
                                        status: status
                                    },
                                    url: bulkUrl,
                                    success: function (res) {
                                        dt.ajax.reload()
                                        if(res.status == true){
                                            showMessage('success', res.message);
                                        }else{
                                            showMessage('error', res.message);
                                        }
                                    },
                                })
                            }
                        }
                    }
                }
            );"),
            Button::make('bulkDelete')->addClass('btn bg-danger d-none')->text('<i class="fal fa-trash-alt mr-2"></i>'.__('Xóa')),
            #Button::make('export')->addClass('btn btn-primary')->text('<i class="fal fa-download mr-2"></i>'.__('Xuất')),
            #Button::make('print')->addClass('btn bg-primary')->text('<i class="fal fa-print mr-2"></i>'.__('In')),
            Button::make('reset')->addClass('btn bg-primary')->text('<i class="fal fa-undo mr-2"></i>'.__('Thiết lập lại')),
        ];
    }

    protected function getBuilderParameters(): array
    {
        return [
            'order' => [4, 'desc'],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Menu_'.date('YmdHis');
    }

    protected function buildExcelFile()
    {
        $this->request()->merge(['length' => -1]);
        $source = app()->call([$this, 'query']);
        $source = $this->applyScopes($source);

        return new MenuExportHandler($source->get());
    }

    public function printPreview(): Renderable
    {
        $this->request()->merge(['length' => -1]);
        $source = app()->call([$this, 'query']);
        $source = $this->applyScopes($source);
        $data = $source->get();
        return view('admin.menus.print', compact('data'));
    }
}
