@extends('admin.layouts.master')

@section('title', __('Chỉnh sửa :model', ['model' => $menu->name]))

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

        select.form-control {
            -moz-appearance: none;
            -webkit-appearance: none;
            appearance: none;
        }

        label.error {
            color: red;
        }
    </style>
@endpush

@push('js')
    <script>
        var adminTaxonomyPath = "{{ route('admin.menus.tree', $menu) }}";
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
                            ? adminTaxonomyPath : adminTaxonomyPath.replace('jstree', 'menu-item') + '/' + node.id + '/jstree'
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
                    let menu_level = '{{ setting('store_menu_level', 3) }}'
                    let actions = {
                        create: {
                            label: '<i class="fal fa-plus mr-2"></i> ' + Lang.create,
                            action: function (node) {
                                $node = tree.create_node($node, {text: 'Menu', type: 'default'});
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
                                $('input[name=menu_item_id]').val($node.id)
                                if ($node.id) {
                                    $.ajax({
                                        method: 'post',
                                        url: '{{ route('admin.menus.getDataUpdate') }}',
                                        data: {
                                            '_token': '{{ csrf_token() }}',
                                            'menu_item_id': $node.id
                                        },
                                        success: function (res) {
                                            let data = res.data
                                            let menu = res.menuItem
                                            if (data && menu) {
                                                $('#updateMenu').find('#name_menu').val(menu.name)
                                                let list_type_option = $('#updateMenu').find('select.list_category option')
                                                if (list_type_option.length != 0) {
                                                    $.each(list_type_option, function (key, val) {
                                                        if ($(val).val() == menu.type) {
                                                            $(val).prop("selected", true)
                                                        }
                                                    })
                                                    $(".select-update").select2({
                                                        dropdownParent: $("#updateMenu"),
                                                    });
                                                }

                                                if (menu.type != '{{ \App\Domain\Menu\Models\MenuItem::TYPE_LINK }}') {
                                                    let html = '<select class="custom-select form-control select-update-content" name="item_id">\n'
                                                    $.each(data, function (key, val) {
                                                        html += '<option value="' + val.id + '"'
                                                        if (menu.item_id == val.id) {
                                                            html += 'selected'
                                                        }
                                                        html += '>' + (val.title ?? val.name) + '</option>'
                                                    })
                                                    html += '</select>'
                                                    if (html != '') {
                                                        $('#updateMenu').find('div.list_item').html(html)
                                                        setPageSelect($(".select-update-content"), $("#updateMenu"))
                                                    }
                                                } else {
                                                    $('#updateMenu').find('div.list_item').html(data)
                                                }
                                            }
                                        }
                                    })
                                    $('#updateMenu').modal('show')
                                }
                                return window.location
                            },
                            separator_after: false,
                            separator_before: false
                        },
                    };

                    if($node.parents.length < 2) {
                        delete actions['remove']
                        delete actions['edit']
                    }
                    if(menu_level && $node.parents.length >= menu_level) {
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
                    url: Admin.adminUrl('menu-item/' + data.node.id),
                    success: function (res) {
                        showMessage('success', res.message);
                    }
                })
                    .fail(function () {
                        data.instance.refresh();
                    });
            })
            .on('create_node.jstree', function (e, data) {
                $('#taxonomy-tree').jstree(true).delete_node("#" + data.node.id);
                $('<input>').attr({
                    type: 'hidden',
                    id: 'foo',
                    name: 'parent_id',
                    value: data.node.parent,
                }).appendTo('#create-menu');
                $('#createMenu').modal('show')
            })
            .on('move_node.jstree', function (e, data) {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: Admin.adminUrl('menus/' + data.node.id + '/sort'),
                    data: {
                        parent_id: data.node.parent,
                        position: data.position
                    },
                    success: function (data) {
                        showMessage('success', '{{ __('Bạn đã chuyển vị trí menu thành công !') }}');
                    }
                }).fail(function () {
                    data.instance.refresh();
                });
            });

        $('.list_category').change(function () {
            var el = $(this)
            var id_el = $(this).closest('form').attr('id')
            let type = $(this).val()
            if (type != '{{ \App\Domain\Menu\Models\MenuItem::TYPE_LINK }}') {
                $.ajax({
                    method: 'post',
                    url: '{{ route('admin.menus.getDataCreate') }}',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'type': type
                    },
                    success: function (res) {
                        let data = res.data
                        if (data) {
                            let html = '<select class="custom-select form-control '
                            if(id_el == 'create-menu') {
                                html += 'select-create-content'
                            } else {
                                html += 'select-update-content'
                            }
                            html += '" name="item_id">\n'

                            $.each(data, function (key, val) {
                                html += '<option value="' + val.id + '">' + (val.title ?? val.name) + '</option>'
                            })
                            html += '</select>'
                            if (html != '') {
                                el.closest('form').find('.list_item').html(html)
                            }
                            setPageSelect($(".select-create-content"), $("#createMenu"))
                            setPageSelect($(".select-update-content"), $("#updateMenu"))
                        }
                    }
                })
            } else {
                let html = '<input type="text" name="item_content" value="" id="item_content" placeholder="Nội dung"\n' +
                    '                                   class="form-control ">'
                $('.list_category').closest('form').find('.list_item').html(html)
            }
        })

        function setPageSelect(selector, dropdownParent) {
            var menu_type =  selector.closest('form').find('select.list_category').val()
            function format(page) {
                return page.pretty_name;
            }
            if (selector.length > 0) {
                selector.select2({
                    dropdownParent: dropdownParent,
                    placeholder: 'Chọn item',
                    width: '100%',
                    ajax: {
                        url: '{{ route('admin.menus.search-data') }}',
                        datatype: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term,
                                page: params.page,
                                menu_type: menu_type
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data.data,
                                pagination: {
                                    more: (params.page * 15) < data.total
                                }
                            };
                        },
                    },
                    templateResult: format,
                    templateSelection: function(item) { return item.pretty_name || item.text; }
                });
            }
        }
        $(function () {
            $('#create-menu').submit(function (e) {
                e.preventDefault();
                if ($('#create-menu').valid()) {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: '{{ route('admin.menu-item.store') }}',
                        data: $('#create-menu').serialize()
                    }).done(function (d) {
                        location.reload();
                    }).fail(function () {
                        location.reload();
                    });
                }
            })
            $('#create-menu').validate({
                rules: {
                    name: {
                        required: true,
                        maxlength: 255
                    },
                    type: {
                        required: true
                    }
                },
                messages: {
                    name: {
                        required: "Vui lòng nhập tên menu.",
                        maxlength: "Tên menu quá dài!"
                    },
                    type: {
                        required: "Vui lòng chọn loại menu",
                    },
                },
            });
            $('#update-menu').validate({
                rules: {
                    name: {
                        required: true,
                        maxlength: 255
                    },
                    type: {
                        required: true
                    }
                },
                messages: {
                    name: {
                        required: "Vui lòng nhập tên menu.",
                        maxlength: "Tên menu quá dài!"
                    },
                    type: {
                        required: "Vui lòng chọn loại menu",
                    },
                },
            });

            setPageSelect($(".select-create-content"), $("#createMenu"));
            $(".select-create").select2({
                dropdownParent: $("#createMenu"),
            });
        })

    </script>
@endpush
@section('page-content')
    <div class="modal fade" id="createMenu" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form class="modal-content" id="create-menu"
                  method="post">
                @csrf
                <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('Tạo menu mới') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label">{{ __('Tên menu') }}</label>
                        <div>
                            <input type="text" name="name" value="" id="name_menu" placeholder="{{ __('Tên menu') }}"
                                   class="form-control ">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">{{ __('Loại menu') }}</label>
                        <div>
                            <select class="custom-select list_category form-control select-create" name="type">
                                @foreach(\App\Domain\Menu\Models\MenuItem::TYPE as $key => $type)
                                    <option value="{{ $key }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">{{ __('Item') }}</label>
                        <div class="list_item">
                            <select class="custom-select list_category form-control select-create-content" name="item_id">
                                @if($leagues)
                                    @foreach($leagues as $league)
                                        <option value="{{ $league->id }}">{{ $league->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Đóng') }}</button>
                    <button type="submit" class="btn btn-primary save_menu">{{ __('Lưu') }}</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="updateMenu" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form class="modal-content" action="{{ route('admin.menu-item.update') }}" id="update-menu"
                  method="post">
                @csrf
                <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                <input type="hidden" name="menu_item_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('Sửa menu') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label">{{ __('Tên menu') }}</label>
                        <div>
                            <input type="text" name="name" value="" id="name_menu" placeholder="{{ __('Tên menu') }}"
                                   class="form-control ">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">{{ __('Loại menu') }}</label>
                        <div class="list_type">
                            <select class="list_category form-control select-update" name="type">
                                @foreach(\App\Domain\Menu\Models\MenuItem::TYPE as $key => $type)
                                    <option value="{{ $key }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">{{ __('Item') }}</label>
                        <div class="list_item">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Đóng') }}</button>
                    <button type="submit" class="btn btn-primary save_menu">{{ __('Lưu') }}</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Inner container -->
    <form action="{{ route('admin.menus.update', $menu) }}" method="POST" data-block>
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

                                <input type="hidden" name="id" value="{{$menu->id}}">
                                <div class="collapse show" id="general">
                                    <x-text-field
                                        name="name"
                                        :label="__('Tên')"
                                        :value="$menu->name"
                                        required
                                    >
                                    </x-text-field>
                                    <div class="form-group row">
                                        <label for="select-taxon" class="col-lg-2 text-lg-right col-form-label"> <span class="text-danger">*</span>
                                            {{ __('Vị trí menu') }}
                                        </label>
                                        <div class="col-lg-9">
                                            <select class="form-control" name="position">
                                                @foreach(\App\Domain\Menu\Models\Menu::POSITION as $position=>$pos_name)
                                                    <option @if($menu->position == $position) selected @endif value="{{ $position }}">{{ $pos_name }}</option>
                                                @endforeach
                                            </select>
                                            @error('position')
                                            <span class="form-text text-danger">
                                                {{ $message }}
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="select-taxon" class="col-lg-2 text-lg-right col-form-label"> <span class="text-danger">*</span>
                                            {{ __('Ngôn ngữ') }}
                                        </label>
                                        <div class="col-lg-9">
                                            <select class="form-control" name="lang">
                                                <option @if($menu->lang == 'en') selected @endif value="en">EN</option>
                                                <option @if($menu->lang == 'vi') selected @endif value="vi">VI</option>
                                            </select>
                                            @error('lang')
                                            <span class="form-text text-danger">
                                                {{ $message }}
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend class="font-weight-semibold text-uppercase font-size-sm">
                                    {{ __('Cây menu') }}
                                </legend>

                                <div class="collapse show" id="tree">
                                    <div class="form-group">
                                        <div id="taxonomy-tree"></div>
                                    </div>
                                </div>
                                <div class="text-warning">
                                    <i class="fas fa-exclamation-triangle"></i> {{ __('Nhấp chuột phải vào một mục trong cây truy cập menu để thêm, xóa hoặc sửa.') }}
                                </div>
                            </fieldset>

                        </x-card>
                        <div class="d-flex justify-content-center align-items-center action" id="action-form">
                            <a href="{{ route('admin.menus.index') }}" class="btn btn-light"><i
                                    class="fal fa-arrow-left mr-2"></i>{{ __('Trở lại') }}</a>
                            <div class="btn-group ml-3">
                                <button class="btn btn-primary btn-block" data-loading><i
                                        class="fal fa-check mr-2"></i>{{ __('Lưu') }}</button>
                                <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"></button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="javascript:void(0)" class="dropdown-item submit-type"
                                       data-redirect="{{ route('admin.menus.index') }}">{{ __('Lưu và thoát') }}</a>
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
