<div class="card" @isset($id) id="{{ $id }}" @endisset>
    @isset($title)
    <div class="card-header header-elements-inline">
        <h5 class="card-title">{{ $title ?? null }}</h5>
        <div class="header-elements">
            <div class="list-icons">
                <a class="list-icons-item" data-action="collapse"></a>
            </div>
        </div>
    </div>
    @endisset

    <div class="card-body">
        {{ $slot }}

    </div>
</div>
