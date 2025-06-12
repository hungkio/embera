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
                                :value="$contract->contract_number"
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

                            <x-select-field
                                name="status"
                                :label="__('Trạng thái')"
                                :options="['pending' => 'Chờ duyệt', 'active' => 'Hoạt động', 'disabled' => 'Hủy']"
                                :value="$contract->status"
                                required
                            />

                            <x-text-field
                                name="expired_time"
                                :label="__('Thời hạn')"
                                :value="$contract->expired_time"
                            />

                            <x-text-field
                                name="bank_info"
                                :label="__('Ngân hàng')"
                                :value="$contract->bank_info"
                            />

                            <x-text-field
                                name="email"
                                type="email"
                                :label="__('Email')"
                                :value="$contract->email"
                            />

                            <x-text-field
                                name="phone"
                                :label="__('Số điện thoại')"
                                :value="$contract->phone"
                            />

                            <x-text-field
                                name="title"
                                :label="__('Tiêu đề')"
                                :value="$contract->title"
                            />

                            <x-text-field
                                name="ceo_sign"
                                :label="__('Giám đốc ký')"
                                :value="$contract->ceo_sign"
                            />

                            <x-text-field
                                name="location"
                                :label="__('Địa điểm')"
                                :value="$contract->location"
                            />

                            <x-textarea-field
                                name="note"
                                :label="__('Ghi chú')"
                                :value="$contract->note"
                            />

                            <div class="form-group row">
                                <label class="col-lg-2 text-lg-right col-form-label">
                                    {{ __('Tập tin PDF') }}
                                </label>
                                <div class="col-lg-9">
                                    <input type="file" name="upload" class="form-control" accept="application/pdf">
                                    @error('upload')
                                    <span class="form-text text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                        </fieldset>
                    </x-card>

                    <div class="d-flex justify-content-center align-items-center action" id="action-form">
                        <a href="{{ route('admin.contracts.index') }}" class="btn btn-light">{{ __('Trở lại') }}</a>
                        <div class="btn-group ml-3">
                            <button class="btn btn-primary btn-block" data-loading>{{ __('Lưu') }}</button>
                            <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"></button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="javascript:void(0)" class="dropdown-item submit-type"
                                   data-redirect="{{ route('admin.contracts.index') }}">{{ __('Lưu và thoát') }}</a>
                                <a href="javascript:void(0)" class="dropdown-item submit-type"
                                   data-redirect="{{ route('admin.contracts.create') }}">{{ __('Lưu và tạo mới') }}</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>
