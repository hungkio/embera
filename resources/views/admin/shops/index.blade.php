@extends('admin.layouts.master')

@section('title', __('Cửa Hàng'))

@section('page-header')
<x-page-header>
    {{ Breadcrumbs::render('admin.shops.index') }}
</x-page-header>
@stop

@section('page-content')
@can('shops.create')
@endcan

<x-card title="Cửa hàng">
    {{$dataTable->table()}}
</x-card>
@stop

@push('js')
{{$dataTable->scripts()}}
<script>

</script>
@endpush
@push('js')
<script>
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('toggle-bind')) {
            const btn = e.target;
            const shopId = btn.dataset.id;
            const newState = btn.dataset.state;

            if (!confirm('Bạn có chắc muốn thay đổi trạng thái bind không?')) return;

            fetch(`/admin/shops/${shopId}/toggle-bind`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ is_bound: newState })
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
