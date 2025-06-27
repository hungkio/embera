@extends('admin.layouts.master')

@section('title', __('Hợp đồng'))

@section('page-header')
<x-page-header>
    {{ Breadcrumbs::render('admin.contracts.index') }}
</x-page-header>
@stop

@section('page-content')
@can('contracts.create')
@endcan

<x-card title="Hợp Đồng">
    {{$dataTable->table()}}
</x-card>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content" method="POST" action="{{ route('admin.contracts.import') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-header  text-center">
                <h4 class="modal-title" id="exampleModalLabel">Nhập HĐ</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label text-lg-right" for="redo">
                        File excel:
                    </label>
                    <div class="col-lg-9">
                        <input  type="file" name="file" id="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
@stop

@push('js')
{{$dataTable->scripts()}}
<script>
    $(function () {
        $('.import').click(function () {
            $('#exampleModal').modal('show')
        })
    })
</script>
@endpush
