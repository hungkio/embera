@extends('admin.layouts.master')

@section('title', __('Bài viết'))
@section('page-header')
    <x-page-header>
        {{ Breadcrumbs::render() }}
    </x-page-header>
@stop

@push('css')
    <style>
        @media (max-width: 767.98px) {
            .btn-danger {
                margin-left: 0!important;
            }
        }
        @media (width: 320px) {
            .btn-danger {
                margin-left: .625rem!important;
            }
        }
    </style>
@endpush

@section('page-content')
    <x-card>
        {{$dataTable->table()}}
    </x-card>

@stop

@push('js')
    {{$dataTable->scripts()}}
    <script>
        $(document).on('change','#select_status', function () {
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
                        success: function(res) {
                            if(res.status == true){
                                showMessage('success', res.message);
                            }else{
                                showMessage('error', res.message);
                            }
                            window.LaravelDataTables['{{ $dataTable->getTableAttribute('id') }}'].ajax.reload();
                        },
                    });
                }else{
                    window.LaravelDataTables['{{ $dataTable->getTableAttribute('id') }}'].ajax.reload();
                }
            });
        });
        @can('posts.create')
        $('.buttons-create').removeClass('d-none')
        @endcan
        @can('posts.delete')
        $('.btn-danger').removeClass('d-none')
        @endcan
        @can('posts.update')
        $('.btn-warning').removeClass('d-none')
        @endcan

        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endpush
