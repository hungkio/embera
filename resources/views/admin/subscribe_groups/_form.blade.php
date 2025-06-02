<form action="{{ $url }}" method="POST" data-block enctype="multipart/form-data" id="subscribe_group-form">
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
                                {{ __('Group') }}
                            </legend>
                            <div class="collapse show" id="general">
                                <x-text-field
                                    name="name"
                                    :placeholder="__('Tên group')"
                                    :label="__('Tên group')"
                                    :value="$subscribe_group->name"
                                    required
                                >
                                </x-text-field>
                                <div class="form-group row">
                                    <label for="select-taxon" class="col-lg-2 text-lg-right col-form-label"> <span
                                            class="text-danger">*</span>
                                        {{ __('Email đăng ký') }}
                                    </label>
                                    <div class="col-lg-9">
                                        <select class="form-control select2" name="email_ids[]" multiple>
                                            @foreach($emails as $email)
                                            <option
                                                value="{{ $email->id }}" @if(in_array($email->id, $subscribe_group->email_ids ? json_decode($subscribe_group->email_ids) : [])) selected @endif>{{ $email->email }}</option>
                                            @endforeach
                                        </select>
                                        @error('email_ids')
                                        <span class="form-text text-danger">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                    </x-card>
                    <div class="d-flex justify-content-center align-items-center action" id="action-form">
                        <a href="{{ route('admin.subs_group.index') }}" class="btn btn-light">{{ __('Trở lại') }}</a>
                        <div class="btn-group ml-3">
                            <button class="btn btn-primary btn-block" data-loading>{{ __('Lưu') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /left content -->
    </div>
</form>
