@props(['category'])

<a
    href="{{ route('products.index', ['category' => $category->id]) }}"
    class="group block rounded-2xl border border-slate-200 bg-white p-4 transition duration-300 hover:-translate-y-1 hover:border-slate-300 hover:shadow-lg sm:p-5"
>

    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition group-hover:bg-slate-900 group-hover:text-white sm:h-12 sm:w-12">

        <svg
            class="h-5 w-5 sm:h-6 sm:w-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.8"
                d="M4 6h16M4 12h16M4 18h16"
            />
        </svg>

    </div>

    <h3 class="mt-4 truncate text-sm font-semibold text-slate-900 sm:text-base">
        {{ $category->name }}
    </h3>

    @if(isset($category->products_count))

        <p class="mt-1 text-xs text-slate-500 sm:text-sm">
            {{ $category->products_count }} produk
        </p>

    @endif

</a>