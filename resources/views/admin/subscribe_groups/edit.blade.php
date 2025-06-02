@extends('admin.layouts.master')

@section('title', __('Chỉnh sửa :model', ['model' => $sub_group->name]))
@section('page-header')
    <x-page-header>
        {{ Breadcrumbs::render('admin.subs_group.edit', $sub_group) }}
    </x-page-header>
@stop

@section('page-content')
    @include('admin.subscribe_groups._form', [
        'url' =>  route('admin.subs_group.update', $sub_group),
        'subscribe_group' => $sub_group ?? new \App\Models\SubscribeGroup(),
        'method' => 'PUT'
    ])
@stop

@push('js')
    <script>
        $('.form-check-input-styled').uniform();
        $('.select2').select2({
            placeholder: "{{ __('-- Vui lòng chọn --') }}",
        });
    </script>
    {!! JsValidator::formRequest('App\Http\Requests\Admin\SubscribeGroupUpdateRequest', '#subscribe_group-form'); !!}
@endpush
