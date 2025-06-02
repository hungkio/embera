<select class="form-control w-auto" name="status" id="select_status" data-url="{{ route('admin.banners.change.status', $id) }}">
    <option value="{{ \App\Enums\BannerState::Pending }}" {{ $status == \App\Enums\BannerState::Pending ? 'selected' : '' }}>{{ __('Chờ phê duyệt') }}</option>
    <option value="{{ \App\Enums\BannerState::Active }}" {{ $status == \App\Enums\BannerState::Active ? 'selected' : '' }}>{{ __('Hoạt động') }}</option>
    <option value="{{ \App\Enums\BannerState::Disabled }}" {{ $status == \App\Enums\BannerState::Disabled ? 'selected' : '' }} >{{ __('Hủy') }}</option>
</select>
