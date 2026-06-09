@extends('admin.layouts.master')

@section('title', __('Cửa Hàng'))

@section('page-header')
<x-page-header>
    {{ Breadcrumbs::render('admin.shops.index') }}
</x-page-header>
@stop

@section('page-content')
    <form method="GET" action="{{ route('admin.shops.index') }}" class="row g-3 mb-4 align-items-end filters">
        <div class="col-md-3">
            <label for="has_device" class="form-label font-weight-bold">Thiết bị</label>
            <select name="has_device" id="has_device" class="form-control">
                <option value="all" {{ request('has_device') === 'all' || !request()->has('has_device') ? 'selected' : '' }}>-- Tất cả --</option>
                <option value="yes" {{ request('has_device') === 'yes' ? 'selected' : '' }}>Có thiết bị</option>
                <option value="no" {{ request('has_device') === 'no' ? 'selected' : '' }}>Không có thiết bị</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary mr-2">
                <i class="fal fa-filter mr-1"></i> Lọc
            </button>
            <a href="{{ route('admin.shops.index') }}" class="btn btn-light">Đặt lại</a>
        </div>
    </form>

<x-card title="Cửa hàng">
    <div class="table-responsive">
        {!! $dataTable->table([
        'class' => 'table table-striped table-bordered nowrap',
        'style' => 'width:100%',
        ], true) !!}
    </div>
</x-card>
@stop

@push('js')
{{$dataTable->scripts()}}
<script>

</script>
@endpush
@push('js')
<script>
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('toggle-bind')) {
            const btn = e.target;
            const shopId = btn.dataset.id;
            const newState = btn.dataset.state;

            if (!confirm('Bạn có chắc muốn thay đổi trạng thái bind không?')) {
                return;
            }

            fetch(`/admin/shops/${shopId}/toggle-bind`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({is_bound: newState})
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.LaravelDataTables["ShopDataTable"].ajax.reload();
                    alert(data.message);
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert('Lỗi khi cập nhật bind.');
            });
        }
    });
</script>
@endpush
