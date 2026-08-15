@props(['seller'])

<a
    href="{{ route('products.index', ['seller' => $seller->id]) }}"
    class="group block rounded-2xl border border-slate-200 bg-white p-4 transition duration-300 hover:-translate-y-1 hover:shadow-lg sm:p-5"
>
    <div class="flex items-center gap-3 sm:gap-4">

        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-slate-900 text-sm font-bold text-white sm:h-12 sm:w-12">
            {{ strtoupper(substr($seller->user->name ?? 'S', 0, 1)) }}
        </div>

        <div class="min-w-0 flex-1">
            <h3 class="truncate text-sm font-semibold text-slate-900 sm:text-base">
                {{ $seller->user->name ?? 'Seller' }}
            </h3>

            @if($seller->user?->username)
                <p class="truncate text-xs text-slate-500 sm:text-sm">
                    {{ '@' . $seller->user->username }}
                </p>
            @endif
        </div>

        <svg
            class="h-4 w-4 shrink-0 text-slate-400 transition group-hover:translate-x-1 group-hover:text-slate-900"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.8"
                d="M9 5l7 7-7 7"
            />
        </svg>

    </div>

    @if($seller->description)
        <p class="mt-4 line-clamp-2 text-xs leading-5 text-slate-500 sm:text-sm">
            {{ $seller->description }}
        </p>
    @endif
</a>