@if ($paginator->hasPages())
    <nav aria-label="Page navigation">
        <ul class="pagination pagination-rounded">
            {{-- First Page Link --}}
            <li class="page-item first {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $paginator->url(1) }}" aria-label="First">
                    <i class="ti ti-chevrons-left ti-sm"></i>
                </a>
            </li>

            {{-- Previous Page Link --}}
            <li class="page-item prev {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}" aria-label="Previous">
                    <i class="ti ti-chevron-left ti-sm"></i>
                </a>
            </li>

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li class="page-item {{ $page == $paginator->currentPage() ? 'active' : '' }}">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            <li class="page-item next {{ !$paginator->hasMorePages() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}" aria-label="Next">
                    <i class="ti ti-chevron-right ti-sm"></i>
                </a>
            </li>

            {{-- Last Page Link --}}
            <li class="page-item last {{ $paginator->currentPage() == $paginator->lastPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}" aria-label="Last">
                    <i class="ti ti-chevrons-right ti-sm"></i>
                </a>
            </li>
        </ul>
    </nav>
@endif
