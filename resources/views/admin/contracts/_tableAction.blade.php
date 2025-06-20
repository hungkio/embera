<div class="list-icons">
{{--    @can('admins.update')--}}
    <a href="{{ route('admin.contracts.edit', ['contract' => $id]) }}"
       class="item-action btn-primary"
       title="{{ __('Chỉnh sửa') }}">
        <i class="fal fa-pencil-alt"></i>
    </a>
{{--    @endcan--}}

{{--    @can('admins.delete')--}}
    <a href="javascript:void(0)"
       data-url="{{ route('admin.contracts.destroy', ['contract' => $id]) }}"
       class="item-action js-delete btn-danger"
       title="{{ __('Xóa') }}">
        <i class="fal fa-trash-alt"></i>
    </a>
{{--    @endcan--}}

{{--    @can('contracts.send')--}}
    <a href="{{ route('admin.contracts.sendEmail', ['id' => $id]) }}"
       class="item-action btn-info"
       title="Gửi email hợp đồng"
       onclick="return confirm('Bạn có chắc muốn gửi email hợp đồng này không?')">
        <i class="fal fa-envelope"></i>
    </a>
{{--    @endcan--}}
</div>
