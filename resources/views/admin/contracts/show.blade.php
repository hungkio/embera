@extends('admin.layouts.master')

@section('title', __('Chi tiết hợp đồng'))

@section('page-header')
<x-page-header>
    {{ Breadcrumbs::render('admin.contracts.show', $contract) }}
</x-page-header>
@stop

@section('page-content')
<x-card title="{{ __('Chi tiết hợp đồng') }} #{{ $contract->contract_number }}">
    <div class="p-4">
        <!-- Thông tin hợp đồng -->
        <h5 class="font-weight-bold text-primary border-bottom pb-2 mb-4">
            {{ __('Thông tin hợp đồng') }}
        </h5>
        <div class="row">
            <div class="col-md-6 form-group mb-3">
                <label class="font-weight-semibold text-muted">{{ __('Mã hợp đồng') }}</label>
                <p class="form-control-plaintext">{{ $contract->contract_number ?? '-' }}</p>
            </div>
            <div class="col-md-6 form-group mb-3">
                <label class="font-weight-semibold text-muted">{{ __('Tiêu đề') }}</label>
                <p class="form-control-plaintext">{{ $contract->title ?? '-' }}</p>
            </div>
            <div class="col-md-6 form-group mb-3">
                <label class="font-weight-semibold text-muted">{{ __('Ngày ký') }}</label>
                <p class="form-control-plaintext">
                    {{ optional($contract->sign_date)->format('d/m/Y') ?? '-' }}
                </p>
            </div>
            <div class="col-md-6 form-group mb-3">
                <label class="font-weight-semibold text-muted">{{ __('Thời hạn') }}</label>
                <p class="form-control-plaintext">
                    {{ $contract->sign_date && $contract->expired_date
                        ? $contract->sign_date->diffInMonths($contract->expired_date) . ' tháng'
                        : '-' }}
                </p>
            </div>
            <div class="col-md-6 form-group mb-3">
                <label class="font-weight-semibold text-muted">{{ __('Ngày hết hạn') }}</label>
                <p class="form-control-plaintext">
                    {{ optional($contract->expired_date)->format('d/m/Y') ?? '-' }}
                </p>
            </div>
            <div class="col-md-6 form-group mb-3">
                <label class="font-weight-semibold text-muted">{{ __('Địa điểm') }}</label>
                <p class="form-control-plaintext">{{ $contract->location ?? '-' }}</p>
            </div>
            <div class="col-md-6 form-group mb-3">
                <label class="font-weight-semibold text-muted">{{ __('Trạng thái') }}</label>
                <p class="form-control-plaintext">
                    <span class="badge {{ $contract->status == \App\Models\Contract::SIGN ? 'badge-success' : ($contract->status == \App\Models\Contract::NOT_SIGN ? 'badge-warning' : 'badge-info') }}">
                        {{ \App\Models\Contract::STATUS[$contract->status] ?? 'Không xác định' }}
                    </span>
                </p>
            </div>
        </div>

        <!-- Thông tin khách hàng -->
        <h5 class="font-weight-bold text-primary border-bottom pb-2 mb-4 mt-5">
            {{ __('Thông tin khách hàng') }}
        </h5>
        <div class="row">
            <div class="col-md-6 form-group mb-3">
                <label class="font-weight-semibold text-muted">{{ __('Merchant') }}</label>
                <p class="form-control-plaintext">{{ $contract->merchant->username ?? '-' }}</p>
            </div>
            <div class="col-md-6 form-group mb-3">
                <label class="font-weight-semibold text-muted">{{ __('Tên khách hàng') }}</label>
                <p class="form-control-plaintext">{{ $contract->customer_name ?? '-' }}</p>
            </div>
            <div class="col-md-6 form-group mb-3">
                <label class="font-weight-semibold text-muted">{{ __('Chức vụ khách hàng') }}</label>
                <p class="form-control-plaintext">{{ $contract->customer_position ?? '-' }}</p>
            </div>
            <div class="col-md-6 form-group mb-3">
                <label class="font-weight-semibold text-muted">{{ __('CCCD khách hàng') }}</label>
                <p class="form-control-plaintext">{{ $contract->customer_cccd ?? '-' }}</p>
            </div>
            <div class="col-md-6 form-group mb-3">
                <label class="font-weight-semibold text-muted">{{ __('Số điện thoại (Zalo)') }}</label>
                <p class="form-control-plaintext">{{ $contract->phone ?? '-' }}</p>
            </div>
            <div class="col-md-6 form-group mb-3">
                <label class="font-weight-semibold text-muted">{{ __('Email') }}</label>
                <p class="form-control-plaintext">{{ $contract->email ?? '-' }}</p>
            </div>
        </div>

        <!-- Thông tin ngân hàng -->
        <h5 class="font-weight-bold text-primary border-bottom pb-2 mb-4 mt-5">
            {{ __('Thông tin ngân hàng') }}
        </h5>
        <div class="row align-items-start">
            <div class="col-md-4 col-sm-12 form-group mb-3">
                <label class="font-weight-semibold text-muted">{{ __('Ngân hàng') }}</label>
                <p class="form-control-plaintext">{{ $contract->bank_info ?? '-' }}</p>
            </div>
            <div class="col-md-4 col-sm-12 form-group mb-3">
                <label class="font-weight-semibold text-muted">{{ __('Số tài khoản') }}</label>
                <p class="form-control-plaintext">{{ $contract->bank_account_number ?? '-' }}</p>
            </div>
            <div class="col-md-4 col-sm-12 form-group mb-3">
                <label class="font-weight-semibold text-muted">{{ __('Tên chủ tài khoản') }}</label>
                <p class="form-control-plaintext">{{ $contract->bank_account_name ?? '-' }}</p>
            </div>
        </div>

        <!-- Thông tin bổ sung -->
        <h5 class="font-weight-bold text-primary border-bottom pb-2 mb-4 mt-5">
            {{ __('Thông tin bổ sung') }}
        </h5>
        <div class="row">
            <div class="col-md-6 form-group mb-3">
                <label class="font-weight-semibold text-muted">{{ __('Giám đốc ký') }}</label>
                <p class="form-control-plaintext">{{ $contract->ceo_sign ?? '-' }}</p>
            </div>
            <div class="col-md-6 form-group mb-3">
                <label class="font-weight-semibold text-muted">{{ __('File hợp đồng') }}</label>
                <p class="form-control-plaintext">
                    @if($contract->upload)
                    <a href="{{ asset('storage/' . $contract->upload) }}" target="_blank" class="text-primary">
                        <i class="fal fa-file-pdf mr-1"></i> {{ basename($contract->upload) }}
                    </a>
                    @else
                    <span class="text-muted">—</span>
                    @endif
                </p>
            </div>
            <div class="col-md-12 form-group mb-3">
                <label class="font-weight-semibold text-muted">{{ __('Ghi chú') }}</label>
                <p class="form-control-plaintext">{{ $contract->note ?? '-' }}</p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="text-center mt-5">
            <a href="{{ route('admin.contracts.edit', $contract->id) }}" class="btn btn-primary mr-2">
                <i class="fal fa-edit mr-1"></i> {{ __('Chỉnh sửa') }}
            </a>
            <a href="{{ route('admin.contracts.index') }}" class="btn btn-light">
                <i class="fal fa-arrow-left mr-1"></i> {{ __('Quay lại danh sách') }}
            </a>
        </div>
    </div>
</x-card>
@stop

@push('css')
<style>
    .form-group label {
        font-size: 0.9rem;
        color: #6c757d;
    }
    .form-control-plaintext {
        font-size: 1rem;
        color: #333;
    }
    .badge-success {
        background-color: #28a745;
    }
    .badge-warning {
        background-color: #ffc107;
    }
    .badge-info {
        background-color: #17a2b8;
    }
    .card {
        border-radius: 0.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    h5.text-primary {
        font-size: 1.2rem;
    }
    @media (max-width: 767px) {
        .form-group {
            margin-bottom: 1.5rem;
        }
    }
</style>
@endpush
