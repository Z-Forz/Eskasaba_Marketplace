<x-layouts.app :title="'Toko ' . ($seller->user->username ?? 'Seller')">

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Navigation Breadcrumb / Back --}}
        <div class="mb-6 flex items-center justify-between">
            <a
                href="{{ route('products.index') }}"
                class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-600 hover:text-emerald-700 transition dark:text-slate-400 dark:hover:text-emerald-400"
            >
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Kembali ke Katalog Produk</span>
            </a>

            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-extrabold text-emerald-800 border border-emerald-200/60 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-900/60">
                <i class="fa-solid fa-shop text-emerald-600"></i> Profil Toko Seller
            </span>
        </div>

        {{-- Seller Header Hero Banner --}}
        <div class="relative overflow-hidden rounded-3xl border border-slate-200/80 bg-gradient-to-br from-white via-slate-50 to-emerald-50/40 p-6 shadow-sm dark:border-slate-800 dark:from-slate-900 dark:via-slate-900/90 dark:to-emerald-950/30 sm:p-8">

            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

                {{-- Left: Avatar & Info --}}
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">

                    <div class="relative flex h-20 w-20 sm:h-24 sm:w-24 shrink-0 items-center justify-center rounded-3xl bg-emerald-700 text-2xl sm:text-3xl font-black text-white shadow-lg shadow-emerald-900/20 border-2 border-emerald-500/40">
                        {{ strtoupper(substr($seller->user->username ?? 'S', 0, 1)) }}
                        <span class="absolute -bottom-1 -right-1 flex h-7 w-7 items-center justify-center rounded-full bg-emerald-500 text-white text-xs shadow-md border-2 border-white dark:border-slate-900" title="Penjual Terverifikasi">
                            <i class="fa-solid fa-check"></i>
                        </span>
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                                Toko {{ $seller->user->username ?? 'Seller' }}
                            </h1>

                            <span class="inline-flex items-center gap-1 rounded-xl bg-emerald-100 px-3 py-0.5 text-xs font-bold text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-300/60 dark:border-emerald-800">
                                <i class="fa-solid fa-user-graduate text-[10px]"></i>
                                {{ $seller->user->role === 'teacher' ? 'Guru' : 'Siswa' }}
                            </span>
                        </div>

                        <p class="text-xs sm:text-sm font-semibold text-emerald-700 dark:text-emerald-400 flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Penjual Terverifikasi Eskasaba Marketplace</span>
                        </p>

                        @if($seller->description)
                            <p class="mt-2 text-xs sm:text-sm text-slate-600 dark:text-slate-300 max-w-2xl leading-relaxed">
                                {{ $seller->description }}
                            </p>
                        @endif

                        @if($seller->created_at)
                            <p class="text-[11px] text-slate-400 dark:text-slate-500 pt-1">
                                <i class="fa-regular fa-calendar-check mr-1"></i> Bergabung sejak {{ $seller->created_at->format('M Y') }}
                            </p>
                        @endif
                    </div>

                </div>

                {{-- Right: Stats & Action --}}
                <div class="flex flex-col gap-3 w-full sm:w-auto lg:self-center">

                    {{-- Stats Summary Grid --}}
                    <div class="grid grid-cols-3 gap-2.5 sm:gap-3 rounded-2xl bg-white/80 p-3 shadow-xs border border-slate-200/80 dark:bg-slate-800/80 dark:border-slate-700/60 backdrop-blur-xs">

                        <div class="text-center px-2 py-1">
                            <p class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">
                                {{ $stats['total_products'] }}
                            </p>
                            <p class="text-[10px] sm:text-xs font-bold text-slate-500 dark:text-slate-400">
                                Produk
                            </p>
                        </div>

                        <div class="text-center px-2 py-1 border-x border-slate-200 dark:border-slate-700">
                            <p class="text-lg sm:text-xl font-black text-emerald-700 dark:text-emerald-400">
                                {{ $stats['total_sales'] }}
                            </p>
                            <p class="text-[10px] sm:text-xs font-bold text-slate-500 dark:text-slate-400">
                                Terjual
                            </p>
                        </div>

                        <div class="text-center px-2 py-1">
                            <p class="text-lg sm:text-xl font-black text-amber-500 flex items-center justify-center gap-0.5">
                                <i class="fa-solid fa-star text-sm"></i>
                                <span>{{ $stats['avg_rating'] }}</span>
                            </p>
                            <p class="text-[10px] sm:text-xs font-bold text-slate-500 dark:text-slate-400">
                                Rating ({{ $stats['total_reviews'] }})
                            </p>
                        </div>

                    </div>

                    {{-- WA Contact Button (Underneath Stats Card) --}}
                    @if($seller->whatsapp_number)
                        <a
                            href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $seller->whatsapp_number) }}?text=Halo%20{{ urlencode($seller->user->username) }},%20saya%20ingin%20bertanya%20mengenai%20produk%20di%20toko%20Anda"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-5 py-3 text-xs sm:text-sm font-bold text-white shadow-md transition hover:bg-emerald-800 active:scale-95 cursor-pointer"
                        >
                            <i class="fa-brands fa-whatsapp text-base"></i>
                            <span>Hubungi Seller</span>
                        </a>
                    @endif

                </div>

            </div>

        </div>

        {{-- Filter & Search Header Section --}}
        <div class="mt-8 space-y-4">

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                <div>
                    <h2 class="text-xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-boxes-packing text-emerald-600"></i> Barang & Produk dari Seller Ini
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Menampilkan seluruh pilihan barang yang dijual oleh toko {{ $seller->user->username }}.
                    </p>
                </div>

                {{-- Search & Sort Controls --}}
                <form method="GET" action="{{ route('sellers.show', $seller) }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif

                    {{-- Search Input --}}
                    <div class="relative min-w-56 sm:min-w-64">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari produk toko ini..."
                            class="w-full rounded-2xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-xs font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-800 dark:bg-slate-900 dark:text-white"
                        >
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                    </div>

                    {{-- Sort Dropdown --}}
                    <select
                        name="sort"
                        onchange="this.form.submit()"
                        class="rounded-2xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-bold text-slate-700 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300"
                    >
                        <option value="" @selected(request('sort') === '')>Urutkan: Terbaru</option>
                        <option value="best_seller" @selected(request('sort') === 'best_seller')>Terlaris</option>
                        <option value="rating" @selected(request('sort') === 'rating')>Rating Tertinggi</option>
                        <option value="price_low" @selected(request('sort') === 'price_low')>Harga: Terendah</option>
                        <option value="price_high" @selected(request('sort') === 'price_high')>Harga: Tertinggi</option>
                    </select>

                    <button type="submit" class="hidden"></button>
                </form>

            </div>

            {{-- Category Filter Pills --}}
            @if($categories->isNotEmpty())
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <a
                        href="{{ route('sellers.show', array_filter(['seller' => $seller->id, 'search' => request('search'), 'sort' => request('sort')])) }}"
                        class="rounded-xl px-3 py-1.5 text-xs font-bold transition inline-flex items-center gap-1.5 {{ !request('category') ? 'bg-emerald-700 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300' }}"
                    >
                        <span>Semua Kategori</span>
                        <span class="rounded-full bg-white/20 px-1.5 py-0.2 text-[10px]">{{ $seller->products_count }}</span>
                    </a>

                    @foreach($categories as $cat)
                        <a
                            href="{{ route('sellers.show', array_filter(['seller' => $seller->id, 'category' => $cat->id, 'search' => request('search'), 'sort' => request('sort')])) }}"
                            class="rounded-xl px-3 py-1.5 text-xs font-bold transition inline-flex items-center gap-1.5 {{ request('category') == $cat->id ? 'bg-emerald-700 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300' }}"
                        >
                            <span>{{ $cat->name }}</span>
                            <span class="rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 px-1.5 py-0.2 text-[10px] font-black">{{ $cat->products_count }}</span>
                        </a>
                    @endforeach
                </div>
            @endif

        </div>

        {{-- Product Catalog Grid --}}
        <div class="mt-6">

            @if($products->isNotEmpty())

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 sm:gap-5">
                    @foreach($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>

                {{-- Pagination Links --}}
                <div class="mt-10 flex justify-center">
                    {{ $products->links() }}
                </div>

            @else

                <div class="rounded-3xl border border-slate-200/80 bg-white p-12 text-center shadow-xs dark:border-slate-800 dark:bg-slate-900">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 text-2xl">
                        <i class="fa-solid fa-box-open"></i>
                    </div>

                    <h3 class="mt-4 text-base font-bold text-slate-900 dark:text-white sm:text-lg">
                        Tidak ada barang ditemukan
                    </h3>

                    <p class="mt-1.5 text-xs sm:text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                        @if(request('search') || request('category'))
                            Toko {{ $seller->user->username }} tidak memiliki produk yang sesuai dengan pencarian atau filter yang dipilih.
                        @else
                            Seller ini belum menambahkan produk jualan ke katalog toko.
                        @endif
                    </p>

                    @if(request('search') || request('category'))
                        <div class="mt-5">
                            <a
                                href="{{ route('sellers.show', $seller) }}"
                                class="inline-flex items-center gap-1.5 rounded-2xl bg-emerald-700 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-800"
                            >
                                <i class="fa-solid fa-rotate-left"></i> Reset Filter Pencarian Toko
                            </a>
                        </div>
                    @endif
                </div>

            @endif

        </div>

    </div>

</x-layouts.app>
