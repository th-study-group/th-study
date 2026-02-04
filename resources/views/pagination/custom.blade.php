@if ($paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();

        $pcMax = 10;
        $mobileMax = 3;

        $pcHalf = intdiv($pcMax, 2);
        $mobileHalf = intdiv($mobileMax, 2);

        $pcStart = max(1, min($current - $pcHalf, $last - $pcMax + 1));
        $pcEnd = min($last, $pcStart + $pcMax - 1);

        $mobileStart = max(1, min($current - $mobileHalf, $last - $mobileMax + 1));
        $mobileEnd = min($last, $mobileStart + $mobileMax - 1);
    @endphp

    <div class="d-flex flex-column align-items-center gap-2">
        <ul class="pagination pagination-sm mb-0 d-none d-md-flex">
            <li class="page-item {{ $current === 1 ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $current === 1 ? '#' : $paginator->url(1) }}" aria-label="First">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $paginator->previousPageUrl() ?? '#' }}" rel="prev" aria-label="Previous">
                    <span aria-hidden="true">&lsaquo;</span>
                </a>
            </li>

            @if ($pcStart > 1)
                <li class="page-item disabled"><span class="page-link">…</span></li>
            @endif

            @for ($page = $pcStart; $page <= $pcEnd; $page++)
                <li class="page-item {{ $page == $current ? 'active' : '' }}">
                    <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                </li>
            @endfor

            @if ($pcEnd < $last)
                <li class="page-item disabled"><span class="page-link">…</span></li>
            @endif

            <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                <a class="page-link" href="{{ $paginator->nextPageUrl() ?? '#' }}" rel="next" aria-label="Next">
                    <span aria-hidden="true">&rsaquo;</span>
                </a>
            </li>
            <li class="page-item {{ $current === $last ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $current === $last ? '#' : $paginator->url($last) }}" aria-label="Last">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>

        <ul class="pagination pagination-sm mb-0 d-flex d-md-none">
            <li class="page-item {{ $current === 1 ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $current === 1 ? '#' : $paginator->url(1) }}" aria-label="First">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $paginator->previousPageUrl() ?? '#' }}" rel="prev">« 이전</a>
            </li>
            @if ($mobileStart > 1)
                <li class="page-item disabled"><span class="page-link">…</span></li>
            @endif
            @for ($page = $mobileStart; $page <= $mobileEnd; $page++)
                <li class="page-item {{ $page == $current ? 'active' : '' }}">
                    <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                </li>
            @endfor
            @if ($mobileEnd < $last)
                <li class="page-item disabled"><span class="page-link">…</span></li>
            @endif
            <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                <a class="page-link" href="{{ $paginator->nextPageUrl() ?? '#' }}" rel="next">다음 »</a>
            </li>
            <li class="page-item {{ $current === $last ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $current === $last ? '#' : $paginator->url($last) }}" aria-label="Last">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>

        @if (config('pagination.showing.enabled'))
            <div class="text-secondary small {{ config('pagination.showing.class') }}">
                {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }}
            </div>
        @endif
    </div>
@endif
