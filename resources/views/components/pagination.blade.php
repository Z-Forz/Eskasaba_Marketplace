@if ($paginator->hasPages())

    <nav
        role="navigation"
        aria-label="Pagination"
        class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
    >

        {{-- Information --}}
        <p class="text-xs text-slate-500 sm:text-sm">
            Menampilkan
            <span class="font-semibold text-slate-700">
                {{ $paginator->firstItem() ?? 0 }}
            </span>
            -
            <span class="font-semibold text-slate-700">
                {{ $paginator->lastItem() ?? 0 }}
            </span>
            dari
            <span class="font-semibold text-slate-700">
                {{ $paginator->total() }}
            </span>
            data
        </p>

        {{-- Pagination --}}
        <div class="flex items-center gap-1">

            {{-- Previous --}}
            @if ($paginator->onFirstPage())

                <span
                    class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 text-slate-300"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 19l-7-7 7-7"
                        />
                    </svg>
                </span>

            @else

                <a
                    href="{{ $paginator->previousPageUrl() }}"
                    class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-50"
                    aria-label="Halaman sebelumnya"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 19l-7-7 7-7"
                        />
                    </svg>
                </a>

            @endif

            {{-- Page Numbers --}}
            @foreach ($elements as $element)

                @if (is_string($element))

                    <span class="px-2 text-sm text-slate-400">
                        {{ $element }}
                    </span>

                @endif

                @if (is_array($element))

                    @foreach ($element as $page => $url)

                        @if ($page == $paginator->currentPage())

                            <span
                                class="flex h-9 min-w-9 items-center justify-center rounded-xl bg-slate-900 px-2.5 text-sm font-semibold text-white"
                            >
                                {{ $page }}
                            </span>

                        @else

                            <a
                                href="{{ $url }}"
                                class="flex h-9 min-w-9 items-center justify-center rounded-xl border border-slate-200 bg-white px-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
                            >
                                {{ $page }}
                            </a>

                        @endif

                    @endforeach

                @endif

            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())

                <a
                    href="{{ $paginator->nextPageUrl() }}"
                    class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-50"
                    aria-label="Halaman berikutnya"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 5l7 7-7 7"
                        />
                    </svg>
                </a>

            @else

                <span
                    class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 text-slate-300"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 5l7 7-7 7"
                        />
                    </svg>
                </span>

            @endif

        </div>

    </nav>

@endif