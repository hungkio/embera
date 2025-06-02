@extends('admin.layouts.master')

@section('title', __('Chỉnh sửa :model', ['model' => $taxon->name]))

@section('page-header')
    <x-page-header>
        <x-slot name='title'>
            <h4><i class="icon-pencil7 mr-2"></i> <span class="font-weight-semibold">{{ __('Chỉnh sửa :model', ['model' => $taxon->name]) }}</span></h4>
        </x-slot>
        {{ Breadcrumbs::render() }}
    </x-page-header>
@stop

@section('page-content')
    <!-- Inner container -->
    <form action="{{ route('admin.taxons.update', $taxon) }}" method="POST" enctype="multipart/form-data" data-block>
        @csrf
        @method('PUT')
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
                                        name="name"
                                        :label="__('Tên')"
                                        :value="$taxon->name"
                                        required
                                    >
                                    </x-text-field>

                                    <x-text-field
                                        name="slug"
                                        :label="__('Slug')"
                                        :value="$taxon->slug"
                                        required
                                    >
                                    </x-text-field>
                                    <x-textarea-field
                                        name="description"
                                        :label="__('Mô tả')"
                                        :value="$taxon->description"
                                    >
                                    </x-textarea-field>
                                </div>
                            </fieldset>

                            <fieldset>
                                <legend class="font-weight-semibold text-uppercase font-size-sm">
                                    {{ __('Ảnh') }}
                                </legend>
                                <div class="form-group row">
                                    <label for="image" class="col-lg-2 col-form-label text-right">{{ __('Icon') }}</label>
                                    <div class="col-lg-8">
                                        <div id="thumbnail">
                                            <div class="single-image clearfix">
                                                <div class="image-holder" onclick="document.getElementById('image').click();">
                                                    <img id="image_url" src="{{ $taxon->getFirstMediaUrl('icon') ?? '/backend/global_assets/images/placeholders/placeholder.jpg'}}" />
                                                    <input type="file" name="icon" id="image"
                                                           class="form-control inputfile hide"
                                                           accept="image/*"
                                                           onchange="document.getElementById('image_url').src = window.URL.createObjectURL(this.files[0])">
                                                </div>
                                            </div>
                                        </div>
                                        @error('icon')
                                            <span class="form-text text-danger">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset>
                                <legend class="font-weight-semibold text-uppercase font-size-sm">
                                    {{ __('Seo') }}
                                </legend>

                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                      <a class="nav-link active" aria-current="page" data-toggle="tab" href="#en">Tiếng Anh</a>
                                    </li>
                                    <li class="nav-item">
                                      <a class="nav-link"  data-toggle="tab" href="#vi">Tiếng Việt</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div id="en" class="tab-pane fade in active show">
                                        <x-textarea-field name="content" :label="__('Nội dung trên')" :value="isset($taxon) ? $taxon->content : ''" :placeholder="__('Nội dung')" > </x-textarea-field>
                                        <x-textarea-field name="content_bottom" :label="__('Nội dung dưới')" :value="isset($taxon) ? $taxon->content_bottom : ''" :placeholder="__('Nội dung')" > </x-textarea-field>
                                        <x-text-field name="meta_title" :label="__('Tiêu đề')" type="text" :value="$taxon->meta_title" :placeholder="__('Tiêu đề nên nhập từ 10 đến 70 ký tự trở lên')" > </x-text-field>

                                        <x-text-field name="meta_description" :label="__('Mô tả')" type="text" :value="$taxon->meta_description" :placeholder="__('Mô tả nên nhập từ 160 đến 255 ký tự trở lên')" > </x-text-field>

                                        <x-text-field name="meta_keywords" :label="__('Từ khóa')" type="text" :value="$taxon->meta_keywords" :placeholder="__('Từ khóa nên nhập 12 ký tự trong 1 từ khóa, cách nhau bằng dấu \',\'')" > </x-text-field>
                                    </div>
                                    <div id="vi" class="tab-pane">
                                        <x-textarea-field name="content_vi" :label="__('Nội dung trên')" :value="isset($taxon) ? $taxon->content_vi : ''" :placeholder="__('Nội dung')" > </x-textarea-field>
                                        <x-textarea-field name="content_bottom_vi" :label="__('Nội dung dưới')" :value="isset($taxon) ? $taxon->content_bottom_vi : ''" :placeholder="__('Nội dung')" > </x-textarea-field>
                                        <x-text-field name="meta_title_vi" :label="__('Tiêu đề')" type="text" :value="$taxon->meta_title_vi" :placeholder="__('Tiêu đề nên nhập từ 10 đến 70 ký tự trở lên')" > </x-text-field>

                                        <x-text-field name="meta_description_vi" :label="__('Mô tả')" type="text" :value="$taxon->meta_description_vi" :placeholder="__('Mô tả nên nhập từ 160 đến 255 ký tự trở lên')" > </x-text-field>

                                        <x-text-field name="meta_keywords_vi" :label="__('Từ khóa')" type="text" :value="$taxon->meta_keywords_vi" :placeholder="__('Từ khóa nên nhập 12 ký tự trong 1 từ khóa, cách nhau bằng dấu \',\'')" > </x-text-field>
                                    </div>
                                </div>
                            </fieldset>
                        </x-card>
                        <div class="d-flex justify-content-center align-items-center action" id="action-form">
                            <a href="{{ route('admin.taxonomies.edit', $taxon->taxonomy_id) }}" class="btn btn-light"><i class="fal fa-arrow-left mr-2"></i>{{ __('Trở lại') }}</a>
                            <div class="btn-group ml-3">
                                <button class="btn btn-primary" data-loading><i class="fal fa-check mr-2"></i>{{ __('Lưu') }}</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- /left content -->
        </div>
        <!-- /inner container -->
    </form>

@stop
