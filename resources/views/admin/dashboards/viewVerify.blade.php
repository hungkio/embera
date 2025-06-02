@extends('admin.layouts.master')
@section('page-content')
<form action="{{ route('admin.verify') }}" method="POST">
@csrf
    <input type="text" name="email" placeholder="email">
    <input type="text" name="code" placeholder="code">
    <button type="submit">{{ __('Gửi') }}</button>
</form>
@endsection
