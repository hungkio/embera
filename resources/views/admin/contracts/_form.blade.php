<form action="{{ $url }}" method="POST" data-block id="contract-form" enctype="multipart/form-data">
    @csrf
    @method($method ?? 'POST')

    <div class="d-flex align-items-start flex-column flex-md-row">
        <div class="w-100 order-2 order-md-1 left-content">
            <div class="row">
                <div class="col-md-12">
                    <x-card class="shadow-sm">
                        <fieldset class="p-4">
                            <!-- Section 1: Contract Information -->
                            <legend class="border-bottom pb-2 mb-4 font-weight-bold text-primary">
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
                                id="sign_date"
                                required
                            />

                            <x-text-field
                                name="expired_time"
                                type="number"
                                :label="__('Thời hạn (tháng)')"
                                :value="$contract->expired_time ? preg_replace('/[^0-9]/', '', $contract->expired_time) : ''"
                                id="expired_time"
                                required
                            />

                            <x-text-field
                                name="expired_date"
                                type="date"
                                :label="__('Ngày hết hạn')"
                                :value="optional($contract->expired_date)->format('Y-m-d')"
                                id="expired_date"
                            />

                            <x-text-field
                                name="location"
                                :label="__('Địa điểm')"
                                :value="$contract->location"
                                required
                            />

                            <div class="form-group row">
                                <label for="city" class="col-lg-2 col-form-label text-lg-right">
                                    <span class="text-danger">*</span> {{ __('Tỉnh/TP') }}
                                </label>
                                <div class="col-lg-9">
                                    <select name="city" id="city" class="form-control" required>
                                        <option value="">-- {{ __('Chọn tỉnh/TP') }} --</option>
                                        @foreach(\App\Models\Contract::provinces() as $code => $name)
                                        <option value="{{ $code }}"
                                                {{ old('city', $contract->city) == $code ? 'selected' : '' }}>
                                        {{ $name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('city')
                                    <span class="form-text text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="status" class="col-lg-2 col-form-label text-lg-right">
                                    <span class="text-danger">*</span> {{ __('Trạng thái') }}
                                </label>
                                <div class="col-lg-9">
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="">-- {{ __('Trạng thái') }} --</option>
                                        <option value="2" {{ old('status', $contract->status ?? '') == 2 ? 'selected' : '' }}>
                                        {{ __('Đã ký') }}
                                        </option>
                                        <option value="1" {{ old('status', $contract->status ?? '') == 1 ? 'selected' : '' }}>
                                        {{ __('Chưa ký') }}
                                        </option>
                                        <option value="0" {{ old('status', $contract->status ?? '') == 0 ? 'selected' : '' }}>
                                        {{ __('Chỉ có BBNT') }}
                                        </option>
                                    </select>
                                    @error('status')
                                    <span class="form-text text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Section 2: Customer Information -->
                            <legend class="font-weight-semibold text-uppercase font-size-sm mt-4">
                                {{ __('Thông tin khách hàng') }}
                            </legend>

                            <div class="form-group row">
                                <label for="merchant_id"
                                       class="col-lg-2 col-form-label text-lg-right">
                                    <span class="text-danger">*</span> {{ __('Chọn Merchant') }}
                                </label>
                                <div class="col-lg-9">
                                    <select name="merchant_id" id="merchant_id"
                                            class="form-control select2" required>
                                        <option value="">{{ __('-- Chọn merchant --') }}</option>
                                        @foreach($merchants as $id => $username)
                                        <option value="{{ $id }}" {{ old(
                                        'merchant_id', $contract->merchant_id ?? '') == $id ?
                                        'selected' : '' }}>
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

                            <x-text-field
                                name="customer_position"
                                :label="__('Chức vụ khách hàng')"
                                :value="old('customer_position', $contract->customer_position)"
                                required
                            />

                            <x-text-field
                                name="customer_cccd"
                                :label="__('CCCD khách hàng')"
                                :value="old('customer_cccd', $contract->customer_cccd)"
                                required
                            />

                            <x-text-field
                                name="phone"
                                :label="__('Số điện thoại (Zalo)')"
                                :value="$contract->phone"
                            />

                            <x-text-field
                                name="email"
                                type="email"
                                :label="__('Email')"
                                :value="$contract->email"
                            />

                            <!-- Section 3: Bank Information -->
                            <legend class="font-weight-semibold text-uppercase font-size-sm mt-4">
                                {{ __('Thông tin ngân hàng') }}
                            </legend>

                            <div class="form-group row">
                                <label for="bank_info"
                                       class="col-lg-2 col-form-label text-lg-right">
                                    <span class="text-danger">*</span> {{ __('Ngân hàng') }}
                                </label>
                                <div class="col-lg-3">
                                    <input type="text" name="bank_info" id="bank_info"
                                           class="form-control"
                                           placeholder="{{ __('Tên ngân hàng') }}"
                                           value="{{ old('bank_info', $contract->bank_info ?? '') }}"
                                           required>
                                    @error('bank_info')
                                    <span class="form-text text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-lg-3">
                                    <input type="text" name="bank_account_number"
                                           id="bank_account_number" class="form-control"
                                           placeholder="{{ __('Số tài khoản') }}"
                                           value="{{ old('bank_account_number', $contract->bank_account_number ?? '') }}"
                                           required>
                                    @error('bank_account_number')
                                    <span class="form-text text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-lg-3">
                                    <input type="text" name="bank_account_name"
                                           id="bank_account_name" class="form-control"
                                           placeholder="{{ __('Tên chủ tài khoản') }}"
                                           value="{{ old('bank_account_name', $contract->bank_account_name ?? '') }}"
                                           required>
                                    @error('bank_account_name')
                                    <span class="form-text text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Section 4: Additional Information -->
                            <legend class="font-weight-semibold text-uppercase font-size-sm mt-4">
                                {{ __('Thông tin bổ sung') }}
                            </legend>

                            <x-text-field
                                name="ceo_sign"
                                :label="__('Giám đốc ký')"
                                :value="$contract->ceo_sign ?? \App\Models\Contract::CURRENT_CEO"
                                required
                            />

                            <x-textarea-field
                                name="note"
                                :label="__('Ghi chú')"
                                :value="$contract->note ?? ''"
                            />

                            <div class="form-group row">
                                <label for="upload" class="col-lg-2 col-form-label text-lg-right">
                                    <span class="text-danger">*</span> {{ __('File hợp đồng') }}
                                </label>
                                <div class="col-lg-9">
                                    @if($contract->upload ?? false)
                                    <p>
                                        📄 <strong>{{ __('File hiện tại') }}:</strong>
                                        <a href="{{ asset('storage/' . $contract->upload) }}"
                                           target="_blank">
                                            {{ basename($contract->upload) }}
                                        </a>
                                    </p>
                                    @endif

                                    <input type="file" name="upload" id="upload"
                                           class="form-control inputfile"
                                           accept=".pdf">
                                    <small class="form-text text-muted">
                                        {{ __(
                                            'Chỉ chấp nhận file PDF. Nếu bạn chọn file mới, file cũ sẽ được thay thế.')
                                        }}
                                    </small>
                                    @error('upload')
                                    <span class="form-text text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </fieldset>
                    </x-card>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-center align-items-center action mt-4"
                         id="action-form">
                        <a href="{{ route('admin.contracts.index') }}" class="btn btn-light mr-3">
                            {{ __('Trở lại') }}
                        </a>
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary btn-block" data-loading>
                                <span class="default-text"><i class="fal fa-save mr-1"></i> {{ __(
                                        'Lưu') }}</span>
                                <span class="loading-text"><i
                                        class="fal fa-spinner fa-spin mr-1"></i> {{ __(
                                        'Đang lưu...') }}</span>
                            </button>
                            <button class="btn btn-primary dropdown-toggle"
                                    data-toggle="dropdown"></button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="javascript:void(0)" class="dropdown-item submit-type"
                                   data-redirect="{{ route('admin.contracts.index') }}">
                                    {{ __('Lưu và thoát') }}
                                </a>
                                <a href="javascript:void(0)" class="dropdown-item submit-type"
                                   data-redirect="{{ route('admin.contracts.create') }}">
                                    {{ __('Lưu và tạo mới') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</form>

@push('js')
<script>
    // Initialize Select2 for merchant_id
    $(document).ready(function () {
        $('.select2').select2();
    });

    // Calculate expired date based on sign date and expired time
    function calculateExpiredDate() {
        const signDate = document.getElementById('sign_date').value;
        const months = parseInt(document.getElementById('expired_time').value);

        if (!signDate || isNaN(months)) {
            document.getElementById('expired_date').value = '';
            return;
        }

        const date = new Date(signDate);
        date.setMonth(date.getMonth() + months);

        const yyyy = date.getFullYear();
        const mm = String(date.getMonth() + 1).padStart(2, '0');
        const dd = String(date.getDate()).padStart(2, '0');

        document.getElementById('expired_date').value = `${yyyy}-${mm}-${dd}`;
    }

    document.getElementById('sign_date').addEventListener('change', calculateExpiredDate);
    document.getElementById('expired_time').addEventListener('input', calculateExpiredDate);

    document.addEventListener('DOMContentLoaded', function () {
        calculateExpiredDate();
    });
</script>
@endpush

@push('css')
<style>
    /* Ensure loading text is hidden by default */
    button[data-loading] .loading-text {
        display: none;
    }

    /* Style disabled button */
    button:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    /* Ensure consistent spacing for form sections */
    legend.mt-4 {
        margin-top: 1.5rem;
        border-bottom: 1px solid #e0e0e0;
        padding-bottom: 0.5rem;
    }

    /* Align dropdown button with main button */
    .btn-group .dropdown-toggle {
        border-left: 1px solid rgba(255, 255, 255, 0.2);
    }
</style>
<style>
    legend {
        font-size: 1.25rem;
    }

    .form-group .col-form-label {
        font-weight: 500;
    }
</style>
@endpush

