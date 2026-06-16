@if ($paginator->hasPages())
    <nav class="flex flex-col items-center gap-3 mt-6" dir="rtl">
        <div class="flex items-center gap-2 flex-wrap justify-center">

            {{-- قبلی --}}
            @if ($paginator->onFirstPage())
                <span class="px-4 py-2 rounded-xl bg-gray-100 text-gray-400 border border-gray-200 cursor-not-allowed text-sm">
                    « قبلی
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   class="px-4 py-2 rounded-xl bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 hover:border-blue-400 hover:text-blue-600 transition-all duration-300 text-sm shadow-sm">
                    « قبلی
                </a>
            @endif

            {{-- شماره صفحات --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-3 py-2 text-gray-400 text-sm">...</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-4 py-2 rounded-xl bg-blue-600 text-white border border-blue-600 font-bold text-sm shadow-sm">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               class="px-4 py-2 rounded-xl bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 hover:border-blue-400 hover:text-blue-600 transition-all duration-300 text-sm shadow-sm">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- بعدی --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   class="px-4 py-2 rounded-xl bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 hover:border-blue-400 hover:text-blue-600 transition-all duration-300 text-sm shadow-sm">
                    بعدی »
                </a>
            @else
                <span class="px-4 py-2 rounded-xl bg-gray-100 text-gray-400 border border-gray-200 cursor-not-allowed text-sm">
                    بعدی »
                </span>
            @endif

        </div>

        {{-- اطلاعات صفحه --}}
        @if ($paginator->firstItem())
            <p class="text-gray-500 text-xs">
                نمایش {{ $paginator->firstItem() }} تا {{ $paginator->lastItem() }} از {{ $paginator->total() }} مورد
            </p>
        @endif
    </nav>
@endif