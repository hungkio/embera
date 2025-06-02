<form action="{{ $url }}" method="POST" data-block enctype="multipart/form-data" id="post-form" >
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
                                        @if($post->id)
                                            Chỉnh sửa bài viết: <a href="{{ $post->url() }}" class="text-primary font-weight-semibold" target="_blank">{{ Str::limit($post->title, 20) }}</a>
                                        @else
                                        Thêm bài viết mới
                                        @endif
                                    </legend>
                                    <div class="collapse show" id="general">

                                        <ul class="nav nav-tabs">
                                            <li class="nav-item">
                                                <a class="nav-link active"  data-toggle="tab" href="#en">Tiếng Anh</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" aria-current="page" data-toggle="tab" href="#vi">Tiếng Việt</a>
                                            </li>
                                        </ul>
                                        <h5>{{ __('Nội dung') }}</h5>
                                        <hr>
                                        <div class="tab-content">
                                            <div id="vi" class="tab-pane ">
                                                <x-text-field name="title_vi" :placeholder="__('Tiêu đề ')" :label="__('Tiêu đề')" :value="$post->title_vi" required></x-text-field>
                                                <x-text-field name="description_vi" :placeholder="__('Mô tả')" :label="__('Mô tả')" :value="$post->description_vi" required>
                                                    {!! $post->description_vi ?? null !!}
                                                </x-text-field>
                                                <x-textarea-field name="body_vi" :placeholder="__('Nội dung')" :label="__('Nội dung')" :value="$post->body_vi" class="noidung" required></x-textarea-field>
                                                <h5>{{ __('SEO') }}</h5>
                                                <hr>
                                                <div class="collapse show" id="seo">
                                                    <x-text-field
                                                        name="meta_title_vi"
                                                        :label="__('Tiêu đề')"
                                                        type="text"
                                                        :value="$post->meta_title_vi"
                                                        :placeholder="__('Tiêu đề nên nhập từ 10 đến 70 ký tự trở lên')"
                                                    >
                                                    </x-text-field>

                                                    <x-text-field
                                                        name="meta_description_vi"
                                                        :label="__('Mô tả')"
                                                        type="text"
                                                        :value="$post->meta_description_vi"
                                                        :placeholder="__('Mô tả nên nhập từ 160 đến 255 ký tự trở lên')"
                                                    >
                                                    </x-text-field>

                                                    <x-text-field
                                                        name="meta_keywords_vi"
                                                        :label="__('Từ khóa')"
                                                        type="text"
                                                        :value="$post->meta_keywords_vi"
                                                        :placeholder="__('Từ khóa nên nhập 12 ký tự trong 1 từ khóa, cách nhau bằng dấu \',\'')"
                                                    >
                                                    </x-text-field>
                                                </div>
                                            </div>
                                            <div id="en" class="tab-pane fade in active show">
                                                <x-text-field name="title" :placeholder="__('Tiêu đề ')" :label="__('Tiêu đề')" :value="$post->title" required></x-text-field>
                                                <x-text-field name="description" :placeholder="__('Mô tả')" :label="__('Mô tả')" :value="$post->description" required>
                                                    {!! $post->description ?? null !!}
                                                </x-text-field>
                                                <x-textarea-field name="body" :placeholder="__('Nội dung')" :label="__('Nội dung')" :value="$post->body" class="noidung" required></x-textarea-field>
                                                <h5>{{ __('SEO') }}</h5>
                                                <hr>
                                                <div class="collapse show" id="seo">
                                                    <x-text-field
                                                        name="meta_title"
                                                        :label="__('Tiêu đề')"
                                                        type="text"
                                                        :value="$post->meta_title"
                                                        :placeholder="__('Tiêu đề nên nhập từ 10 đến 70 ký tự trở lên')"
                                                    >
                                                    </x-text-field>

                                                    <x-text-field
                                                        name="meta_description"
                                                        :label="__('Mô tả')"
                                                        type="text"
                                                        :value="$post->meta_description"
                                                        :placeholder="__('Mô tả nên nhập từ 160 đến 255 ký tự trở lên')"
                                                    >
                                                    </x-text-field>

                                                    <x-text-field
                                                        name="meta_keywords"
                                                        :label="__('Từ khóa')"
                                                        type="text"
                                                        :value="$post->meta_keywords"
                                                        :placeholder="__('Từ khóa nên nhập 12 ký tự trong 1 từ khóa, cách nhau bằng dấu \',\'')"
                                                    >
                                                    </x-text-field>

                                                </div>
                                            </div>
                                        </div>
                                        <h5>{{ __('Thông tin chung') }}</h5>
                                        <hr>
                                        <div class="form-group row">
                                            <label class="col-lg-2 col-form-label text-lg-right"><span class="text-danger">*</span> {{ __('Ảnh') }}:</label>
                                            <div class="col-lg-9">
                                                <div id="thumbnail">
                                                    <div class="single-image">
                                                        <div class="image-holder" onclick="document.getElementById('image').click();">
                                                            <img id="image_url" width="170" height="170" src="{{ $post->getFirstMediaUrl('image') ?? '/backend/global_assets/images/placeholders/placeholder.jpg'}}" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="file" name="image" id="image"
                                                    class="form-control inputfile hide"
                                                    onchange="document.getElementById('image_url').src = window.URL.createObjectURL(this.files[0])">
                                                @error('image')
                                                <span class="form-text text-danger">
                                                            {{ $message }}
                                                        </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="select-taxon" class="col-lg-2 text-lg-right col-form-label">
                                                <span class="text-danger">*</span> {{ __('Danh mục') }}
                                            </label>
                                            <div class="col-lg-9" id="select2">
                                                <select name="category[]" class="form-control select2" data-width="100%"
                                                        multiple>
                                                    <option value="">
                                                        {{ __('Chọn danh mục') }}
                                                    </option>
                                                    @foreach($taxons as $taxon)
                                                        <option value="{{ $taxon->id }}"
                                                                @if(in_array($taxon->id, $post->taxons->pluck('id')->toArray())) selected @endif>
                                                            {{ $taxon->selectText() }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="clearfix"></div>
                                                @error('category')
                                                    <span class="form-text text-danger">
                                                        {{ $message }}
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-2 col-form-label text-lg-right col-form-label">
                                                {{ __('Hiển thị trên trang:') }}
                                            </label>
                                            <div class="col-lg-9">
                                                <select id="on_pages" name="on_pages[]" class="form-control select2"
                                                        multiple>
                                                    @foreach($pagesOptions as $pageOption)
                                                        <option
                                                            value="{{ $pageOption->slug }}" {{ \in_array($pageOption->slug, $selectedPages) ? 'selected' : null }}>
                                                            {{ $pageOption->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-2 col-form-label text-lg-right col-form-label">
                                                {{ __('Bài viết liên quan:') }}
                                            </label>
                                            <div class="col-lg-9">
                                                <select id="related_posts" name="related_posts[]" class="form-control select2"
                                                        multiple>
                                                    @foreach($relatedPosts as $relatedPost)
                                                        <option
                                                            value="{{ $relatedPost->id }}" {{ \in_array($relatedPost->id, $selectedRelatePost) ? 'selected' : null }}>
                                                            {{ $relatedPost->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        {{-- tag --}}
                                        <div class="form-group row">
                                            <label class="col-lg-2 col-form-label text-lg-right col-form-label">
                                                {{ __('Tags:') }}
                                            </label>
                                            <div class="col-lg-9">
                                                <input type="text" value="@if ($post->tags) {{implode(',',$post->tags)}} @endif" name="tags" data-role="tagsinput">
                                            </div>
                                        </div>
                                        {{-- end tag --}}
                                        <div class="form-group row">
                                            <label for="select-taxon" class="col-lg-2 text-lg-right col-form-label">
                                                <span class="text-danger">*</span> {{ __('Trạng thái') }}:
                                            </label>
                                            <div class="col-lg-9">
                                                <select class="form-control" name="status">
                                                    <option
                                                        value="{{ \App\Enums\PostState::Pending }}" {{ $post->status == \App\Enums\PostState::Pending ? 'selected' : '' }}>{{ __('Chờ phê duyệt') }}</option>
                                                    <option
                                                        value="{{ \App\Enums\PostState::Active }}" {{ $post->status == \App\Enums\PostState::Active ? 'selected' : '' }}>{{ __('Hoạt động') }}</option>
                                                    <option
                                                        value="{{ \App\Enums\PostState::Disabled }}" {{ $post->status == \App\Enums\PostState::Disabled ? 'selected' : '' }} >{{ __('Hủy') }}</option>
                                                </select>
                                                @error('status')
                                                    <span class="form-text text-danger">
                                                        {{ $message }}
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <x-text-field name="slug" :label="__('Đường dẫn')" type="text" :value="$post->slug" :placeholder="__('Đường dẫn sẽ hiển thị trên URL của trang web')" > </x-text-field>
                                    </div>
                                </fieldset>

                    </x-card>
                    <div class="d-flex justify-content-center align-items-center action-div" id="action-form">
                        <a href="{{ route('admin.posts.index') }}" class="btn btn-light">{{ __('Trở lại') }}</a>
                        <div class="btn-group ml-3">
                            <button class="btn btn-primary btn-block" data-loading><i
                                    class="mi-save mr-2"></i>{{ __('Lưu') }}</button>
                            <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"></button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="javascript:void(0)" class="dropdown-item submit-type"
                                   data-redirect="{{ route('admin.posts.index') }}">{{ __('Lưu & Thoát') }}</a>
                                <a href="javascript:void(0)" class="dropdown-item submit-type"
                                   data-redirect="{{ route('admin.posts.create') }}">{{ __('Lưu & Thêm mới') }}</a>
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
    <script>
        $("#post-form").bind("keypress", function (e) {
            if (e.keyCode == 13) {
                return false;
            }
        });
        $(document).on('keyup', '#slug', function () {
            let slug = $('#slug').val()
            let fullLink = '{{route('post.show')}}' +'/'+ slug;
            $('#slug-value').html(fullLink);
        })
        /*let count = $('.tags input').length;
        $('#add-tag').on('click', function () {
            const inputElelemt = "<input class='form-control' type='text' name='tags["+count+"]' />"
            $('.tags').append(inputElelemt);
            count++;
        })
        $('#select-tags').on('change', function () {
            const thisValue = $(this).val();
            const inputElelemt = "<input class='form-control' type='text' name='tags["+count+"]' value="+thisValue+" />"
            $('.tags').append(inputElelemt);
            count++;
        })*/
    </script>
@endpush()
