@props(['category'])

<a
    href="{{ route('products.index', ['category' => $category->id]) }}"
    class="group block rounded-3xl border border-slate-200/80 bg-white p-5 transition duration-300 hover:-translate-y-1 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-950/5 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-emerald-700"
>

    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-800 transition duration-300 group-hover:bg-emerald-800 group-hover:text-white dark:bg-emerald-950/50 dark:text-emerald-300 dark:group-hover:bg-emerald-600 sm:h-14 sm:w-14">
        <i class="{{ $category->icon ?: 'fa-solid fa-layer-group' }} text-xl sm:text-2xl"></i>
    </div>

    <h3 class="mt-4 truncate text-sm font-bold text-slate-900 transition-colors group-hover:text-emerald-800 dark:text-white dark:group-hover:text-emerald-400 sm:text-base">
        {{ $category->name }}
    </h3>

    @if(isset($category->products_count))
        <p class="mt-1 text-xs font-semibold text-slate-400 sm:text-sm">
            {{ $category->products_count }} produk
        </p>
    @endif

</a>