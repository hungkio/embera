@extends('admin.layouts.master')

@section('title', __('Pins'))

@section('page-header')
    <x-page-header>
        {{ Breadcrumbs::render('admin.pins.index') }}
    </x-page-header>
@stop

@section('page-content')
    @can('pins.create')
    @endcan

    <x-card title="Danh sách Pins">
        <div class="table-responsive">
            {!! $dataTable->table([
                'class' => 'table table-striped table-bordered nowrap',
                'style' => 'width:100%',
                'id' => 'pin-datatable',
                'dom' => 'Bfrtip',
                'buttons' => [
                    'create',
                    'bulkDelete',
                    'reset',
                    'selected'
                ],
            ], true) !!}
        </div>
    </x-card>

    <!-- Modal Import -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form class="modal-content" method="POST" action="{{ route('admin.pins.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header text-center">
                    <h4 class="modal-title" id="exampleModalLabel">Nhập Pins</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-lg-right" for="file">File Excel:</label>
                        <div class="col-lg-9">
                            <input type="file" name="file" id="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                            <small class="form-text text-muted">File phải có hai cột: imei và serial_number với tiêu đề hàng đầu tiên.</small>
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
    {!! $dataTable->scripts() !!}
    <script>
        $(document).ready(function() {
            console.log('DataTable initializing...');
            var table = $('#pin-datatable').DataTable();
            console.log('DataTable initialized:', table);

            // Gắn sự kiện click cho nút import
            $('.import').click(function() {
                console.log('Import button clicked');
                $('#exampleModal').modal('show');
            });
        });
    </script>
@endpush
