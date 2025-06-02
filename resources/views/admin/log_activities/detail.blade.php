@extends('admin.layouts.master')

@section('title', __('Chỉnh sửa :model', ['model' => $activity->log_name]))
@section('page-header')
    <x-page-header>
        {{ Breadcrumbs::render('admin.log-activities.show', $activity) }}
    </x-page-header>
@stop

@section('page-content')
    @include('admin.log_activities._form', [
        'page' => $activity ?? new \Spatie\Activitylog\Models\Activity,
    ])
@stop

@push('js')
    <script src="{{ asset('backend/js/editor-admin.js') }}"></script>
    <script>
        $('.form-check-input-styled').uniform();
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/min/dropzone.min.js" integrity="sha512-9WciDs0XP20sojTJ9E7mChDXy6pcO0qHpwbEJID1YVavz2H6QBz5eLoDD8lseZOb2yGT8xDNIV7HIe1ZbuiDWg==" crossorigin="anonymous"></script>
@endpush
