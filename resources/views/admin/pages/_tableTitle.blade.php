<div class="d-flex align-items-center">
    <div>
        <a href="{{ $page->url() }}" data-toggle="tooltip" data-html="true"  title="{{ $page->title }}" class="text-primary font-weight-semibold" target="_blank">{{ \Str::limit($page->title, 20) }}</a>
    </div>
</div>
