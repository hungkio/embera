<div class="list-icons">
{{--    @can('shops.update')--}}
    <a href="{{ route('admin.shops.edit', $id) }}" class="item-action btn-primary" title="{{ __('Chỉnh sửa') }}">
        <i class="fal fa-pencil-alt"></i>
    </a>
{{--    @endcan--}}

{{--    @can('shops.delete')--}}
    <a href="javascript:void(0)"
       data-url="{{ route('admin.shops.destroy', $id) }}"
       class="item-action js-delete btn-danger"
       title="{{ __('Xóa') }}">
        <i class="fal fa-trash-alt"></i>
    </a>
{{--    @endcan--}}

    <a href="{{ route('admin.shops.bbnt.preview', $id) }}"
       class="item-action btn-secondary"
       title="Tạo BBNT">
        <i class="fal fa-file-word"></i> BBNT
    </a>
    {{--    @endcan--}}
</div>
