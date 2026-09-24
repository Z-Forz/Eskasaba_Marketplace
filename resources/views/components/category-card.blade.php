@props(['category'])

<a
    href="{{ route('products.index', ['category' => $category->id]) }}"
    class="group relative block rounded-3xl border border-slate-200/80 bg-white p-5 transition duration-300 hover:-translate-y-1 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-950/5 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-emerald-700"
>
    @if ($category->is_new)
        <span class="absolute top-3 right-3 inline-flex items-center gap-1 rounded-full bg-emerald-700 px-2.5 py-0.5 text-[9px] font-black uppercase tracking-wider text-white shadow-xs animate-pulse" title="Kategori Baru (7 Hari)">
            <i class="fa-solid fa-sparkles text-[8px]"></i> BARU
        </span>
    @endif

    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-800 transition duration-300 group-hover:bg-emerald-800 group-hover:text-white dark:bg-emerald-950/50 dark:text-emerald-300 dark:group-hover:bg-emerald-600 sm:h-14 sm:w-14">
        <i class="{{ $category->icon ?: 'fa-solid fa-layer-group' }} text-xl sm:text-2xl"></i>
    </div>

    <h3 class="mt-4 truncate text-sm font-bold text-slate-900 transition-colors group-hover:text-emerald-800 dark:text-white dark:group-hover:text-emerald-400 sm:text-base">
        {{ $category->name }}
    </h3>

    <div class="mt-2 flex flex-wrap items-center justify-between gap-1.5 text-xs font-semibold">
        @if(isset($category->products_count))
            <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1">
                <i class="fa-solid fa-box text-[10px] text-emerald-600 dark:text-emerald-400"></i>
                {{ $category->products_count }} produk
            </span>
        @endif

        @if(!empty($category->reviews_avg_rating) && $category->reviews_avg_rating > 0)
            <span class="inline-flex items-center gap-1 font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/50 px-2 py-0.5 rounded-full border border-amber-200/60 dark:border-amber-800/40 text-[11px]" title="Rating Rata-rata Kategori">
                <i class="fa-solid fa-star text-[10px] text-amber-500"></i>
                {{ number_format((float)$category->reviews_avg_rating, 1) }}
            </span>
        @endif
    </div>

</a>