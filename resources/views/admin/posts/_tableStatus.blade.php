<select class="form-control w-auto" name="status" id="select_status" data-url="{{ route('admin.posts.change.status', $id) }}">
    <option value="{{ \App\Enums\PostState::Pending }}" {{ $status == \App\Enums\PostState::Pending ? 'selected' : '' }}>{{ __('Chờ phê duyệt') }}</option>
    <option value="{{ \App\Enums\PostState::Active }}" {{ $status == \App\Enums\PostState::Active ? 'selected' : '' }}>{{ __('Hoạt động') }}</option>
    <option value="{{ \App\Enums\PostState::Disabled }}" {{ $status == \App\Enums\PostState::Disabled ? 'selected' : '' }} >{{ __('Hủy') }}</option>
</select>
