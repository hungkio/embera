<div class="list-icons">
    @can('admins.update')
    <a href="{{ route('admin.contracts.edit', $id) }}" class="item-action btn-primary" title="{{ __('Chỉnh sửa') }}">
        <i class="fal fa-pencil-alt"></i>
    </a>
    @endcan
    @can('admins.delete')
    @if($email != config('ecc.admin_email'))
    <form method="POST" action="{{ route('admin.contracts.destroy', $id) }}" style="display:inline-block;">
        @csrf
        @method('DELETE')
        <button class="item-action btn-danger js-delete" title="{{ __('Xóa') }}" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
            <i class="fal fa-trash-alt"></i>
        </button>
    </form>
    @endif
    @endcan
</div>
