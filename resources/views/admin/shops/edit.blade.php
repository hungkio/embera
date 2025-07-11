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
            const pin = entry.querySelector('[name="device_pin[]"]').value;

            if (name && code && pin) {
                devices.push({
                    name,
                    code,
                    pin: parseInt(pin)
                });
            }
        });
        document.getElementById('device_json').value = JSON.stringify({ devices });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const deviceJson = @json($shop->device_json ?? null);
        const container = document.getElementById('device-container');
        const template = container.firstElementChild;

        container.innerHTML = ''; // Clear all

        if (deviceJson && Array.isArray(deviceJson.devices) && deviceJson.devices.length > 0) {
            deviceJson.devices.forEach(device => {
                const entry = template.cloneNode(true);
                entry.querySelector('[name="device_name[]"]').value = device.name || '';
                entry.querySelector('[name="device_code[]"]').value = device.code || '';
                entry.querySelector('[name="device_pin[]"]').value = device.pin || '';
                container.appendChild(entry);
            });
        } else {
            // Không có thiết bị nào => hiển thị 1 dòng trống
            const emptyEntry = template.cloneNode(true);
            emptyEntry.querySelectorAll('input').forEach(input => input.value = '');
            emptyEntry.querySelector('select').value = ''; // nếu dùng <select>
            container.appendChild(emptyEntry);
        }
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
