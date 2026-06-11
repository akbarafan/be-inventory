@if ($paginator->hasPages())
<div class="pagination">
    @if ($paginator->onFirstPage())
        <span class="page-item disabled"><span class="page-link">← Prev</span></span>
    @else
        <a class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}">← Prev</a></a>
    @endif

    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="page-item disabled"><span class="page-link">{{ $element }}</span></span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="page-item active"><span class="page-link">{{ $page }}</span></span>
                @else
                    <a class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></a>
                @endif
            @endforeach
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
        <a class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}">Next →</a></a>
    @else
        <span class="page-item disabled"><span class="page-link">Next →</span></span>
    @endif
</div>
@endif
