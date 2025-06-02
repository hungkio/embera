<?php

namespace App\DataTables;

use App\DataTables\Core\BaseDatable;
use App\DataTables\Export\CommentExportHandler;
use App\Domain\Comment\Models\Comment;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class CommentDataTable extends BaseDatable
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
            ->addColumn('action', 'admin.comments._tableAction')
            ->editColumn('created_at', fn (Comment $comment) => formatDate($comment->created_at))
            ->rawColumns(['action', 'status', 'section']);
    }

    public function query(Comment $model): Builder
    {
        return $model->newQuery();
    }

    protected function getColumns(): array
    {
        return [
            Column::checkbox(''),
            Column::make('id')->title(__('STT'))->data('DT_RowIndex')->searchable(false),
            Column::make('user_id')->title(__('ID Tài khoản')),
            Column::make('post_id')->title(__('ID bài đăng')),
            Column::make('content')->title(__('Nội Dung')),
            Column::make('created_at')->title(__('Thời gian tạo'))->searchable(false),
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
            'order' => [5, 'desc'],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Comment_'.date('YmdHis');
    }

    protected function getTableButton(): array
    {
        return [
            // Button::make('selected')->addClass('btn btn-warning')
            //     ->text('<i class="fal fa-tasks mr-2"></i>'.__('Cập nhật trạng thái'))
            //     ->action("
            //         var selectedRow = dt.rows( { selected: true } ).data();
            //         var selectedId = [];
            //         for (var i=0; i < selectedRow.length ;i++){
            //             selectedId.push(selectedRow[i].id);
            //         }

            //         var bulkUrl = window.location.href.replace(/\/+$/, \"\") + '/bulk-status';

            //         bootbox.dialog({
            //         title: 'Cập nhật trạng thái',
            //         message: '<div class=\"row\">  ' +
            //             '<div class=\"col-md-12\">' +
            //                 '<form action=\"\">' +
            //                     '<div class=\"form-group row\">' +
            //                         '<label class=\"col-md-3 col-form-label\">Trạng thái</label>' +
            //                         '<div class=\"col-md-9\">' +
            //                             '<select class=\"form-control\" id=\"change-state\">' +
			//                                 '<option value=\"pending\">Chờ phê duyệt</option>' +
			//                                 '<option value=\"active\">Hoạt động</option>' +
			//                                 '<option value=\"disabled\">Hủy</option>' +
			//                             '</select>' +
            //                         '</div>' +
            //                     '</div>' +
            //                 '</form>' +
            //             '</div>' +
            //         '</div>',
            //         buttons: {
            //             success: {
            //                 label: 'Lưu',
            //                 className: 'btn-success',
            //                 callback: function () {
            //                     var status = $('#change-state').val();
            //                     $.ajax({
            //                         type: 'POST',
            //                         data: {
            //                             id: selectedId,
            //                             status: status
            //                         },
            //                         url: bulkUrl,
            //                         success: function (res) {
            //                             dt.ajax.reload()
            //                             if(res.status == true){
            //                                 showMessage('success', res.message);
            //                             }else{
            //                                 showMessage('error', res.message);
            //                             }
            //                         },
            //                     })
            //                 }
            //             }
            //         }
            //     }
            // );"),
            // Button::make('bulkDelete')->addClass('btn btn-danger')->text('<i class="fal fa-trash-alt mr-2"></i>'.__('Xóa')),
            Button::make('export')->addClass('btn btn-primary')->text('<i class="fal fa-download mr-2"></i>'.__('Xuất')),
            Button::make('print')->addClass('btn bg-primary')->text('<i class="fal fa-print mr-2"></i>'.__('In')),
            Button::make('reset')->addClass('btn bg-primary')->text('<i class="fal fa-undo mr-2"></i>'.__('Thiết lập lại')),
        ];
    }

    // protected function buildExcelFile()
    // {
    //     $this->request()->merge(['length' => -1]);
    //     $source = app()->call([$this, 'query']);
    //     $source = $this->applyScopes($source);

    //     return new CommentExportHandler($source->get());
    // }

    // public function printPreview(): Renderable
    // {
    //     $this->request()->merge(['length' => -1]);
    //     $source = app()->call([$this, 'query']);
    //     $source = $this->applyScopes($source);
    //     $data = $source->get();
    //     return view('admin.comments.print', compact('data'));
    // }
}
