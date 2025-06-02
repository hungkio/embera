<div class="d-flex align-items-center">
    <div class="mr-3">
        <a href="javascript:void(0)">
            <img src="{{ $banner->getFirstMediaUrl('banner') }}" class="rounded-circle" width="50" height="50" alt="">
        </a>
    </div>
    <div>
        <a href="javascript:void(0)" data-toggle="tooltip" data-html="true" title="{{ $banner->title }}" class="text-default font-weight-semibold">{{ \Str::limit($banner->title, 20) }}</a>
    </div>
</div>
