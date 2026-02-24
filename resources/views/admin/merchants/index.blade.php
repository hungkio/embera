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

    <div class="row mb-3">
        <div class="col-md-3">
            <label class="form-label">Từ ngày</label>
            <input type="date" id="range_start" class="form-control" value="2025-01-15">
        </div>
        <div class="col-md-3">
            <label class="form-label">Đến ngày</label>
            <input type="date" id="range_end" class="form-control" value="2026-01-31">
        </div>
    </div>

    {{$dataTable->table()}}
</x-card>


@stop

@push('js')
{{ $dataTable->scripts() }}
<script>
    document.addEventListener('DOMContentLoaded', function () {

        function getSelectedIds() {
            const rows = $('tr.selected');
            if (!rows.length) return [];
            return rows.map(function () {
                return $(this).attr('id').replace('merchant_', '');
            }).get();
        }

        function getDateRangeOrAlert() {
            const start_date = $('#range_start').val();
            const end_date = $('#range_end').val();

            if (!start_date || !end_date) {
                alert('Vui lòng chọn đầy đủ Từ ngày và Đến ngày.');
                return null;
            }
            if (start_date > end_date) {
                alert('Từ ngày không được lớn hơn Đến ngày.');
                return null;
            }
            return {start_date, end_date};
        }

        // ===== SEND MAIL =====
        $(document).on('click', '.sendmail', function () {
            const ids = getSelectedIds();
            if (!ids.length) {
                alert('Vui lòng chọn ít nhất một merchant để gửi mail.');
                return;
            }

            const range = getDateRangeOrAlert();
            if (!range) return;

            $.ajax({
                url: "{{ route('admin.merchants.send-email') }}",
                type: 'POST',
                data: {
                    ids: ids,
                    start_date: range.start_date,
                    end_date: range.end_date,
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

        // ===== SEND ZALO =====
        $(document).on('click', '.sendzalo', function () {
            const ids = getSelectedIds();
            if (!ids.length) {
                alert('Vui lòng chọn ít nhất một merchant.');
                return;
            }

            const range = getDateRangeOrAlert();
            if (!range) return;

            $.ajax({
                url: "{{ route('admin.merchants.send-zalo') }}",
                type: 'POST',
                data: {
                    ids: ids,
                    start_date: range.start_date,
                    end_date: range.end_date,
                    _token: '{{ csrf_token() }}'
                },
                success: function (json) {
                    if (json.success) {
                        alert(json.message || 'Gửi Zalo thành công');
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

        // ===== SEND ZALO CONTRACT (optional) =====
        $(document).on('click', '.sendzalo-contract', function () {
            const ids = getSelectedIds();
            if (!ids.length) {
                alert('Vui lòng chọn ít nhất một merchant.');
                return;
            }

            const range = getDateRangeOrAlert();
            if (!range) return;

            if (!confirm('Bạn muốn gửi thông báo Hợp đồng cho các merchant đã chọn?')) return;

            $.ajax({
                url: "{{ route('admin.merchants.send-zalo-contract') }}",
                type: 'POST',
                data: {
                    ids: ids,
                    start_date: range.start_date,
                    end_date: range.end_date,
                    _token: '{{ csrf_token() }}'
                },
                success: function (json) {
                    if (json.success) {
                        alert(json.message || 'Gửi Zalo thành công');
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
