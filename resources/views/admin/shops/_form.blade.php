<form action="{{ $url }}" method="POST" id="shop-form" enctype="multipart/form-data">
    @csrf
    @method($method ?? 'POST')

    <div class="row">
        <div class="col-md-12">
            <x-card class="shadow-sm">
                <fieldset class="p-4">
                    <legend class="border-bottom pb-2 mb-4 font-weight-bold text-primary">
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

                    <div class="form-group row">
                        <label for="shop_type" class="col-lg-2 col-form-label text-lg-right">
                            <span class="text-danger">*</span>
                            {{ __('Loại cửa hàng') }}
                        </label>
                        <div class="col-lg-9">
                            <select name="shop_type" id="shop_type" class="form-control" required>
                                <option value="">-- Chọn loại cửa hàng --</option>
                                <option value="Airport(机场)" {{ old(
                                'shop_type', $shop->shop_type ?? '') === 'Airport(机场)' ?
                                'selected' : '' }}>Airport(机场)</option>
                                <option value="Arcade(游乐中心)" {{ old(
                                'shop_type', $shop->shop_type ?? '') === 'Arcade(游乐中心)' ?
                                'selected' : '' }}>Arcade(游乐中心)</option>
                                <option value="Bar(酒吧)" {{ old(
                                'shop_type', $shop->shop_type ?? '') === 'Bar(酒吧)' ? 'selected' :
                                '' }}>Bar(酒吧)</option>
                                <option value="Beauty Salon/Tattoo Shop(美容院/纹身店)" {{ old(
                                'shop_type', $shop->shop_type ?? '') === 'Beauty Salon/Tattoo
                                Shop(美容院/纹身店)' ? 'selected' : '' }}>Beauty Salon/Tattoo
                                Shop(美容院/纹身店)</option>
                                <option value="Coffee Shop(咖啡店)" {{ old(
                                'shop_type', $shop->shop_type ?? '') === 'Coffee Shop(咖啡店)' ?
                                'selected' : '' }}>Coffee Shop(咖啡店)</option>
                                <option value="Hospital(医院)" {{ old(
                                'shop_type', $shop->shop_type ?? '') === 'Hospital(医院)' ?
                                'selected' : '' }}>Hospital(医院)</option>
                                <option value="Night Club(夜店)" {{ old(
                                'shop_type', $shop->shop_type ?? '') === 'Night Club(夜店)' ?
                                'selected' : '' }}>Night Club(夜店)</option>
                                <option value="Office Space(写字楼)" {{ old(
                                'shop_type', $shop->shop_type ?? '') === 'Office Space(写字楼)' ?
                                'selected' : '' }}>Office Space(写字楼)</option>
                                <option value="Others(其它)" {{ old(
                                'shop_type', $shop->shop_type ?? '') === 'Others(其它)' ? 'selected'
                                : '' }}>Others(其它)</option>
                                <option value="Public Space(政府机构)" {{ old(
                                'shop_type', $shop->shop_type ?? '') === 'Public Space(政府机构)' ?
                                'selected' : '' }}>Public Space(政府机构)</option>
                                <option value="Restaurant(餐馆)" {{ old(
                                'shop_type', $shop->shop_type ?? '') === 'Restaurant(餐馆)' ?
                                'selected' : '' }}>Restaurant(餐馆)</option>
                                <option value="School(学校)" {{ old(
                                'shop_type', $shop->shop_type ?? '') === 'School(学校)' ? 'selected'
                                : '' }}>School(学校)</option>
                            </select>
                            @error('shop_type')
                            <span class="form-text text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-lg-right">
                            <span class="text-danger">*</span> {{ __('Chia lợi nhuận') }}
                        </label>

                        <div class="col-lg-3">
                            <select name="share_rate_type" id="share_rate_type" class="form-control"
                                    required>
                                <option value="percentage" {{ old(
                                'share_rate_type', $shop->share_rate_type ?? 'percentage') ===
                                'percentage' ? 'selected' : '' }}>
                                Phần trăm (%)
                                </option>
                                <option value="fixed" {{ old(
                                'share_rate_type', $shop->share_rate_type ?? '') === 'fixed' ?
                                'selected' : '' }}>
                                Doanh thu cố định (VNĐ)
                                </option>
                            </select>
                        </div>

                        <div class="col-lg-4">
                            <div class="input-group">
                                <input type="number" step="0.01" required class="form-control"
                                       name="share_rate"
                                       id="share_rate"
                                       value="{{ old('share_rate', $shop->share_rate ?? '') }}">

                                <div class="input-group-append">
                <span class="input-group-text" id="share_rate_unit">
                    {{ old('share_rate_type', $shop->share_rate_type ?? 'percentage') === 'fixed'
                        ? 'VNĐ' : '%' }}
                </span>
                                </div>
                            </div>

                            @error('share_rate')
                            <span class="form-text text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="strategy" class="col-lg-2 col-form-label text-lg-right">
                            {{ __('Chiến lược') }}
                        </label>
                        <div class="col-lg-9">
                            <select name="strategy" id="strategy" class="form-control">
                                <option value="(VND-1h)5-0-0" {{ old(
                                'strategy', $shop->strategy ?? '') === '(VND-1h)5-0-0' ? 'selected'
                                : '' }}>(VND-1h) 5-0-0</option>
                                <option value="(VND-1h)1-5000-5000" {{ old(
                                'strategy', $shop->strategy ?? '') === '(VND-1h)1-5000-5000' ?
                                'selected' : '' }}>(VND-1h) 1-5000-5000</option>
                                <option value="(VND-1h)5-10000-52000" {{ old(
                                'strategy', $shop->strategy ?? '') === '(VND-1h)5-10000-52000' ?
                                'selected' : '' }}>(VND-1h) 5-10000-52000</option>
                                <option value="(VND-1h)20-10000-52000" {{ old(
                                'strategy', $shop->strategy ?? '') === '(VND-1h)20-10000-52000' ?
                                'selected' : '' }}>(VND-1h) 20-10000-52000</option>
                                <option value="(VND-1h)5-7000-52000" {{ old(
                                'strategy', $shop->strategy ?? '') === '(VND-1h)5-7000-52000' ?
                                'selected' : '' }}>(VND-1h) 5-7000-52000</option>
                            </select>
                            @error('strategy')
                            <span class="form-text text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="contract_id" class="col-lg-2 col-form-label text-lg-right">
                            <span class="text-danger">*</span> {{ __('Hợp đồng') }}
                        </label>
                        <div class="col-lg-9">
                            <select name="contract_id" id="contract_id" class="form-control" required>
                                <option value="">-- Chọn hợp đồng --</option>
                                @foreach($contracts as $id => $label)
                                <option value="{{ $id }}" {{ old(
                                'contract_id', $shop->contract_id ?? '') == $id ? 'selected' : ''
                                }}>
                                {{ $label }}
                                </option>
                                @endforeach
                            </select>
                            @error('contract_id')
                            <span class="form-text text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>


                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-lg-right">
                            {{ __('Bind thiết bị') }}
                        </label>
                        <div class="col-lg-9">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_bound"
                                       id="bound_yes" value="1"
                                       @checked(old('is_bound', $shop->is_bound ?? '') == '1')>
                                <label class="form-check-label" for="bound_yes">{{ __('Đã bind')
                                    }}</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_bound"
                                       id="bound_no"  value="0"
                                       @checked(old('is_bound', $shop->is_bound ?? '') == '0')>
                                <label class="form-check-label" for="bound_no">{{ __('Chưa bind')
                                    }}</label>
                            </div>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-lg-right">{{ __('Thiết bị')
                            }}</label>
                        <div class="col-lg-9">
                            <div id="device-container">
                                {{--Thiết bị mẫu sẽ được clone--}}
                                <div class="device-entry d-flex mb-2">
                                    <select name="device_name[]" class="form-control mr-2" required>
                                        <option value="">-- Chọn thiết bị --</option>
                                        <option value="CB8">CB8</option>
                                        <option value="CB8PRO">CB8PRO</option>
                                        <option value="CB32">CB32</option>
                                    </select>

                                    <input type="text" name="device_code[]"
                                           class="form-control mr-2" placeholder="Mã máy" required>

                                    {{-- <input type="number" name="device_quantity[]"
                                                class="form-control mr-2" placeholder="Số lượng"
                                                required>--}}

                                    <input type="number" name="device_pin[]"
                                           class="form-control mr-2" placeholder="Số pin" required>

                                    <button type="button" class="btn btn-danger remove-device">–
                                    </button>
                                </div>

                            </div>

                            <button type="button" class="btn btn-sm btn-primary mt-2"
                                    id="add-device">+ Thêm thiết bị
                            </button>
                        </div>
                    </div>


                    {{--Hidden JSON input--}}
                    <input type="hidden" name="device_json" id="device_json">


                    <x-textarea-field
                        name="note"
                        :label="__('Ghi chú')"
                        :value="$shop->note ?? ''"
                    />

                </fieldset>
            </x-card>

            <div class="d-flex justify-content-center mt-4">
                <a href="{{ route('admin.shops.index') }}" class="btn btn-light">{{ __('Trở lại')
                    }}</a>
                <button type="submit" class="btn btn-primary ml-2">{{ __('Lưu') }}</button>
            </div>
        </div>
    </div>
</form>
@push('css')
<style>
    legend {
        font-size: 1.25rem;
    }

    .form-group .col-form-label {
        font-weight: 500;
    }

    .device-entry {
        gap: 10px;
    }

    .remove-device {
        width: 40px;
        height: 40px;
    }
</style>
<style>
    .select2-container--default .select2-selection--single {
        height: 38px !important;
        padding: 6px 12px;
        font-size: 1rem;
        line-height: 1.5;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 24px !important;
        padding-left: 0 !important;
    }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        // Kích hoạt Select2 cho trường contract_id
        $('#contract_id').select2({
            placeholder: "-- Chọn hợp đồng --",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush
