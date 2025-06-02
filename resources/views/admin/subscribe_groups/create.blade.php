@extends('admin.layouts.master')

@section('title', __('Tạo Group'))
@section('page-header')
    <x-page-header>
        {{ Breadcrumbs::render() }}
    </x-page-header>
@stop

@section('page-content')
    @include('admin.subscribe_groups._form', [
        'url' =>  route('admin.subs_group.store'),
        'subscribe_group' => new \App\Models\SubscribeGroup,
    ])
@stop

@push('js')
    <script>
        $('.form-check-input-styled').uniform();
        $('.select2').select2({
            placeholder: "{{ __('-- Vui lòng chọn --') }}",
        });
    </script>
    {!! JsValidator::formRequest('App\Http\Requests\Admin\SubscribeGroupStoreRequest', '#subscribe_group-form'); !!}
@endpush
