<form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3 mb-4 align-items-end filters">
    <div class="col-md-6">
        <label for="date_range">Khoảng ngày thuê <span class="text-danger">*</span></label>
        <input type="text" name="date_range" id="date_range" class="form-control"
               value="{{ request('date_range') }}" required autocomplete="off" />
    </div>

    <div class="col-md-3">
        <label for="staff">Nhân viên</label>
        <select name="staff" id="staff" class="form-select select2">
            <option value="">-- Tất cả --</option>
            @foreach($staffList as $staff)
                <option value="{{ trim($staff) }}" {{ (request('staff') == trim($staff)) ? 'selected' : '' }}>
                    {{ trim($staff) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label for="shop_type">Loại cửa hàng</label>
        <select name="shop_type" id="shop_type" class="form-select select2">
            <option value="">-- Tất cả --</option>
            @foreach($shopTypeList as $type)
                <option value="{{ $type }}" {{ (request('shop_type') == $type) ? 'selected' : '' }}>
                    {{ $type }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label for="shop_name">Cửa hàng</label>
        <select name="shop_name" id="shop_name" class="form-select select2">
            <option value="">-- Tất cả --</option>
            @foreach($shopNameList as $shop)
                <option value="{{ $shop }}" {{ (request('shop_name') == $shop) ? 'selected' : '' }}>
                    {{ $shop }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label for="region">Miền</label>
        <select name="region" id="region" class="form-select select2">
            <option value="">-- Tất cả --</option>
            @foreach($regionList as $region)
                <option value="{{ $region }}" {{ (request('region') == $region) ? 'selected' : '' }}>
                    {{ $region }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label for="city">Thành phố</label>
        <select name="city" id="city" class="form-select select2">
            <option value="">-- Tất cả --</option>
            @foreach($cityList as $city)
                <option value="{{ $city }}" {{ (request('city') == $city) ? 'selected' : '' }}>
                    {{ $city }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label for="city">Khu vực</label>
        <select name="area" id="area" class="form-select select2">
            <option value="">-- Tất cả --</option>
            @foreach($areaList as $area)
                <option value="{{ $area }}" {{ (request('area') == $area) ? 'selected' : '' }}>
                    {{ $area }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-12 text-end mt-3">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-funnel-fill"></i> Lọc dữ liệu
        </button>
    </div>
</form>
