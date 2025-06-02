@extends('admin.layouts.master')

@section('title', __('Chỉnh sửa :model', ['model' => $admin->email]))
@section('page-header')
    <x-page-header>
        {{ Breadcrumbs::render() }}
    </x-page-header>
@stop

@section('page-content')
    @include('admin.admins._form', [
        'url' =>  route('admin.admins.update', $admin),
        'admin' => $admin ?? new \App\Domain\Admin\Models\Admin,
        'method' => 'PUT'
    ])
@stop

@push('js')
    <script>
        $(function () {
            setTimeout(function() { $('#first_name').focus() }, 2000);
            $('#roles').change(function () {
                if($(this).val() && $('#roles-error').text() != '') {
                    $('#roles-error').text('')
                }
            })
        })
    </script>
    {!! JsValidator::formRequest('App\Http\Requests\Admin\AdminRequest', 'form'); !!}
@endpush
