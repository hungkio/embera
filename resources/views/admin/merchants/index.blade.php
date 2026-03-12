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
    // Chỉ dùng một khối DOMContentLoaded
    document.addEventListener('DOMContentLoaded', function () {

        // Hàm lấy IDs đã chọn (đã hoạt động tốt với mail/zalo → dùng lại)
        function getSelectedIds() {
            const rows = $('tr.selected');
            if (!rows.length) return [];
            return rows.map(function () {
                const idAttr = $(this).attr('id');
                return idAttr ? idAttr.replace('merchant_', '') : null;
            }).get().filter(Boolean); // loại bỏ null nếu có
        }

        function getDateRangeOrAlert() {
            const start = $('#range_start').val();
            const end = $('#range_end').val();

            if (!start || !end) {
                alert('Vui lòng chọn đầy đủ Từ ngày và Đến ngày.');
                return null;
            }
            if (start > end) {
                alert('Từ ngày không được lớn hơn Đến ngày.');
                return null;
            }
            return {start_date: start, end_date: end};
        }

        // Gửi Mail
        $(document).on('click', '.sendmail', function () {
            const ids = getSelectedIds();
            if (!ids.length) return alert('Vui lòng chọn ít nhất một merchant để gửi mail.');

            const range = getDateRangeOrAlert();
            if (!range) return;

            $.ajax({
                url: "{{ route('admin.merchants.send-email') }}",
                type: 'POST',
                data: {ids, ...range, _token: '{{ csrf_token() }}'},
                success: res => alert(res.message || 'Gửi mail thành công'),
                error: () => alert('Gửi mail thất bại')
            });
        });

        // Gửi Zalo (giữ nguyên nếu đang chạy tốt)
        $(document).on('click', '.sendzalo', function () {
            const ids = getSelectedIds();
            if (!ids.length) return alert('Vui lòng chọn ít nhất một merchant.');

            const range = getDateRangeOrAlert();
            if (!range) return;

            $.ajax({
                url: "{{ route('admin.merchants.send-zalo') }}",
                type: 'POST',
                data: {ids, ...range, _token: '{{ csrf_token() }}'},
                success: json => alert(json.success ? (json.message || 'Gửi Zalo thành công') : (json.message || 'Gửi Zalo thất bại.')),
                error: (xhr, status, error) => {
                    console.error('Zalo error:', error);
                    alert('Đã xảy ra lỗi khi gửi Zalo.');
                }
            });
        });

        // ===== TẠO LOG CHIA SẺ =====
        $(document).on('click', '.btn-create-log', function (e) {
            e.preventDefault();

            console.log('Nút Tạo log được bấm!');

            const ids = getSelectedIds();
            console.log('IDs được chọn:', ids);

            if (!ids.length) {
                return alert('Vui lòng chọn ít nhất một merchant để tạo log!');
            }

            const start_date = $('#range_start').val();
            const end_date = $('#range_end').val();

            if (!start_date || !end_date) {
                return alert('Vui lòng chọn đầy đủ Từ ngày và Đến ngày cho kỳ log!');
            }
            if (start_date > end_date) {
                return alert('Từ ngày không được lớn hơn Đến ngày.');
            }

            if (!confirm(`Tạo log cho ${ids.length} merchant từ ${start_date} đến ${end_date}?`)) {
                return;
            }

            $.ajax({
                url: '{{ route("admin.merchants.create-share-log") }}',
                type: 'POST',
                data: {
                    merchant_ids: ids,
                    start_date: start_date,
                    end_date: end_date,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    console.log('Phản hồi:', response);
                    if (response.success) {
                        alert(response.message || `Đã tạo ${response.created_count} log!`);
                    } else {
                        alert('Lỗi: ' + (response.message || 'Không thể tạo log'));
                    }
                },
                error: function (xhr) {
                    console.error('Lỗi AJAX:', xhr);
                    alert('Có lỗi: ' + (xhr.responseJSON?.message || 'Lỗi hệ thống') + ' (mã: ' + xhr.status + ')');
                }
            });
        });

    });
</script>
@endpush
