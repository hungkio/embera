<form action="{{ $url }}" method="POST" data-block id="merchant-form" enctype="multipart/form-data">
    @csrf
    @method($method ?? 'POST')

    <div class="d-flex align-items-start flex-column flex-md-row">
        <div class="w-100 order-2 order-md-1 left-content">
            <div class="row">
                <div class="col-md-12">
                    <x-card>
                        <fieldset>
                            <legend class="font-weight-semibold text-uppercase font-size-sm">
                                {{ __('Thông tin Merchant') }}
                            </legend>

                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label text-lg-right">
                                    <span class="text-danger">*</span> Tên đăng nhập
                                </label>
                                <div class="col-lg-9">
                                    <input type="text" name="username" class="form-control"
                                           value="{{ $merchant->username ?? old('username') }}">
                                    @error('username')
                                    <span class="form-text text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label text-lg-right">
                                    <span class="text-danger">*</span> Mật khẩu
                                </label>
                                <div class="col-lg-9">
                                    <input type="text" name="password" class="form-control"
                                           value="{{ old('password') ?? '' }}"
                                           placeholder="">

                                    @if (session()->has('plain_password'))
                                    <small class="form-text text-success">
                                        Mật khẩu vừa tạo: {{ session()->get('plain_password') }}
                                    </small>
                                    @endif

                                    @error('password')
                                    <span class="form-text text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <x-text-field
                                name="email"
                                type="email"
                                :label="__('Email')"
                                :value="$merchant->email ?? old('email')"
                            />

                            <x-text-field
                                name="phone"
                                :label="__('Số điện thoại')"
                                :value="$merchant->phone ?? old('phone')"
                            />

                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label text-lg-right">{{ __('BD') }}</label>
                                <div class="col-lg-9">
                                    <select name="admin_id" id="admin_id" class="form-control">
                                        <option value="" {{ old('admin_id') === '' || !$merchant->admin_id ? 'selected' : '' }}>
                                        {{ __('Chọn BD') }}
                                        </option>
                                        @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}"
                                                {{ ($merchant->admin_id ?? old('admin_id')) == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->first_name }} {{ $employee->last_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </fieldset>
                    </x-card>

                    <div class="d-flex justify-content-center align-items-center action" id="action-form">
                        <a href="{{ route('admin.merchants.index') }}" class="btn btn-light">{{ __('Trở lại') }}</a>
                        <div class="btn-group ml-3">
                            <button class="btn btn-primary btn-block" data-loading>{{ __('Lưu') }}</button>
                            <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"></button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="javascript:void(0)" class="dropdown-item submit-type"
                                   data-redirect="{{ route('admin.merchants.index') }}">{{ __('Lưu và thoát') }}</a>
                                <a href="javascript:void(0)" class="dropdown-item submit-type"
                                   data-redirect="{{ route('admin.merchants.create') }}">{{ __('Lưu và tạo mới') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
