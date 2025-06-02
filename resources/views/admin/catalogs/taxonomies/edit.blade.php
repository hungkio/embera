@extends('admin.layouts.master')

@section('title', __('Chỉnh sửa :model', ['model' => $taxonomy->name]))

@section('page-header')
    <x-page-header>
        {{ Breadcrumbs::render() }}
    </x-page-header>
@stop
@push('css')
    <link rel="stylesheet" href="{{ asset('backend/global_assets/js/plugins/jstree/themes/default/style.min.css') }}">
    <style>
        .icon-md {
            font-size: 1.5rem;
        }
    </style>
@endpush

@push('js')
    <script>
        var adminTaxonomyPath = "{{ route('admin.taxonomies.tree', $taxonomy) }}";
    </script>
    <script src="{{ asset('backend/global_assets/js/plugins/jstree/jstree.min.js') }}"></script>
    <script !src="">
        $.jstree.defaults.core.themes.variant = "large";
        $('#taxonomy-tree').jstree({
            'core': {
                "check_callback": true,
                'data': {
                    "url": function (node) {
                        return node.id === '#'
                            ? adminTaxonomyPath : adminTaxonomyPath.replace('jstree', 'taxon') + '/' + node.id + '/jstree'
                    },
                },

            },
            "plugins": ['contextmenu', 'dnd', 'types'],
            "types": {
                "default": {
                    "icon": "fas fa-folders text-primary"
                }
            },
            "contextmenu": {
                items: function ($node) {
                    var tree = $('#taxonomy-tree').jstree(true);
                    let taxon_level = '{{ setting('store_taxon_level', 3) }}'
                    let actions = {
                        @can('taxonomies.update')
                        create: {
                            label: '<i class="fal fa-plus mr-2"></i> ' + Lang.create,
                            action: function (node) {
                                $node = tree.create_node($node, {text: '{{ __('Danh mục') }}', type: 'default'});
                                tree.edit($node);
                            },
                            separator_after: false,
                            separator_before: false
                        },
                        rename: {
                            label: '<i class="fal fa-money-check-edit mr-2"></i> ' + Lang.rename,
                            action: function (obj) {
                                tree.edit($node);
                            },
                            separator_after: false,
                            separator_before: false
                        },
                        remove: {
                            label: '<i class="fal fa-trash-alt mr-2"></i> ' + Lang.remove,
                            action: function (obj) {
                                confirmAction(Lang.confirm_delete, function (result) {
                                    if (result) {
                                        return tree.delete_node($node)
                                    }
                                })
                            },
                            separator_after: false,
                            separator_before: false
                        },
                        edit: {
                            label: '<i class="fal fa-edit mr-2"></i> ' + Lang.edit,
                            action: function (obj) {
                                window.location = Admin.adminUrl('taxons/' + $node.id + '/edit');
                                return window.location
                            },
                            separator_after: false,
                            separator_before: false
                        }
                        @endcan
                    };

                    if($node.parents.length < 2) {
                        delete actions['remove']
                    }
                    if(taxon_level && $node.parents.length >= taxon_level) {
                        delete actions['create']
                    }
                    return actions;
                }
            },
        })
            .on('loaded.jstree', function (e, data) {
                $("#taxonomy-tree").jstree("select_node", "ul > li:first");
                let Selectednode = $("#taxonomy-tree").jstree("get_selected");
                $("#taxonomy-tree").jstree("open_node", Selectednode, false, true);
            })
            .on('delete_node.jstree', function (e, data) {
                $.ajax({
                    type: 'DELETE',
                    dataType: 'json',
                    url: Admin.adminUrl('taxons/' + data.node.id),
                    success: function (data) {
                        showMessage('success', '{{ __(' Bạn đã xóa danh mục thành công !') }}')
                    }
                })
                    .fail(function () {
                        data.instance.refresh();
                    });
            })
            .on('create_node.jstree', function (e, data) {
                let name = data.node.text;
                let position = data.position;
                let parent_id = data.node.parent;
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: Admin.adminUrl('taxons'),
                    data: {
                        name: name,
                        position: position,
                        parent_id: parent_id,
                    },
                    success: function (data) {
                        showMessage('success', '{{ __('Bạn đã tạo danh mục thành công !') }}')
                    }
                }).done(function (d) {
                    data.instance.set_id(data.node, d.id);
                }).fail(function () {
                    data.instance.refresh();
                });
            })
            .on('rename_node.jstree', function (e, data) {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: Admin.adminUrl('taxons/' + data.node.id + '/rename'),
                    data: {
                        name: data.text,
                    },
                    success: function (data) {
                        showMessage('success', '{{ __('Bạn đã đổi tên danh mục thành công !') }}');
                        if (data) {
                            if (data.taxon.parent_id == null) {
                                $('#name').val(data.taxon.name);
                            }
                        }
                    }
                }).fail(function (err) {
                    showMessage('error', err.responseJSON.errors.name[0] ?? 'Lỗi');
                    setTimeout(function () {
                        data.instance.refresh();
                    }, 3000)
                });
            })
            .on('move_node.jstree', function (e, data) {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: Admin.adminUrl('taxons/' + data.node.id + '/sort'),
                    data: {
                        parent_id: data.node.parent,
                        position: data.position
                    },
                    success: function (data) {
                        showMessage('success', '{{ __('Bạn đã chuyển vị trí danh mục thành công !') }}');
                    }
                }).fail(function () {
                    data.instance.refresh();
                });
            });

    </script>
@endpush

@section('page-content')
    <!-- Inner container -->
    <form action="{{ route('admin.taxonomies.update', $taxonomy) }}" method="POST" data-block>
        @csrf
        @method('PUT')
        <div class="d-flex align-items-start flex-column flex-md-row">

            <!-- Left content -->
            <div class="w-100 order-2 order-md-1 left-content">

                <div class="row">
                    <div class="col-md-12">
                        <x-card>
                            <fieldset>
                                <legend class="font-weight-semibold text-uppercase font-size-sm">
                                    {{ __('Chung') }}
                                </legend>

                                <div class="collapse show" id="general">
                                    <x-text-field
                                        name="name"
                                        :label="__('Tên')"
                                        :value="$taxonomy->name"
                                        required
                                    >
                                    </x-text-field>
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend class="font-weight-semibold text-uppercase font-size-sm">
                                    {{ __('Cây thư mục') }}
                                </legend>

                                <div class="collapse show" id="tree">
                                    <div class="form-group">
                                        <div id="taxonomy-tree"></div>
                                    </div>
                                </div>
                                <div class="text-primary">
                                    <i class="fas fa-exclamation-triangle"></i> {{ __('Hướng dẫn: Nhấp chuột phải vào một danh mục trong cây truy cập để thêm, sửa, xóa, kéo thả để sắp xếp một danh mục !') }}
                                </div>
                            </fieldset>

                        </x-card>
                        <div class="d-flex justify-content-center align-items-center action" id="action-form">
                            <a href="{{ route('admin.taxonomies.index') }}" class="btn btn-light"><i
                                    class="fal fa-arrow-left mr-2"></i>{{ __('Trở lại') }}</a>
                            <div class="btn-group ml-3">
                                <button class="btn btn-primary btn-block" data-loading><i
                                        class="fal fa-check mr-2"></i>{{ __('Lưu') }}</button>
                                <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"></button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="javascript:void(0)" class="dropdown-item submit-type"
                                       data-redirect="{{ route('admin.taxonomies.index') }}">{{ __('Lưu và thoát') }}</a>
                                    <a href="javascript:void(0)" class="dropdown-item submit-type"
                                       data-redirect="{{ route('admin.taxonomies.create') }}">{{ __('Lưu và tạo mới') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- /left content -->
        </div>
        <!-- /inner container -->
    </form>

@stop
