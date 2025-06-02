<select class="form-control w-auto" name="status" id="select_status" data-url="{{ route('admin.pages.change.status', $id) }}">
    <option value="{{ \App\Enums\PageState::Pending }}" {{ $status == \App\Enums\PageState::Pending ? 'selected' : '' }}>{{ __('Chờ phê duyệt') }}</option>
    <option value="{{ \App\Enums\PageState::Active }}" {{ $status == \App\Enums\PageState::Active ? 'selected' : '' }}>{{ __('Hoạt động') }}</option>
    <option value="{{ \App\Enums\PageState::Disabled }}" {{ $status == \App\Enums\PageState::Disabled ? 'selected' : '' }} >{{ __('Hủy') }}</option>
</select>
