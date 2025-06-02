<div class="breadcrumb-line breadcrumb-line-light">
    @if (count($breadcrumbs))
        <div class="breadcrumb">
            @foreach ($breadcrumbs as $breadcrumb)
                @if ($breadcrumb->url && !$loop->last)
                    <a href="{{ $breadcrumb->url }}" class="breadcrumb-item">
                        @isset($breadcrumb->icon)
                            <i class="{{ $breadcrumb->icon }} mr-2"></i>
                        @endisset
                        {{ $breadcrumb->title }}
                    </a>
                @else
                    <span class="breadcrumb-item active">
                        @isset($breadcrumb->icon)
                            <i class="{{ $breadcrumb->icon }} mr-2"></i>
                        @endisset{{ $breadcrumb->title }}
                    </span>
                @endif
            @endforeach
        </div>
    @endif
</div>
