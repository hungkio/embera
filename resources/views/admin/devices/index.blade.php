@extends('admin.layouts.master')

@section('title', __('Trạng thái thiết bị'))

@section('page-header')
    <x-page-header>
        {{ Breadcrumbs::render('admin.device-status.index') }}
    </x-page-header>
@stop

@push('css')
<style>
    .table-loading {
        position: relative;
        opacity: 0.5;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

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
        <div class="col-md-2">
            <label for="filter_mode">Chế độ lọc</label>
            <select name="filter_mode" id="filter_mode" class="form-control">
                <option value="default" {{ request('filter_mode', 'default') === 'default' ? 'selected' : '' }}>Mặc định</option>
                <option value="advanced" {{ request('filter_mode') === 'advanced' ? 'selected' : '' }}>Nâng cao (bỏ các shop test)</option>
            </select>
        </div>
        <div class="col-md-2">
            <label for="status">Trạng thái</label>
            <select name="status" id="status" class="form-control">
                <option value="">-- Tất cả --</option>
                <option value="online" {{ request('status') === 'online' ? 'selected' : '' }}>Online</option>
                <option value="offline" {{ request('status') === 'offline' ? 'selected' : '' }}>Offline</option>
            </select>
        </div>
        <div class="col-md-2">
            <label for="product_type">Sản phẩm</label>
            <select name="product_type" id="product_type" class="form-control">
                <option value="">-- Tất cả --</option>
                <option value="8_pin" {{ request('product_type') === '8_pin' ? 'selected' : '' }}>VNxxxxx - 8 pin</option>
                <option value="8_pin_screen" {{ request('product_type') === '8_pin_screen' ? 'selected' : '' }}>VNSxxxxx - 8 pin có màn</option>
                <option value="32_pin" {{ request('product_type') === '32_pin' ? 'selected' : '' }}>VNSBABPxxxxx - 32 pin</option>
            </select>
        </div>
        <div class="col-md-2">
            <label for="assignment_status">Trạng thái đặt</label>
            <select name="assignment_status" id="assignment_status" class="form-control">
                <option value="">-- Tất cả --</option>
                <option value="assigned" {{ request('assignment_status') === 'assigned' ? 'selected' : '' }}>Đã đặt</option>
                <option value="unassigned" {{ request('assignment_status') === 'unassigned' ? 'selected' : '' }}>Chưa đặt</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="shop_keyword">Cửa hàng</label>
            <input type="text" name="shop_keyword" id="shop_keyword" class="form-control" value="{{ request('shop_keyword') }}" placeholder="Nhập mã/tên cửa hàng, ví dụ MB-HN">
        </div>
        <div class="col-md-12 text-end mt-3">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Tìm kiếm
            </button>
            <a href="{{ route('admin.device-status.index') }}" class="btn btn-light">Đặt lại</a>
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
                    'reload',
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

        $(document).on('click', '.buttons-reload', function () {
            showTableLoading();
            table.ajax.reload(hideTableLoading, false);
        });

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

        function showTableLoading() {
            $('#device-status-datatable').addClass('table-loading');
        }
        function hideTableLoading() {
            $('#device-status-datatable').removeClass('table-loading');
        }

    });
    </script>
@endpush
