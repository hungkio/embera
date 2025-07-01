@extends('admin.layouts.master')

@section('title', __('Merchants'))

@section('page-header')
<x-page-header>
    {{ Breadcrumbs::render('admin.merchants.index') }}
</x-page-header>
@stop

@section('page-content')
@can('merchants.create')
@endcan

<x-card title="Merchant">
    {{$dataTable->table()}}
</x-card>

@stop

@push('js')
{{ $dataTable->scripts() }}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelector('.sendmail')?.addEventListener('click', function () {
            const selectedRows = document.querySelectorAll('tr.selected');
            if (!selectedRows.length) {
                alert('Vui lòng chọn ít nhất một merchant để gửi mail.');
                return;
            }

            const ids = Array.from(selectedRows).map(row => {
                const idStr = row.id; // ví dụ: merchant_5
                return idStr.replace('merchant_', '');
            });

            fetch('{{ route('admin.merchants.send-email') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ids })
            })
        .then(res => res.json())
            .then(data => {
                alert(data.message || 'Gửi mail thành công');
            })
            .catch(() => alert('Gửi mail thất bại'));
        });
    });
</script>
@endpush
