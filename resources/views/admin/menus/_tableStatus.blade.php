<select class="form-control w-auto" name="status" id="select_status" data-url="{{ route('admin.menus.change.status', $id) }}">
    <option value="{{ \App\Domain\Menu\Models\Menu::STATUS_HIDE }}" {{ $status == \App\Domain\Menu\Models\Menu::STATUS_HIDE ? 'selected' : '' }}>{{ __('Ẩn') }}</option>
    <option value="{{ \App\Domain\Menu\Models\Menu::STATUS_SHOW }}" {{ $status == \App\Domain\Menu\Models\Menu::STATUS_SHOW ? 'selected' : '' }} >{{ __('Hiển thị') }}</option>
</select>
