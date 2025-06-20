<div class="list-icons">
{{--    @can('admins.update')--}}
    <a href="{{ route('admin.merchants.edit', $id) }}" class="item-action btn-primary" title="{{ __('Chỉnh sửa') }}">
        <i class="fal fa-pencil-alt"></i>
    </a>
{{--    @endcan--}}

{{--    @can('admins.delete')--}}
    <a href="javascript:void(0)"
       data-url="{{ route('admin.merchants.destroy', $id) }}"
       class="item-action js-delete btn-danger"
       title="{{ __('Xóa') }}">
        <i class="fal fa-trash-alt"></i>
    </a>
{{--    @endcan--}}
</div>
