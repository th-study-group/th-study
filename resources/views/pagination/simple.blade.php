@if ($paginator->hasPages())
    <ul class="pagination pagination-sm mb-0">
        <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $paginator->previousPageUrl() ?? '#' }}" rel="prev">« 이전</a>
        </li>
        <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
            <a class="page-link" href="{{ $paginator->nextPageUrl() ?? '#' }}" rel="next">다음 »</a>
        </li>
    </ul>
@endif
