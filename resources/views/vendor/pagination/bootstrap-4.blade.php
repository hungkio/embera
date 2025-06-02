@if ($paginator->hasPages())
    <nav>
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <a href="javascript:void(0)"><i class="fas fa-angle-double-left"></i></a>
                </li>
            @else
                <li class="page-item" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <a href="{{ $paginator->previousPageUrl() }}"><i class="fas fa-angle-double-left"></i></a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item" aria-disabled="true" aria-label="{{ $element }}">
                        <a href="javascript:void(0)">{{ $element }}</a>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-disabled="true" aria-label="{{ $page }}">
                                <a href="javascript:void(0)">{{ $page }}</a>
                            </li>
                        @else
                            <li class="page-item" aria-disabled="true" aria-label="{{ $page }}">
                                <a href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item"><a href="{{ $paginator->nextPageUrl() }}" aria-label="@lang('pagination.next')"><i class="fas fa-angle-double-right"></i></a></li>
            @else
                <li class="page-item"><a href="javascript:void(0)" aria-label="@lang('pagination.next')"><i class="fas fa-angle-double-right"></i></a></li>
            @endif
        </ul>
    </nav>
@endif
