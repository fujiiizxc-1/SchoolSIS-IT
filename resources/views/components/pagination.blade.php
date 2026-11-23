@if ($paginator->hasPages())
    <nav class="pagination" aria-label="Pagination">
        @if ($paginator->previousPageUrl())
            <a class="button secondary" href="{{ $paginator->previousPageUrl() }}" rel="prev">Previous</a>
        @endif
        <span>Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span>
        @if ($paginator->nextPageUrl())
            <a class="button secondary" href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a>
        @endif
    </nav>
@endif
