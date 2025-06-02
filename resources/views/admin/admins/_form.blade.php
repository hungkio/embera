<form action="{{ $url }}" method="POST" data-block>
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
                                {{ __('Chung') }}
                            </legend>
                            <div class="collapse show" id="general">
                                <x-text-field
                                    name="first_name"
                                    :placeholder="__('Nguyễn')"
                                    :label="__('Họ')"
                                    :value="$admin->first_name"
                                    required
                                >
                                </x-text-field>

                                <x-text-field
                                    name="last_name"
                                    :placeholder="__('Văn Huy')"
                                    :label="__('Tên')"
                                    :value="$admin->last_name"
                                    required
                                >
                                </x-text-field>
                                <x-text-field
                                    name="email"
                                    :placeholder="__('nguyenhuy@gmail.com')"
                                    :label="__('Email')"
                                    type="email"
                                    :value="$admin->email"
                                    required
                                >
                                </x-text-field>

                                <x-select-field
                                    name="roles"
                                    :placeholder="__('Chọn quyền')"
                                    :options="$roles->pluck('display_name', 'id')"
                                    :label="__('Quyền')"
                                    :value="optional($admin->roles->first())->id"
                                    required
                                >

                                </x-select-field>
                            </div>
                        </fieldset>

                        <fieldset>
                            <legend class="font-weight-semibold text-uppercase font-size-sm">
                                {{ __('Mật khẩu') }}
                            </legend>
                            <div class="collapse show" id="password">
                                <x-text-field
                                    name="password"
                                    placeholder="********"
                                    :label="__('Nhập mật khẩu')"
                                    type="password"
                                    required
                                >
                                </x-text-field>

                                <x-text-field
                                    name="password_confirmation"
                                    placeholder="********"
                                    :label="__('Nhập lại mật khẩu')"
                                    type="password"
                                    required
                                >
                                </x-text-field>
                            </div>
                        </fieldset>

                    </x-card>
                    <div class="d-flex justify-content-center align-items-center action" id="action-form">
                        <a href="{{ route('admin.admins.index') }}" class="btn btn-light"><i
                                class="fal fa-arrow-left mr-2"></i>{{ __('Trở lại') }}</a>
                        <div class="btn-group ml-3">
                            @if($admin->id)
                                <button class="btn btn-primary btn-block" data-loading><i
                                        class="fal fa-check mr-2"></i>{{ __('Cập nhật') }}</button>
                            @else
                                <button class="btn btn-primary btn-block" data-loading><i
                                        class="fal fa-check mr-2"></i>{{ __('Lưu') }}</button>
                            @endif
                            <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"></button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="javascript:void(0)" class="dropdown-item submit-type"
                                   data-redirect="{{ route('admin.admins.index') }}">{{ __('Lưu và thoát') }}</a>
                                <a href="javascript:void(0)" class="dropdown-item submit-type"
                                   data-redirect="{{ route('admin.admins.create') }}">{{ __('Lưu và thêm mới') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- /left content -->
    </div>
</form>
