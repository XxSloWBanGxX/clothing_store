@if ($paginator->hasPages())
    <nav class="pagination">
        @if (! $paginator->onFirstPage())
            <a href="{{ $paginator->previousPageUrl() }}" class="pagination-link" rel="prev">←</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="pagination-link disabled">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="pagination-link active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pagination-link">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="pagination-link" rel="next">→</a>
        @endif
    </nav>
@endif
