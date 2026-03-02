@if ($paginator->hasPages())
    <ul class="pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <!-- <li class="disabled"><span><</span></li> -->
        @else
            <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev"><</a></li>
        @endif

        {{-- Pagination Elements --}}
        @php
            $current = $paginator->currentPage();
            $last = $paginator->lastPage();
            $start = max(1, $current - 1);
            $end = min($last, $current + 1);
            if ($end - $start < 2) {
                if ($start == 1) {
                    $end = min($last, $start + 2);
                } else if ($end == $last) {
                    $start = max(1, $end - 2);
                }
            }
        @endphp
        @for ($i = $start; $i <= $end; $i++)
            @if ($i == $current)
                <li class="active"><span>{{ $i }}</span></li>
            @else
                <li><a href="{{ $paginator->url($i) }}">{{ $i }}</a></li>
            @endif
        @endfor

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">></a></li>
        @endif
    </ul>
@endif
