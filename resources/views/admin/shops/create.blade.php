@extends('admin.layouts.master')

@section('title', __('Tạo Shop'))

@section('page-header')
<x-page-header>
    {{ Breadcrumbs::render() }}
</x-page-header>
@stop

@section('page-content')
@include('admin.shops._form', [
'url' => route('admin.shops.store'),
'shop' => new \App\Models\Shop,
'merchants' => \App\Models\Merchant::pluck('username', 'id'),
])

@stop

@push('js')
{!! JsValidator::formRequest('App\Http\Requests\Admin\ShopStoreRequest', 'form'); !!}
<script>
    function generateDeviceJSON() {
        let devices = [];
        document.querySelectorAll('#device-container .device-entry').forEach(entry => {
            const name = entry.querySelector('[name="device_name[]"]').value;
            const code = entry.querySelector('[name="device_code[]"]').value;
            const pin = entry.querySelector('[name="device_pin[]"]').value;

            if (name && code && quantity && pin) {
                devices.push({
                    name,
                    code,
                    quantity: parseInt(quantity),
                    pin: parseInt(pin)
                });
            }
        });
        document.getElementById('device_json').value = JSON.stringify({ devices });
    }


    // Thêm mới thiết bị
    document.getElementById('add-device').addEventListener('click', function () {
        const container = document.getElementById('device-container');
        const entry = container.firstElementChild.cloneNode(true);
        entry.querySelectorAll('input').forEach(input => input.value = '');
        container.appendChild(entry);
    });

    // Xoá thiết bị
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-device')) {
            const entry = e.target.closest('.device-entry');
            if (document.querySelectorAll('.device-entry').length > 1) {
                entry.remove();
            }
        }
    });

    // Trước khi submit -> generate JSON
    document.getElementById('shop-form').addEventListener('submit', function (e) {
        generateDeviceJSON();
    });

    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('share_rate_type');
        const unitLabel = document.getElementById('share_rate_unit');

        function updateUnit() {
            unitLabel.innerText = typeSelect.value === 'fixed' ? 'VNĐ' : '%';
        }

        typeSelect.addEventListener('change', updateUnit);
        updateUnit(); // chạy khi load form
    });
</script>
@endpush
