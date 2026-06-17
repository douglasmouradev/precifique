@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination" class="flex items-center justify-between gap-4 flex-wrap">
    <p class="text-sm text-slate-500">
        @if ($paginator->total() > 0)
        {{ __('pagination.showing', ['from' => $paginator->firstItem(), 'to' => $paginator->lastItem(), 'total' => $paginator->total()]) }}
        @endif
    </p>
    <div class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white p-1 shadow-sm">
        @if ($paginator->onFirstPage())
        <span class="px-3 py-1.5 text-sm text-slate-300 rounded-lg cursor-not-allowed" aria-disabled="true">&lsaquo;</span>
        @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-3 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-lg transition-colors" aria-label="{{ __('pagination.previous') }}">&lsaquo;</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
            <span class="px-2 text-slate-400">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                    <span class="px-3 py-1.5 text-sm font-semibold rounded-lg bg-brand text-ink" aria-current="page">{{ $page }}</span>
                    @else
                    <a href="{{ $url }}" class="px-3 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-lg transition-colors">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-3 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-lg transition-colors" aria-label="{{ __('pagination.next') }}">&rsaquo;</a>
        @else
        <span class="px-3 py-1.5 text-sm text-slate-300 rounded-lg cursor-not-allowed" aria-disabled="true">&rsaquo;</span>
        @endif
    </div>
</nav>
@endif
