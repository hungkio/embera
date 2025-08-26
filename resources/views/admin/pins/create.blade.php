@extends('admin.layouts.master')

@section('title', __('Tạo PIN'))

@section('page-header')
    <x-page-header>
        {{ Breadcrumbs::render('admin.pins.create') }}
    </x-page-header>
@stop

@section('page-content')
    @include('admin.pins._form', [
        'url' => route('admin.pins.store'),
        'pin' => new \App\Models\Pin(),
    ])
@stop

@push('js')
    {!! JsValidator::formRequest('App\Http\Requests\Admin\PinStoreRequest', '#pin-form'); !!}
    <script>
        $('.select2').select2({
            placeholder: "{{ __('-- Vui lòng chọn --') }}",
        });
    </script>
@endpush
