    <form action="{{ $url }}" method="POST" data-block id="contract-form" enctype="multipart/form-data">
        @csrf
        @method($method ?? 'POST')

        <div class="d-flex align-items-start flex-column flex-md-row">
            <div class="w-100 order-2 order-md-1 left-content">
                <div class="row">
                    <div class="col-md-12">
                        <x-card>
                            <fieldset>
                                <legend class="font-weight-semibold text-uppercase font-size-sm">
                                    {{ __('Thông tin hợp đồng') }}
                                </legend>

                                <x-text-field
                                    name="contract_number"
                                    :label="__('Mã hợp đồng')"
                                    :value="$contract->contract_number ?? ''"
                                    readonly
                                />

                                <x-text-field
                                name="title"
                                :label="__('Tiêu đề')"
                                :value="$contract->title"
                                required
                                />

                                <x-text-field
                                    name="sign_date"
                                    type="date"
                                    :label="__('Ngày ký')"
                                    :value="optional($contract->sign_date)->format('Y-m-d')"
                                    required
                                />

                                <x-text-field
                                    name="expired_date"
                                    type="date"
                                    :label="__('Ngày hết hạn')"
                                    :value="optional($contract->expired_date)->format('Y-m-d')"
                                    required
                                />

                                <x-text-field
                                    name="expired_time"
                                    :label="__('Thời hạn')"
                                    :value="$contract->expired_time"
                                    readonly
                                />

                                <x-text-field
                                    name="location"
                                    :label="__('Địa điểm')"
                                    :value="$contract->location"
                                    required
                                />

                                <div class="form-group row">
                                    <label for="status" class="col-lg-2 col-form-label text-lg-right">
                                        <span class="text-danger">*</span> {{ __('Trạng thái') }}
                                    </label>
                                    <div class="col-lg-9">
                                        <select name="status" id="status" class="form-control" required>
                                            <option value="">-- Trạng thái --</option>
                                            <option value="đã_ký" {{ old(
                                            'status', $contract->status ?? '') === \App\Models\Contract::SIGN ? 'selected'
                                            : '' }}>Đã ký</option>
                                            <option value="chưa_ký" {{ old(
                                            'status', $contract->status ?? '') === \App\Models\Contract::NOT_SIGN ?
                                            'selected' : '' }}>Chưa ký</option>
                                            <option value="chỉ_có_BBNT" {{ old(
                                            'status', $contract->status ?? '') === \App\Models\Contract::BBNT ?
                                            'selected' : '' }}>Chỉ có BBNT</option>
                                        </select>
                                        @error('status')
                                        <span class="form-text text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label text-lg-right">
                                        <span class="text-danger">*</span> {{ __('Chọn Merchant') }}
                                    </label>
                                    <div class="col-lg-9">
                                        <select name="merchant_id" class="form-control select2" required>
                                            <option value="">{{ __('-- Chọn merchant --') }}</option>
                                            @foreach($merchants as $id => $username)
                                            <option value="{{ $id }}" {{ old('merchant_id', $contract->merchant_id ?? '') == $id ? 'selected' : '' }}>
                                            {{ $username }}
                                            </option>
                                            @endforeach
                                        </select>

                                        @error('merchant_id')
                                        <span class="form-text text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <x-text-field
                                    name="customer_name"
                                    :label="__('Tên khách hàng')"
                                    :value="old('customer_name', $contract->customer_name)"
                                    required
                                />

                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label text-lg-right">
                                        {{ __('Ngân hàng') }}
                                    </label>
                                    <div class="col-lg-3">
                                        <input type="text" name="bank_info" class="form-control"
                                               placeholder="Tên ngân hàng"
                                               value="{{ old('bank_info', $contract->bank_info ?? '') }}"
                                               required>
                                        @error('bank_info')
                                        <span class="form-text text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-lg-3">
                                        <input type="text" name="bank_account_number" class="form-control" placeholder="Số tài khoản"
                                               value="{{ old('bank_account_number', $contract->bank_account_number ?? '') }}" required>
                                        @error('bank_account_number')
                                        <span class="form-text text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-lg-3">
                                        <input type="text" name="bank_account_name" class="form-control" placeholder="Tên chủ tài khoản"
                                               value="{{ old('bank_account_name', $contract->bank_account_name ?? '') }}" required>
                                        @error('bank_account_name')
                                        <span class="form-text text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <x-text-field
                                    name="phone"
                                    :label="__('Số điện thoại (Zalo)')"
                                    :value="$contract->phone"
                                />

                                <x-text-field
                                    name="customer_cccd"
                                    :label="__('CCCD khách hàng')"
                                    :value="old('customer_cccd', $contract->customer_cccd)"
                                    required
                                />

                                <x-text-field
                                    name="customer_position"
                                    :label="__('Chức vụ khách hàng')"
                                    :value="old('customer_position', $contract->customer_position)"
                                    required
                                />

                                <x-text-field
                                    name="ceo_sign"
                                    :label="__('Giám đốc ký')"
                                    :value="$contract->ceo_sign ?? App\Models\Contract::CURRENT_CEO"
                                    required
                                />

                                <x-text-field
                                    name="email"
                                    type="email"
                                    :label="__('Email')"
                                    :value="$contract->email"
                                />

                                <x-textarea-field
                                    name="note"
                                    :label="__('Ghi chú')"
                                    :value="$contract->note ?? ''"
                                />

                                <div class="form-group row">
                                    <label for="upload" class="col-lg-2 col-form-label text-right">
                                        <span class="text-danger">*</span>{{ __("File hợp đồng") }} :
                                    </label>

                                    <div class="col-lg-9">
                                        @if($contract->upload ?? false)
                                            <p>
                                                📄 <strong>File hiện tại:</strong>
                                                <a href="{{ asset('storage/' . $contract->upload) }}" target="_blank">
                                                    {{ basename($contract->upload) }}
                                                </a>
                                            </p>
                                        @endif

                                        <input type="file" name="upload" id="upload"
                                               class="form-control inputfile"
                                               accept=".pdf">

                                        <small class="form-text text-muted">Chỉ chấp nhận file PDF. Nếu bạn chọn file mới, file cũ sẽ được thay thế.</small>

                                        @error('upload')
                                        <span class="form-text text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>


                            </fieldset>
                        </x-card>

                        <div class="d-flex justify-content-center align-items-center action"
                             id="action-form">
                            <a href="{{ route('admin.contracts.index') }}" class="btn btn-light">{{ __(
                                    'Trở lại') }}</a>
                            <div class="btn-group ml-3">
                                <button class="btn btn-primary btn-block" data-loading>{{ __('Lưu')}}
                                </button>
                                <button class="btn btn-primary dropdown-toggle"
                                        data-toggle="dropdown"></button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="javascript:void(0)" class="dropdown-item submit-type"
                                       data-redirect="{{ route('admin.contracts.index') }}">{{ __(
                                            'Lưu và thoát') }}</a>
                                    <a href="javascript:void(0)" class="dropdown-item submit-type"
                                       data-redirect="{{ route('admin.contracts.create') }}">{{ __(
                                            'Lưu và tạo mới') }}</a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
