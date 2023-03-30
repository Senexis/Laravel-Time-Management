@if ($paginator->hasPages())
<ul class="pagination justify-content-center" role="navigation">
    {{-- First Page Link --}}
    @if ($paginator->onFirstPage())
    <li class="page-item pagination-first disabled d-none d-md-block" aria-disabled="true" aria-label="{{ __('pagination.first') }}">
        <span class="page-link" aria-hidden="true"></span>
    </li>
    @else
    <li class="page-item pagination-first d-none d-md-block">
        <a class="page-link" href="{{ $paginator->toArray()['first_page_url'] }}" rel="first" aria-label="{{ __('pagination.first') }}"></a>
    </li>
    @endif

    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
    <li class="page-item pagination-prev disabled" aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
        <span class="page-link" aria-hidden="true"></span>
    </li>
    @else
    <li class="page-item pagination-prev">
        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}"></a>
    </li>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($elements as $element)
    {{-- "Three Dots" Separator --}}
    @if (is_string($element))
    <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
    @endif

    {{-- Array Of Links --}}
    @if (is_array($element))
    @foreach ($element as $page => $url)
    @if ($page == $paginator->currentPage())
    <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
    @else
    <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
    @endif
    @endforeach
    @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
    <li class="page-item pagination-next">
        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}"></a>
    </li>
    @else
    <li class="page-item pagination-next disabled" aria-disabled="true" aria-label="{{ __('pagination.next') }}">
        <span class="page-link" aria-hidden="true"></span>
    </li>
    @endif

    {{-- Last Page Link --}}
    @if ($paginator->hasMorePages())
    <li class="page-item pagination-last d-none d-md-block">
        <a class="page-link" href="{{ $paginator->toArray()['last_page_url'] }}" rel="last" aria-label="{{ __('pagination.last') }}"></a>
    </li>
    @else
    <li class="page-item pagination-last disabled d-none d-md-block" aria-disabled="true" aria-label="{{ __('pagination.last') }}">
        <span class="page-link" aria-hidden="true"></span>
    </li>
    @endif
</ul>
@endif
