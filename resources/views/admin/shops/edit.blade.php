@extends('admin.layouts.master')

@section('title', isset($shop->id) ? __('Chỉnh sửa :model', ['model' => $shop->shop_name]) : __('Tạo Shop'))

@section('page-header')
<x-page-header>
    {{ Breadcrumbs::render(isset($shop->id) ? 'admin.shops.edit' : '', $shop ?? null) }}
</x-page-header>
@stop

@section('page-content')
@include('admin.shops._form', [
'url' => isset($shop->id) ? route('admin.shops.update', $shop) : route('admin.shops.store'),
'shop' => $shop ?? new \App\Models\Shop(),
'method' => isset($shop->id) ? 'PUT' : 'POST',
'merchants' => \App\Models\Merchant::pluck('username', 'id'),
])
@stop

@push('js')
{!! JsValidator::formRequest(isset($shop->id) ? 'App\Http\Requests\Admin\ShopUpdateRequest' : 'App\Http\Requests\Admin\ShopStoreRequest', '#shop-form'); !!}
<script>
    // Hàm generate JSON từ các input
    function generateDeviceJSON() {
        let devices = [];
        document.querySelectorAll('#device-container .device-entry').forEach(entry => {
            const name = entry.querySelector('[name="device_name[]"]').value;
            const quantity = entry.querySelector('[name="device_quantity[]"]').value;
            const pin = entry.querySelector('[name="device_pin[]"]').value;
            if (name && quantity && pin) {
                devices.push({ name, quantity: parseInt(quantity), pin: parseInt(pin) });
            }
        });
        document.getElementById('device_json').value = JSON.stringify({ devices });
    }

    // Fill dữ liệu từ device_json khi load (chỉ cho edit)
    document.addEventListener('DOMContentLoaded', function () {
        const deviceJson = @json($shop->device_json ?? null);
        if (deviceJson && deviceJson.devices) {
            const container = document.getElementById('device-container');
            container.innerHTML = ''; // Xóa template mặc định
            deviceJson.devices.forEach((device, index) => {
                const entry = document.createElement('div');
                entry.className = 'device-entry row mb-2';
                entry.innerHTML = `
                    <div class="col-md-4">
                        <input type="text" name="device_name[]" class="form-control" value="${device.name || ''}" placeholder="Tên thiết bị" required>
                    </div>
                    <div class="col-md-4">
                        <input type="number" name="device_quantity[]" class="form-control" value="${device.quantity || ''}" placeholder="Số lượng" required>
                    </div>
                    <div class="col-md-3">
                        <select name="device_pin[]" class="form-control" required>
                            <option value="8" ${device.pin === 8 ? 'selected' : ''}>8</option>
                            <option value="12" ${device.pin === 12 ? 'selected' : ''}>12</option>
                            <option value="32" ${device.pin === 32 ? 'selected' : ''}>32</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger remove-device">X</button>
                    </div>
                `;
                container.appendChild(entry);
            });
        }
    });

    // Thêm mới thiết bị
    document.getElementById('add-device').addEventListener('click', function () {
        const container = document.getElementById('device-container');
        const entry = document.createElement('div');
        entry.className = 'device-entry row mb-2';
        entry.innerHTML = `
            <div class="col-md-4">
                <input type="text" name="device_name[]" class="form-control" placeholder="Tên thiết bị" required>
            </div>
            <div class="col-md-4">
                <input type="number" name="device_quantity[]" class="form-control" placeholder="Số lượng" required>
            </div>
            <div class="col-md-3">
                <select name="device_pin[]" class="form-control" required>
                    <option value="8">8</option>
                    <option value="12">12</option>
                    <option value="32">32</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger remove-device">X</button>
            </div>
        `;
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
