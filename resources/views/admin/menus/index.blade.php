@extends('admin.layouts.master')

@section('title', __('Quản lý menu'))
@section('page-header')
    <x-page-header>
        {{ Breadcrumbs::render() }}
    </x-page-header>
@stop

@push('css')
    <style>
        label.error {
            color: red;
        }
    </style>
@endpush

@section('page-content')
    <x-card>
        {{$dataTable->table()}}
    </x-card>
    @can('menus.store')
        <div class="modal fade" id="createMenu" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form class="modal-content" id="create-menu" action="{{ route('admin.menus.store') }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Tạo menu mới</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label">Tên menu</label>
                            <div>
                                <input type="text" name="name" value="" id="name_menu" placeholder="Tên menu" class="form-control ">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Vị trí</label>
                            <div>
                                <select class="form-control" name="position">
                                    @foreach(\App\Domain\Menu\Models\Menu::POSITION as $position=>$pos_name)
                                        <option value="{{ $position }}">{{ $pos_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Ngôn ngữ</label>
                            <div>
                                <select class="form-control" name="lang">
                                    <option value="en">EN</option>
                                    <option value="vi">VI</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary save_menu">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    @endcan
@stop

@push('js')
    <script src="{{ asset('backend/global_assets/js/plugins/forms/styling/switchery.min.js') }}"></script>
    {{$dataTable->scripts()}}
    <script>
        $(function () {
            @cannot('menus.store')
            $('.buttons-create').remove()
            @endcan
            @cannot('menus.edit')
            $('.buttons-create').remove()
            @endcan
            @can('menus.delete')
            $('.bg-danger').removeClass('d-none')
            @endcan
            $('#create-menu').validate({
                rules: {
                    name: {
                        required: true,
                        maxlength: 255,
                    }
                },
                messages: {
                    name: {
                        required: "Hãy nhập tên menu",
                        maxlength: "Tên menu quá dài",
                    }
                }
            })
            $('#create-menu').submit(function (e) {
                e.preventDefault();
                if ($('#create-menu').valid()) {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: '{{ route('admin.menus.store') }}',
                        data: $('#create-menu').serialize()
                    }).done(function (d) {
                        location.reload();
                    }).fail(function (data) {
                        let msg = ''
                        if (data.message) {
                            msg = data.message
                        } else {
                            msg = data.responseJSON.errors.name[0]
                        }
                        showMessage('error', msg);
                    });
                }
            })
        })
        $(document).on('change', '#select_status', function () {
            var status = $(this).val();
            var url = $(this).attr('data-url');
            confirmAction('Bạn có muốn thay đổi trạng thái ?', function (result) {
                if (result) {
                    $.ajax({
                        url: url,
                        data: {
                            'status': status
                        },
                        type: 'POST',
                        dataType: 'json',
                        success: function (res) {
                            if (res.status == true) {
                                showMessage('success', res.message);
                            } else {
                                showMessage('error', res.message);
                            }
                            window.LaravelDataTables['{{ $dataTable->getTableAttribute('id') }}'].ajax.reload();
                        },
                    });
                } else {
                    window.LaravelDataTables['{{ $dataTable->getTableAttribute('id') }}'].ajax.reload();
                }
            });
        });

        $('#createMenu').on('hidden.bs.modal', function () {
            // do something…
            $('#name_menu').val('')
            $('#name_menu-error').text('')
        })
    </script>
@endpush
