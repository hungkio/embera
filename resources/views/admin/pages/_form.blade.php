<form action="{{ $url }}" method="POST" data-block id="page">
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
                                {{ __('Trang') }}
                                @if($page->id)
                                    | <a href="{{ $page->url() }}" class="text-primary font-weight-semibold"
                                         target="_blank">{{ Str::limit($page->title, 20) }}</a>
                                @endif
                            </legend>
                            <div class="collapse show" id="general">
                                <x-text-field
                                    name="title"
                                    :placeholder="__('Tiêu đề')"
                                    :label="__('Tiêu đề')"
                                    :value="$page->title"
                                    required
                                >
                                </x-text-field>
                                <x-textarea-field
                                    name="body"
                                    :placeholder="__('Nội dung')"
                                    :label="__('Nội dung')"
                                    :value="$page->body"
                                    required
                                    class="wysiwyg"
                                >
                                </x-textarea-field>
                                <div class="form-group row">
                                    <label for="select-taxon" class="col-lg-2 text-lg-right col-form-label">
                                         {{ __('Trạng thái') }}:
                                    </label>
                                    <div class="col-lg-9">
                                        <select class="form-control" name="status">
                                            <option value="{{ \App\Enums\PageState::Pending }}" {{ $page->status == \App\Enums\PageState::Pending ? 'selected' : '' }}>{{ __('Chờ phê duyệt') }}</option>
                                            <option value="{{ \App\Enums\PageState::Active }}" {{ $page->status == \App\Enums\PageState::Active ? 'selected' : '' }}>{{ __('Hoạt động') }}</option>
                                            <option value="{{ \App\Enums\PageState::Disabled }}" {{ $page->status == \App\Enums\PageState::Disabled ? 'selected' : '' }} >{{ __('Hủy') }}</option>
                                        </select>
                                        @error('status')
                                        <span class="form-text text-danger">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <legend class="font-weight-semibold text-uppercase font-size-sm">
                                {{ __('SEO') }}
                            </legend>
                            <div class="collapse show" id="seo">
                                <x-text-field
                                    name="meta_title"
                                    :label="__('Tiêu đề')"
                                    type="text"
                                    :value="$page->meta_title"
                                    :placeholder="__('Tiêu đề nên nhập từ 10 đến 70 ký tự trở lên')"
                                >
                                </x-text-field>

                                <x-text-field
                                    name="meta_description"
                                    :label="__('Mô tả')"
                                    type="text"
                                    :value="$page->meta_description"
                                    :placeholder="__('Mô tả nên nhập từ 160 đến 255 ký tự')"
                                >
                                </x-text-field>

                                <x-text-field
                                    name="meta_keywords"
                                    :label="__('Từ khóa')"
                                    type="text"
                                    :value="$page->meta_keywords"
                                    :placeholder="__('Từ khóa nên nhập 12 ký tự trong 1 từ khóa, cách nhau bằng dấu \',\'')"
                                >
                                </x-text-field>

                                <x-text-field
                                    name="slug"
                                    :label="__('Đường dẫn')"
                                    type="text"
                                    :value="$page->slug"
                                    :placeholder="__('Đường dẫn sẽ hiển thị trên URL của trang web')"
                                >
                                </x-text-field>

                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label text-lg-right">
                                    {{ __('Đường dẫn thực tế') }}:
                                </label>
                                <div class="col-lg-9">
                                    <div style="line-height: 36px;">
                                        <span id="slug-value" class="text-primary">
                                         @if($page->id)
                                                {{route('page.show', $page->slug)}}
                                            @else
                                                {{route('page.show')}}/
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </x-card>
                    <div class="d-flex justify-content-center align-items-center action-div" id="action-form">
                        <a href="{{ route('admin.pages.index') }}" class="btn btn-light">{{ __('Hủy') }}</a>
                        <div class="btn-group ml-3">
                            <button class="btn btn-primary btn-block" data-loading><i class="mi-save mr-2"></i>{{ __('Lưu') }}</button>
                            <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"></button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="javascript:void(0)" class="dropdown-item submit-type" data-redirect="{{ route('admin.pages.index') }}">{{ __('Lưu & Thoát') }}</a>
                                <a href="javascript:void(0)" class="dropdown-item submit-type" data-redirect="{{ route('admin.pages.create') }}">{{ __('Lưu & Thêm mới') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /left content -->
    </div>
</form>

@push('js')
{!! JsValidator::formRequest('App\Http\Requests\Admin\PageRequest', '#page'); !!}
<script>
    $(document).on('keyup', '#slug', function () {
        let slug = $('#slug').val()
        let fullLink = '{{route('page.show')}}' +'/'+ slug;
        $('#slug-value').html(fullLink);
    })
</script>
@endpush()
