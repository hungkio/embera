@extends('admin.layouts.master')

@section('title', isset($shop->id) ? __('Chỉnh sửa :model', ['model' => $shop->shop_name]) : __('Tạo Shop'))

@section('page-header')
<x-page-header>
    {{ Breadcrumbs::render('admin.shops.edit', $shop) }}
</x-page-header>
@stop

@section('page-content')
@include('admin.shops._form', [
'url' => route('admin.shops.update', $shop),
'shop' => $shop,
'method' => 'PUT',
'merchants' => \App\Models\Merchant::pluck('username', 'id'),
])
@stop

@push('js')
{!! JsValidator::formRequest('App\Http\Requests\Admin\ShopUpdateRequest', '#shop-form'); !!}

<script>
    function generateDeviceJSON() {
        let devices = [];
        document.querySelectorAll('#device-container .device-entry').forEach(entry => {
            const name = entry.querySelector('[name="device_name[]"]').value;
            const code = entry.querySelector('[name="device_code[]"]').value;
            const quantity = entry.querySelector('[name="device_quantity[]"]').value;
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

    document.addEventListener('DOMContentLoaded', function () {
        const deviceJson = @json($shop->device_json ?? null);
        const container = document.getElementById('device-container');

        if (deviceJson && deviceJson.devices && container) {
            const template = container.firstElementChild;
            container.innerHTML = ''; // Clear existing

            deviceJson.devices.forEach(device => {
                const entry = template.cloneNode(true);
                entry.querySelector('[name="device_name[]"]').value = device.name || '';
                entry.querySelector('[name="device_code[]"]').value = device.code || '';
                entry.querySelector('[name="device_quantity[]"]').value = device.quantity || '';
                entry.querySelector('[name="device_pin[]"]').value = device.pin || '';
                container.appendChild(entry);
            });

            // Remove template if still empty entry exists at top
            if (container.firstElementChild.querySelector('[name="device_name[]"]').value === '') {
                container.firstElementChild.remove();
            }
        }

        const typeSelect = document.getElementById('share_rate_type');
        const unitLabel = document.getElementById('share_rate_unit');
        function updateUnit() {
            unitLabel.innerText = typeSelect.value === 'fixed' ? 'VNĐ' : '%';
        }
        typeSelect.addEventListener('change', updateUnit);
        updateUnit();
    });

    document.getElementById('add-device').addEventListener('click', function () {
        const container = document.getElementById('device-container');
        const entry = container.firstElementChild.cloneNode(true);
        entry.querySelectorAll('input').forEach(input => input.value = '');
        container.appendChild(entry);
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-device')) {
            const entry = e.target.closest('.device-entry');
            if (document.querySelectorAll('.device-entry').length > 1) {
                entry.remove();
            }
        }
    });

    document.getElementById('shop-form').addEventListener('submit', function () {
        generateDeviceJSON();
    });
</script>
@endpush
