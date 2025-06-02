<div class="list-icons">
    <a href="{{ route('admin.banners.edit', $id) }}" class="item-action btn-primary" title="{{ __('Chỉnh sửa') }}"><i class="fal fa-pencil-alt"></i></a>
    <a href="javascript:void(0)" data-url="{{ route('admin.banners.destroy', $id) }}" class="item-action js-delete btn-danger" title="{{ __('Xóa') }}"><i class="fal fa-trash-alt"></i></a>
</div>
