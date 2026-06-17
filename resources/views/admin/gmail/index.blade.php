@extends('admin.layouts.master')
@section('title', __('Hộp thư Gmail'))

@section('page-header')
    <x-page-header>
        <div class="d-sm-flex align-items-center justify-content-between">
            <div class="p-2">
                <h4 class="mb-1">{{ __('Hộp thư Gmail') }}</h4>
                <p class="text-muted mb-0">{{ __('Kết nối Gmail cá nhân để đọc và đồng bộ email vào trang quản trị.') }}</p>
            </div>
        </div>
    </x-page-header>
@stop

@section('page-content')
    <div class="row">
        <div class="col-lg-4">
            <x-card>
                <h5 class="mb-3">{{ __('Kết nối Gmail') }}</h5>

                @if(!$isConfigured)
                    <div class="alert alert-warning mb-0">
                        {{ __('Thiếu GMAIL_CLIENT_ID, GMAIL_CLIENT_SECRET hoặc GMAIL_REDIRECT_URI trong file .env.') }}
                    </div>
                @elseif(!$account)
                    <p class="text-muted">{{ __('Chưa có tài khoản Gmail nào được kết nối cho tài khoản admin này.') }}</p>
                    <a href="{{ route('admin.gmail.connect') }}" class="btn btn-primary">
                        {{ __('Kết nối Gmail') }}
                    </a>
                @else
                    <dl class="mb-3">
                        <dt>{{ __('Email') }}</dt>
                        <dd>{{ $account->email }}</dd>
                        <dt>{{ __('Hết hạn access token') }}</dt>
                        <dd>{{ optional($account->expires_at)->format('d/m/Y H:i') ?: __('Chưa có') }}</dd>
                        <dt>{{ __('Số mail đã lưu') }}</dt>
                        <dd>{{ $messages?->total() ?? 0 }}</dd>
                    </dl>

                    <div class="d-flex flex-wrap align-items-center" style="gap: 12px;">
                        <form method="POST" action="{{ route('admin.gmail.sync') }}" class="mb-1">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm">{{ __('Đồng bộ inbox') }}</button>
                        </form>


                        <form method="POST" action="{{ route('admin.gmail.import-daily-orders-today') }}" class="d-inline-flex align-items-center mb-1" style="gap: 6px;" onsubmit="return confirm('{{ __('Xác nhận import dữ liệu daily orders?') }}')">
                            @csrf
                            <select name="date" class="form-control form-control-sm" style="width: auto;">
                                <option value="{{ now('Asia/Ho_Chi_Minh')->toDateString() }}">{{ __('Hôm nay') }} ({{ now('Asia/Ho_Chi_Minh')->format('d/m') }})</option>
                                <option value="{{ now('Asia/Ho_Chi_Minh')->subDay()->toDateString() }}" selected>{{ __('Hôm qua') }} ({{ now('Asia/Ho_Chi_Minh')->subDay()->format('d/m') }})</option>
                                <option value="{{ now('Asia/Ho_Chi_Minh')->subDays(2)->toDateString() }}">{{ __('2 ngày trước') }} ({{ now('Asia/Ho_Chi_Minh')->subDays(2)->format('d/m') }})</option>
                            </select>
                            <button type="submit" class="btn btn-warning btn-sm">{{ __('Lấy daily orders') }}</button>
                        </form>

                        <form method="POST" action="{{ route('admin.gmail.disconnect') }}" class="mb-1" onsubmit="return confirm('{{ __('Bạn chắc chắn muốn ngắt kết nối Gmail?') }}')">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm">{{ __('Ngắt kết nối') }}</button>
                        </form>
                    </div>
                @endif
            </x-card>
        </div>

        <div class="col-lg-8">
            <x-card>
                <h5 class="mb-3">{{ __('Inbox') }}</h5>

                @if(!$account)
                    <p class="text-muted mb-0">{{ __('Kết nối Gmail trước để xem email.') }}</p>
                @elseif(!$messages || $messages->isEmpty())
                    <p class="text-muted mb-0">{{ __('Chưa có email nào được đồng bộ.') }}</p>
                @else
                    <div class="table-responsive mb-3">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>{{ __('Người gửi') }}</th>
                                <th>{{ __('Tiêu đề') }}</th>
                                <th>{{ __('Nhận lúc') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($messages as $message)
                                <tr class="{{ $selectedMessage && $selectedMessage->id === $message->id ? 'table-primary' : '' }}">
                                    <td>
                                        <a href="{{ route('admin.gmail.index', ['message' => $message->id]) }}">
                                            {{ $message->from_name ?: $message->from_email ?: __('Không rõ') }}
                                        </a>
                                        @if($message->is_unread)
                                            <span class="badge badge-info ml-2">{{ __('Mới') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $message->subject ?: __('(Không có tiêu đề)') }}</td>
                                    <td>{{ optional($message->received_at)->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $messages->withQueryString()->links() }}

                    @if($selectedMessage)
                        <hr>
                        <h5 class="mb-2">{{ $selectedMessage->subject ?: __('(Không có tiêu đề)') }}</h5>
                        <p class="text-muted mb-3">
                            {{ __('Từ') }}: {{ $selectedMessage->from_name ?: $selectedMessage->from_email }}
                            @if($selectedMessage->from_email && $selectedMessage->from_name && $selectedMessage->from_email !== $selectedMessage->from_name)
                                &lt;{{ $selectedMessage->from_email }}&gt;
                            @endif
                        </p>

                        <pre class="border rounded p-3 mb-0" style="white-space: pre-wrap;">{{ $selectedMessage->body_text ?: strip_tags($selectedMessage->body_html ?? '') ?: $selectedMessage->snippet }}</pre>
                    @endif
                @endif
            </x-card>

        </div>
    </div>
@stop
