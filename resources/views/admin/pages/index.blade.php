@extends('admin.layouts.master')

@section('title', __('Trang'))
@section('page-header')
    <x-page-header>
        {{ Breadcrumbs::render() }}
    </x-page-header>
@stop

@section('page-content')
    <x-card>
        {{$dataTable->table()}}
    </x-card>

@stop

@push('js')
    <script src="{{ asset('backend/global_assets/js/plugins/forms/styling/switchery.min.js') }}"></script>
    {{$dataTable->scripts()}}
    <script>
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
        @can('pages.create')
        $('.buttons-create').removeClass('d-none')
        @endcan
        @can('pages.delete')
        $('.buttons-selected').removeClass('d-none')
        @endcan
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endpush
