@extends('admin.layouts.master')

@section('title', __('Chỉnh sửa PIN'))

@section('page-header')
    <x-page-header>
        {{ Breadcrumbs::render('admin.pins.edit', $pin) }}
    </x-page-header>
@stop

@section('page-content')
    @include('admin.pins._form', [
        'url' => route('admin.pins.update', $pin),
        'pin' => $pin,
        'method' => 'PUT',
    ])
@stop

@push('js')
    {!! JsValidator::formRequest('App\Http\Requests\Admin\PinUpdateRequest', '#pin-form'); !!}
    <script>
        $('.select2').select2({
            placeholder: "{{ __('-- Vui lòng chọn --') }}",
        });
    </script>
@endpush
