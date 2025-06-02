<?php

namespace App\DataTables\Core;

use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Services\DataTable;

class BaseDatable extends DataTable
{
    protected array $excludeFromExport = [];

    protected array $excludeFromPrint = [];

    protected string $exportClass = TableExportHandler::class;

    public function htmlBuilder(): BaseBuilder
    {
        return app(BaseBuilder::class);
    }

    public function html(): BaseBuilder
    {
        return $this->htmlBuilder()
            ->setTableId($this->getTableId())
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters(array_merge(
                [
                    'columnDefs' => [
                        'orderable' => false,
                        'className' => 'select-checkbox',
                        'targets' => 0
                    ],
                    'drawCallback' => 'function(setting) {
                        $("th.select-checkbox").removeClass("selected");
                        var api = this.api();
                        $(\'.dataTables_filter\').find(\'input[type=search]\').attr(\'type\', \'text\');
                         $(\'.dataTables_length select\').select2({
                            minimumResultsForSearch: Infinity,
                            dropdownAutoWidth: true,
                            width: \'auto\'
                        });

                        $("#"+setting.sTableId).unbind(\'click\');

                        $("#"+setting.sTableId).on("click", "th.select-checkbox", function() {
                            if ($("th.select-checkbox").hasClass("selected")) {
                                api.rows().deselect();
                                $("th.select-checkbox").removeClass("selected");
                            } else {
                                api.rows().select();
                                $("th.select-checkbox").addClass("selected");
                            }
                        })
                         $("#"+setting.sTableId).on("select deselect", function() {
                            if (api.rows({
                                    selected: true
                                }).count() !== api.rows().count()) {
                                $("th.select-checkbox").removeClass("selected");
                            } else {
                                $("th.select-checkbox").addClass("selected");
                            }
                        })

                    }',
                    'autoWidth' => false,
                    'order' => [[1, 'desc']],
                    'dom' => '<"dt-buttons-full"B><"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
                    'language' => [
                        'searchPlaceholder' => __('searchPlaceholder'),
                        'sLengthMenu' => __('sLengthMenu'),
                        'sZeroRecords' => __('sZeroRecords'),
                        'sInfo' => __('sInfo'),
                        'sInfoEmpty' => __('sInfoEmpty'),
                        'sInfoFiltered' => __('sInfoFiltered'),
                        'sInfoPostFix' => '',
                        'sSearch' => __('sSearch'),
                        'sUrl' => '',
                        'oPaginate' => [
                            'sFirst' => __('sFirst'),
                            'sPrevious' => __('sPrevious'),
                            'sNext' => __('sNext'),
                            'sLast' => __('sLast')
                        ],
                        'select' => [
                            'rows'=> __('rowSelected')
                        ]
                    ],
                ],
                $this->getBuilderParameters()
            ))
            ->selectStyleMulti()
            ->selectSelector('td:not(:last-child)')
            ->buttons(
                empty($this->getTableButton()) ? null : $this->getTableButton()
            );
    }

    protected function getTableId(): string
    {
        return class_basename($this);
    }

    protected function getBuilderParameters(): array
    {
        return [];
    }

    protected function getColumns(): array
    {
        return [];
    }
}
