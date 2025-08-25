<form action="{{ $url }}" method="POST" data-block id="pin-form" enctype="multipart/form-data">
    @csrf
    @method($method ?? 'POST')

    <div class="d-flex align-items-start flex-column flex-md-row">
        <div class="w-100 order-2 order-md-1 left-content">
            <div class="row">
                <div class="col-md-12">
                    <x-card class="shadow-sm">
                        <fieldset class="p-4">
                            <legend class="border-bottom pb-2 mb-4 font-weight-bold text-primary">
                                {{ __('Thông tin PIN') }}
                            </legend>

                            <div class="row">
                                <div class="col-md-6">
                                    <x-text-field
                                        name="imei"
                                        :label="__('IMEI')"
                                        :value="old('imei', $pin->imei ?? '')"
                                        required
                                    />
                                </div>
                                <div class="col-md-6">
                                    <x-text-field
                                        name="serial_number"
                                        :label="__('Serial Number')"
                                        :value="old('serial_number', $pin->serial_number ?? '')"
                                        required
                                    />
                                </div>
                            </div>
                        </fieldset>
                    </x-card>

                    <div class="d-flex justify-content-center align-items-center action mt-4" id="action-form">
                        <a href="{{ route('admin.pins.index') }}" class="btn btn-light mr-3">
                            {{ __('Trở lại') }}
                        </a>
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary btn-block" data-loading>
                                <span class="default-text"><i class="fal fa-save mr-1"></i> {{ __('Lưu') }}</span>
                                <span class="loading-text"><i class="fal fa-spinner fa-spin mr-1"></i> {{ __('Đang lưu...') }}</span>
                            </button>
                            <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"></button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="javascript:void(0)" class="dropdown-item submit-type"
                                   data-redirect="{{ route('admin.pins.index') }}">
                                    {{ __('Lưu và thoát') }}
                                </a>
                                <a href="javascript:void(0)" class="dropdown-item submit-type"
                                   data-redirect="{{ route('admin.pins.create') }}">
                                    {{ __('Lưu và tạo mới') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('css')
<style>
    button[data-loading] .loading-text { display: none; }
    button:disabled { opacity: 0.7; cursor: not-allowed; }
    legend.mt-4 { margin-top: 1.5rem; border-bottom: 1px solid #e0e0e0; padding-bottom: 0.5rem; }
    .btn-group .dropdown-toggle { border-left: 1px solid rgba(255, 255, 255, 0.2); }
    legend { font-size: 1.25rem; }
    .form-group .col-form-label { font-weight: 500; }
</style>
@endpush
