<form action="{{ $url }}" method="POST" data-block enctype="multipart/form-data" id="banner-form">
    @csrf
    @method($method ?? 'POST')
    <div class="d-flex align-items-start flex-column flex-md-row">
        <!-- Left content -->
        <div class="w-100 order-2 order-md-1 left-content">
            <div class="row">
                <div class="col-md-12">
                    <x-card>
                        <fieldset>
                            <legend class="font-weight-semibold text-uppercase font-size-sm">
                                {{ __('Quảng Cáo') }}
                            </legend>
                            <div class="collapse show" id="general">
                                <div class="form-group row">
                                    <label for="image" class="col-lg-2 col-form-label text-right"> <span
                                            class="text-danger">*</span>{{ __("Ảnh") }} :</label>
                                    <div class="col-lg-9">
                                        <div id="thumbnail">
                                            <div class="single-image clearfix">
                                                <div class="image-holder"
                                                     onclick="document.getElementById('image').click();">
                                                    <img id="image_url"
                                                         src="{{ $banner->getFirstMediaUrl('banner') ?? '/backend/global_assets/images/placeholders/placeholder.jpg'}}"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <input type="file" name="image" id="image"
                                               class="form-control inputfile hide"
                                               onchange="document.getElementById('image_url').src = window.URL.createObjectURL(this.files[0])"
                                               accept="image/*"
                                        >
                                        @error('image')
                                        <span class="form-text text-danger">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <x-text-field
                                    name="title"
                                    :placeholder="__('Tiêu đề')"
                                    :label="__('Tiêu đề')"
                                    :value="$banner->title"
                                    required
                                >
                                </x-text-field>
                                <div class="form-group row">
                                    <label for="select-taxon" class="col-lg-2 text-lg-right col-form-label"> <span
                                            class="text-danger">*</span>
                                        {{ __('Trạng thái') }}
                                    </label>
                                    <div class="col-lg-9">
                                        <select class="form-control" name="status">
                                            <option
                                                value="{{ \App\Enums\BannerState::Pending }}" {{ $banner->status == \App\Enums\BannerState::Pending ? 'selected' : '' }}>{{ __('Chờ phê duyệt') }}</option>
                                            <option
                                                value="{{ \App\Enums\BannerState::Active }}" {{ $banner->status == \App\Enums\BannerState::Active ? 'selected' : '' }}>{{ __('Hoạt động') }}</option>
                                            <option
                                                value="{{ \App\Enums\BannerState::Disabled }}" {{ $banner->status == \App\Enums\BannerState::Disabled ? 'selected' : '' }} >{{ __('Hủy') }}</option>
                                        </select>
                                        @error('status')
                                        <span class="form-text text-danger">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <x-text-field
                                    name="link"
                                    :placeholder="__('Đường dẫn')"
                                    :label="__('Đường dẫn')"
                                    :value="$banner->link"
                                    required
                                >
                                </x-text-field>

                                <div class="form-group row">
                                    <label class="col-lg-2 text-lg-right col-form-label">
                                        <span class="text-danger">*</span> {{ __('Phần') }}
                                    </label>
                                    <div class="col-lg-9">
                                        <select class="form-control" name="section">
                                            @foreach(\App\Domain\Banner\Models\Banner::PART as $key => $part)
                                                <option
                                                    value="{{ $key }}" {{ $banner->section == $key ? 'selected' : '' }}>{{ $part }}</option>
                                            @endforeach
                                        </select>
                                        @error('section')
                                        <span class="form-text text-danger">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <x-text-field
                                    name="position"
                                    :placeholder="__('Vị trí')"
                                    :label="__('Vị trí')"
                                    :value="$banner->position"
                                    required
                                >
                                </x-text-field>
                            </div>
                        </fieldset>

                    </x-card>
                    <div class="d-flex justify-content-center align-items-center action" id="action-form">
                        <a href="{{ route('admin.banners.index') }}" class="btn btn-light">{{ __('Trở lại') }}</a>
                        <div class="btn-group ml-3">
                            <button class="btn btn-primary btn-block" data-loading>{{ __('Lưu') }}</button>
                            <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"></button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="javascript:void(0)" class="dropdown-item submit-type"
                                   data-redirect="{{ route('admin.banners.index') }}">{{ __('Lưu và thoát') }}</a>
                                <a href="javascript:void(0)" class="dropdown-item submit-type"
                                   data-redirect="{{ route('admin.banners.create') }}">{{ __('Lưu và tạo mới') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /left content -->
    </div>
</form>
