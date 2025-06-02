<div class="d-flex align-items-center">
    <div class="mr-3">
        <a href="{{ $post->url() }}" target="_blank">
            <img src="{{ $post->getFirstMediaUrl('image') }}" class="rounded-circle" width="50" height="50" alt="">
        </a>
    </div>
    <div>
        <a href="{{ $post->url() }}" data-toggle="tooltip" data-html="true"  title="{{ $post->title }}" class="text-default font-weight-semibold" target="_blank">{{ \Str::limit($post->title, 20) }}</a>
    </div>
</div>
