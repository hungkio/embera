<form action="{{ $url }}" method="POST" id="shop-form" enctype="multipart/form-data">
    @csrf
    @method($method ?? 'POST')

    <div class="row">
        <div class="col-md-12">
            <x-card>
                <fieldset>
                    <legend class="font-weight-semibold text-uppercase font-size-sm">
                        {{ __('Thông tin cửa hàng') }}
                    </legend>

                    <x-text-field
                        name="shop_name"
                        :label="__('Tên cửa hàng')"
                        :value="$shop->shop_name ?? ''"
                        required
                    />

                    <x-text-field
                        name="address"
                        :label="__('Địa chỉ')"
                        :value="$shop->address ?? ''"
                        required
                    />

                    <x-text-field
                        name="shop_type"
                        :label="__('Loại cửa hàng')"
                        :value="$shop->shop_type ?? ''"
                        required
                    />

                    <x-text-field
                        name="contact_phone"
                        :label="__('Số điện thoại')"
                        :value="$shop->contact_phone ?? ''"
                        required
                    />

                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-lg-right">
                            {{ __('Chia lợi nhuận') }} <span class="text-danger">*</span> :
                        </label>

                        <div class="col-lg-3">
                            <select name="share_rate_type" id="share_rate_type" class="form-control" required>
                                <option value="percentage" {{ (old('share_rate_type', $shop->share_rate_type ?? 'percentage') === 'percentage') ? 'selected' : '' }}>
                                Phần trăm (%)
                                </option>
                                <option value="fixed" {{ (old('share_rate_type', $shop->share_rate_type ?? '') === 'fixed') ? 'selected' : '' }}>
                                Doanh thu cố định (VNĐ)
                                </option>
                            </select>
                        </div>

                        <div class="input-group col-lg-6">
                            <input type="number" step="0.01" required class="form-control"
                                   name="share_rate"
                                   id="share_rate"
                                   value="{{ old('share_rate', $shop->share_rate ?? '') }}">

                            <div class="input-group-append">
            <span class="input-group-text" id="share_rate_unit">
                {{ (old('share_rate_type', $shop->share_rate_type ?? 'percentage') === 'fixed') ? 'VNĐ' : '%' }}
            </span>
                            </div>
                        </div>
                    </div>


                    <x-text-field
                        name="strategy"
                        :label="__('Chiến lược')"
                        :value="$shop->strategy ?? ''"
                    />

                    <x-select-field
                        name="merchant_id"
                        :label="__('Thuộc Merchant')"
                        :options="$merchants"
                        :value="$shop->merchant_id ?? ''"
                        required
                        placeholder="-- Thuộc Merchant --"
                    />

                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-lg-right">{{ __('Thiết bị') }}</label>
                        <div class="col-lg-9">
                            <div id="device-container">
                                {{-- Thiết bị mẫu sẽ được clone --}}
                                <div class="device-entry d-flex mb-2">
                                    <select name="device_name[]" class="form-control mr-2" required>
                                        <option value="">-- Chọn thiết bị --</option>
                                        <option value="CP8">CP8</option>
                                        <option value="CP8 PRO">CP8 PRO</option>
                                        <option value="CP32">CP32</option>
                                    </select>

                                    <input type="number" name="device_quantity[]" class="form-control mr-2" placeholder="Số lượng" required>

                                    <input type="number" name="device_pin[]" class="form-control mr-2" placeholder="Số pin" required>

                                    <button type="button" class="btn btn-danger remove-device">–</button>
                                </div>
                            </div>

                            <button type="button" class="btn btn-sm btn-primary mt-2" id="add-device">+ Thêm thiết bị</button>
                        </div>
                    </div>


                    {{-- Hidden JSON input --}}
                    <input type="hidden" name="device_json" id="device_json">


                    <x-textarea-field
                        name="note"
                        :label="__('Ghi chú')"
                        :value="$shop->note ?? ''"
                    />

                </fieldset>
            </x-card>

            <div class="d-flex justify-content-center mt-4">
                <a href="{{ route('admin.shops.index') }}" class="btn btn-light">{{ __('Trở lại') }}</a>
                <button type="submit" class="btn btn-primary ml-2">{{ __('Lưu') }}</button>
            </div>
        </div>
    </div>
</form>
