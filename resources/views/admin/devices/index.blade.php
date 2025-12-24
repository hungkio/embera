@extends('admin.layouts.master')

@section('title', __('Trạng thái thiết bị'))

@section('page-header')
    <x-page-header>
        {{ Breadcrumbs::render('admin.device-status.index') }}
    </x-page-header>
@stop

@push('css')
<style>
    /* Hiệu ứng làm mờ bảng khi đang reload */
    .table-loading {
        position: relative;
        opacity: 0.5;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    /* Overlay loading icon */
    .table-loading::after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        width: 40px;
        height: 40px;
        margin-top: -20px;
        margin-left: -20px;
        border: 4px solid #0dcaf0;
        border-top-color: transparent;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        z-index: 10;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>
@endpush

@section('page-content')
    <form method="GET" action="{{ route('admin.device-status.index') }}" class="row g-3 mb-4 align-items-end filters">
        <div class="col-md-3">
            <label for="shop_name">Cửa hàng</label>
            <select name="shop_name[]" id="shop_name" class="form-select select2" multiple>
                <option value="">-- Tất cả --</option>
                @foreach($shopNameList as $shop)
                    <option value="{{ $shop }}" {{ (request('shop_name') ? in_array($shop, request('shop_name')) : false) ? 'selected' : '' }}>
                        {{ $shop }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-12 text-end mt-3">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-funnel-fill"></i> Lọc dữ liệu
            </button>
        </div>
    </form>
    <x-card title="Danh sách trạng thái thiết bị">
        <div class="table-responsive">
            {!! $dataTable->table([
                'class' => 'table table-striped table-bordered nowrap',
                'style' => 'width:100%',
                'id' => 'device-status-datatable',
                'dom' => 'Bfrtip',
                'buttons' => [
                    'reload', // chỉ hiển thị nút reload
                ],
            ], true) !!}
        </div>
    </x-card>
@stop

@push('js')
    {!! $dataTable->scripts() !!}
    <script>
    $(document).ready(function () {
        var table = $('#device-status-datatable').DataTable();

        // 🔁 Nút reload thủ công
        $(document).on('click', '.buttons-reload', function () {
            showTableLoading();
            table.ajax.reload(hideTableLoading, false);
        });

        // ⚡ Nút đồng bộ ngay
        $(document).on('click', '.js-sync-now', function () {
            if (confirm('Bạn có chắc chắn muốn đồng bộ trạng thái thiết bị ngay bây giờ?')) {
                showTableLoading();
                $.ajax({
                    url: '{{ route("admin.device-status.sync") }}',
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success(res) {
                        alert(res.message || 'Đồng bộ thành công!');
                        table.ajax.reload(hideTableLoading, false);
                    },
                    error() {
                        alert('Lỗi khi đồng bộ thiết bị!');
                        hideTableLoading();
                    }
                });
            }
        });

        // 🎨 Hàm bật/tắt hiệu ứng loading
        function showTableLoading() {
            $('#device-status-datatable').addClass('table-loading');
        }
        function hideTableLoading() {
            $('#device-status-datatable').removeClass('table-loading');
        }

        $('.filters .select2').select2({
            // width: '100%',
        });
    });
    </script>
@endpush
