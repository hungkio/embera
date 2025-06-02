<div class="list-icons">
    <a href="{{ route('admin.log-activities.show', $id) }}" class="item-action btn-primary"
       title="{{ __('Chi tiết') }}"><i class="far fa-eye"></i></a>
    @can('log-activities.destroy')
        <a href="javascript:void(0)" data-url="{{ route('admin.log-activities.destroy', $id) }}"
           class="item-action js-delete btn-danger" title="{{ __('Xóa') }}"><i class="fal fa-trash-alt"></i></a>
    @endcan
</div>
