@if ($paginator->hasPages())
<nav class="flex flex-col items-center gap-3 mt-6" dir="rtl">

    <div class="flex items-center gap-2 flex-wrap justify-center">

        {{-- قبلی --}}
        @if ($paginator->onFirstPage())
            <span class="px-4 py-2 rounded-xl bg-dark-800 text-dark-500 border-2 border-dark-700 cursor-not-allowed text-sm">
                « قبلی
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                class="px-4 py-2 rounded-xl bg-dark-700 text-cream-300 border-2 border-dark-600 hover:bg-dark-600 hover:border-primary-500/50 hover:text-primary-300 transition-all duration-300 text-sm">
                « قبلی
            </a>
        @endif

        {{-- شماره صفحات --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-3 py-2 text-dark-400 text-sm">...</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-4 py-2 rounded-xl bg-primary-500/25 text-primary-300 border-2 border-primary-500/50 font-bold text-sm shadow-lg shadow-primary-900/30">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                            class="px-4 py-2 rounded-xl bg-dark-700 text-cream-300 border-2 border-dark-600 hover:bg-dark-600 hover:border-primary-500/50 hover:text-primary-300 transition-all duration-300 text-sm">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- بعدی --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                class="px-4 py-2 rounded-xl bg-dark-700 text-cream-300 border-2 border-dark-600 hover:bg-dark-600 hover:border-primary-500/50 hover:text-primary-300 transition-all duration-300 text-sm">
                بعدی »
            </a>
        @else
            <span class="px-4 py-2 rounded-xl bg-dark-800 text-dark-500 border-2 border-dark-700 cursor-not-allowed text-sm">
                بعدی »
            </span>
        @endif

    </div>

    {{-- اطلاعات صفحه --}}
    @if ($paginator->firstItem())
    <p class="text-dark-400 text-xs">
        نمایش {{ $paginator->firstItem() }} تا {{ $paginator->lastItem() }} از {{ $paginator->total() }} مورد
    </p>
    @endif

</nav>
@endif