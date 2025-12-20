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
        $(document).on('click', '.sendmail', function () {
            const rows = $('tr.selected');

            if (!rows.length) {
                alert('Vui lòng chọn ít nhất một merchant để gửi mail.');
                return;
            }

            const ids = rows.map(function () {
                return $(this).attr('id').replace('merchant_', '');
            }).get();

            $.ajax({
                url: "{{ route('admin.merchants.send-email') }}",
                type: 'POST',
                data: {
                    ids: ids,
                    _token: '{{ csrf_token() }}'
                },
                success: function (res) {
                    alert(res.message || 'Gửi mail thành công');
                },
                error: function () {
                    alert('Gửi mail thất bại');
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        $(document).on('click', '.sendzalo', function () {
            const rows = $('tr.selected');

            if (!rows.length) {
                alert('Vui lòng chọn ít nhất một merchant.');
                return;
            }

            const ids = rows.map(function () {
                return $(this).attr('id').replace('merchant_', '');
            }).get();

            $.ajax({
                url: "{{ route('admin.merchants.send-zalo') }}",
                type: 'POST',
                data: {
                    ids: ids,
                    _token: '{{ csrf_token() }}'
                },
                success: function (json) {
                    if (json.success) {
                        alert(json.message);
                    } else {
                        alert(json.message || 'Gửi Zalo thất bại.');
                    }
                },
                error: function (xhr, status, error) {
                    console.error(error);
                    alert('Đã xảy ra lỗi khi gửi Zalo.');
                }
            });
        });
    });

</script>
@endpush
