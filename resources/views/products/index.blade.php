<x-layouts.app title="Katalog Produk">

    <section class="bg-slate-50/70 py-10 sm:py-14 dark:bg-slate-950">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-400">
                    <i class="fa-solid fa-store mr-1"></i> Katalog Eskasaba Marketplace
                </p>

                <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-900 dark:text-white sm:text-4xl">
                    Semua Produk Karya Siswa & Guru
                </h1>

                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-500 dark:text-slate-400">
                    Jelajahi berbagai produk kuliner, jasa, kerajinan, dan peralatan sekolah buatan warga SMKN 1 Bantul.
                </p>
            </div>

            {{-- Filter & Search Card --}}
            <div class="mt-8 rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900 sm:p-6">

                <form
                    action="{{ route('products.index') }}"
                    method="GET"
                    class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4"
                >

                    {{-- Search Input --}}
                    <div class="sm:col-span-2">
                        <label for="search" class="mb-1.5 block text-xs font-bold text-slate-700 dark:text-slate-300">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 mr-1"></i> Cari Produk
                        </label>
                        <div class="relative">
                            <input
                                id="search"
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Cari nama produk, rasa, atau kategori..."
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 pl-10 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-4 text-xs text-slate-400"></i>
                        </div>
                    </div>

                    {{-- Category Select --}}
                    <div>
                        <label for="category" class="mb-1.5 block text-xs font-bold text-slate-700 dark:text-slate-300">
                            <i class="fa-solid fa-layer-group text-slate-400 mr-1"></i> Kategori
                        </label>
                        <select
                            id="category"
                            name="category"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option
                                    value="{{ $category->id }}"
                                    @selected(request('category') == $category->id)
                                >
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sort Select --}}
                    <div>
                        <label for="sort" class="mb-1.5 block text-xs font-bold text-slate-700 dark:text-slate-300">
                            <i class="fa-solid fa-arrow-down-short-wide text-slate-400 mr-1"></i> Urutkan
                        </label>
                        <select
                            id="sort"
                            name="sort"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >
                            <option value="">Terbaru</option>
                            <option value="price_low" @selected(request('sort') === 'price_low')>Harga Terendah</option>
                            <option value="price_high" @selected(request('sort') === 'price_high')>Harga Tertinggi</option>
                            <option value="name" @selected(request('sort') === 'name')>Nama A-Z</option>
                        </select>
                    </div>

                    {{-- Submit & Reset Buttons --}}
                    <div class="sm:col-span-2 lg:col-span-4 flex flex-wrap items-center justify-end gap-3 pt-1">
                        @if(request()->hasAny(['search', 'category', 'sort']))
                            <a
                                href="{{ route('products.index') }}"
                                class="rounded-2xl border border-slate-200 bg-slate-100 px-5 py-2.5 text-xs font-bold text-slate-700 transition hover:bg-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 flex items-center gap-1"
                            >
                                <i class="fa-solid fa-rotate-left text-xs"></i> Reset Filter
                            </a>
                        @endif

                        <button
                            type="submit"
                            class="rounded-2xl bg-emerald-700 px-6 py-2.5 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-800 flex items-center gap-1.5"
                        >
                            <i class="fa-solid fa-filter"></i> Terapkan Filter
                        </button>
                    </div>

                </form>

            </div>

            {{-- Products Grid --}}
            <div class="mt-10">

                @if($products->isNotEmpty())

                    <div class="mb-6 flex items-center justify-between">
                        <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">
                            Menampilkan <span class="font-black text-emerald-800 dark:text-emerald-400">{{ $products->total() }}</span> produk yang tersedia
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-10">
                        {{ $products->links('components.pagination') }}
                    </div>

                @else

                    <x-empty-state
                        icon="search"
                        title="Produk tidak ditemukan"
                        description="Coba ubah kata kunci pencarian atau filter kategori yang Anda pilih."
                        :action="route('products.index')"
                        action-text="Lihat Semua Produk"
                    />

                @endif

            </div>

        </div>

    </section>

</x-layouts.app>