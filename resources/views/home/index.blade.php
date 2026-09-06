<x-layouts.app>

    {{-- =========================================================
        HERO SECTION
    ========================================================== --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-emerald-950 via-slate-950 to-emerald-900">

        {{-- Background Glow Accent --}}
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            @if(!empty($settings?->hero_image))
                <img
                    src="{{ asset('storage/' . $settings->hero_image) }}"
                    alt="{{ $settings->hero_title ?? 'Eskasaba Market' }}"
                    class="h-full w-full object-cover opacity-20 blur-sm"
                >
            @else
                <div class="absolute -left-20 -top-20 h-96 w-96 rounded-full bg-emerald-500/20 blur-3xl"></div>
                <div class="absolute -right-20 -bottom-20 h-96 w-96 rounded-full bg-emerald-700/20 blur-3xl"></div>
            @endif        
        </div>

        <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8 lg:py-28">

            <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16">

                {{-- Hero Content --}}
                <div class="max-w-2xl">

                    <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3.5 py-1.5 text-xs font-bold text-emerald-300 backdrop-blur-md">
                        <i class="fa-solid fa-graduation-cap text-emerald-400"></i> Marketplace Resmi SMKN 1 Bangsri
                    </span>

                    <h1 class="mt-5 text-3xl font-black leading-tight tracking-tight text-white sm:text-4xl lg:text-6xl">
                        {{ $settings->hero_title ?? 'Selamat Datang di Eskasaba Market' }}
                    </h1>

                    <p class="mt-5 max-w-xl text-sm leading-6 text-emerald-100/80 sm:text-base sm:leading-7">
                        {{ $settings->hero_description ?? 'Marketplace internal sekolah untuk memudahkan warga sekolah melakukan transaksi jual beli produk karya siswa & guru dengan aman, praktis, dan terpercaya.' }}
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">

                        <a
                            href="{{ route('products.index') }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-emerald-950/40 transition hover:bg-emerald-500 hover:shadow-emerald-600/30 sm:w-auto"
                        >
                            <span>Mulai Belanja</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>

                        <a
                            href="{{ route('guide') }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-emerald-500/30 bg-white/5 px-6 py-3.5 text-sm font-bold text-white transition hover:bg-white/10 backdrop-blur-md sm:w-auto"
                        >
                            <i class="fa-solid fa-book-open text-xs text-emerald-400"></i>
                            <span>Panduan COD Sekolah</span>
                        </a>

                    </div>

                </div>
            </div>

        </div>
    </section>


    {{-- =========================================================
        FEATURES
    ========================================================== --}}
    <section class="border-b border-slate-100 bg-white dark:border-slate-800 dark:bg-slate-900">
        <div class="mx-auto grid max-w-7xl grid-cols-1 divide-y divide-slate-100 px-4 sm:grid-cols-3 sm:divide-x sm:divide-y-0 sm:px-6 lg:px-8 dark:divide-slate-800">

            <div class="flex items-center gap-4 px-0 py-6 sm:px-6 lg:py-8">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-100 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-900/60">
                    <i class="fa-solid fa-shield-halved text-xl"></i>
                </div>

                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                        Aman & Terverifikasi
                    </h3>

                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Penjual diverifikasi langsung oleh Admin Sekolah.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-4 px-0 py-6 sm:px-6 lg:py-8">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-100 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-900/60">
                    <i class="fa-solid fa-basket-shopping text-xl"></i>
                </div>

                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                        Karya Siswa & Guru
                    </h3>

                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Produk kuliner, kerajinan, dan jasa warga sekolah.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-4 px-0 py-6 sm:px-6 lg:py-8">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-100 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-900/60">
                    <i class="fa-solid fa-handshake text-xl"></i>
                </div>

                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                        COD Praktis di Sekolah
                    </h3>

                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Pengambilan di kantin, gazebo, atau lokasi kesepakatan.
                    </p>
                </div>
            </div>

        </div>
    </section>


    {{-- =========================================================
        FEATURED PRODUCTS SHOWCASE (PRODUK UNGGULAN REVISED)
    ========================================================== --}}
    @if(isset($featuredProducts) && $featuredProducts->isNotEmpty())
        <section class="relative overflow-hidden bg-gradient-to-b from-emerald-900/10 via-slate-50 to-white py-14 sm:py-16 dark:from-emerald-950/40 dark:via-slate-900 dark:to-slate-950">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                {{-- Header --}}
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between mb-8">
                    <div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3.5 py-1 text-xs font-black uppercase tracking-wider text-emerald-900 border border-emerald-300/60 dark:bg-emerald-950 dark:text-emerald-300 dark:border-emerald-800">
                            <i class="fa-solid fa-crown text-amber-500"></i> Rekomendasi Pilihan
                        </span>
                        <h2 class="mt-2 text-2xl font-black tracking-tight text-slate-900 sm:text-3xl dark:text-white flex items-center gap-2">
                            <i class="fa-brands fa-gripfire text-amber-500"></i>Produk Unggulan & Terlaris Sekolah
                        </h2>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                            Produk favorit pilihan siswa dan guru dengan penawaran dan ulasan terbaik.
                        </p>
                    </div>

                    <a
                        href="{{ route('products.index') }}"
                        class="inline-flex items-center gap-1.5 rounded-2xl bg-emerald-700 px-4 py-2.5 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-800 dark:bg-emerald-600 dark:hover:bg-emerald-700 shrink-0"
                    >
                        <span>Jelajahi Katalog Lengkap</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

                {{-- Grid Cards (1 per baris di HP) --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($featuredProducts as $featuredItem)
                        <x-product-card :product="$featuredItem" />
                    @endforeach
                </div>

            </div>
        </section>
    @endif


    {{-- =========================================================
        CATEGORIES
    ========================================================== --}}
    <section class="bg-slate-50 py-14 sm:py-16 dark:bg-slate-900/60">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="flex items-end justify-between gap-4">

                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-400">
                        <i class="fa-solid fa-border-all mr-1"></i> Jelajahi Kategori
                    </p>

                    <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-900 sm:text-3xl dark:text-white">
                        Kategori Produk Unggulan
                    </h2>
                </div>

                <a
                    href="{{ route('products.index') }}"
                    class="hidden text-sm font-bold text-emerald-800 hover:text-emerald-900 sm:inline-flex items-center gap-1 dark:text-emerald-400 dark:hover:text-emerald-300"
                >
                    <span>Lihat Semua</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>

            </div>


            @if($categories->isNotEmpty())

                <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">

                    @foreach($categories as $category)

                        <x-category-card
                            :category="$category"
                        />

                    @endforeach

                </div>

            @else

                <div class="mt-8">
                    <x-empty-state
                        title="Belum ada kategori"
                        description="Kategori produk belum tersedia saat ini."
                    />
                </div>

            @endif

        </div>

    </section>


    {{-- =========================================================
        LATEST PRODUCTS
    ========================================================== --}}
    <section class="bg-white py-14 sm:py-16 dark:bg-slate-950">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="flex items-end justify-between gap-4">

                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-400">
                        <i class="fa-solid fa-fire mr-1"></i> Pilihan Terbaru
                    </p>

                    <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-900 sm:text-3xl dark:text-white">
                        Semua Produk Karya Siswa & Guru
                    </h2>
                </div>

                <a
                    href="{{ route('products.index') }}"
                    class="hidden text-sm font-bold text-emerald-800 hover:text-emerald-900 sm:inline-flex items-center gap-1 dark:text-emerald-400 dark:hover:text-emerald-300"
                >
                    <span>Jelajahi Semua Produk</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>

            </div>


            @if($products->isNotEmpty())

                <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                    @foreach($products as $product)

                        <x-product-card
                            :product="$product"
                        />

                    @endforeach

                </div>

            @else

                <div class="mt-8">
                    <x-empty-state
                        icon="search"
                        title="Belum ada produk"
                        description="Produk belum tersedia di marketplace."
                    />
                </div>

            @endif


            <div class="mt-8 text-center sm:hidden">

                <a
                    href="{{ route('products.index') }}"
                    class="inline-flex items-center gap-2 rounded-2xl bg-emerald-800 px-6 py-3 text-xs font-bold text-white shadow-xs"
                >
                    <span>Lihat Semua Produk</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>

            </div>

        </div>

    </section>


    {{-- =========================================================
        ABOUT & VISION MISSION
    ========================================================== --}}
    <section class="bg-slate-50 py-14 sm:py-16 dark:bg-slate-900/80">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="grid gap-10 lg:grid-cols-2 lg:items-center">

                <div>

                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-400">
                        <i class="fa-solid fa-building-columns mr-1"></i> Tentang Marketplace
                    </p>

                    <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-900 sm:text-3xl dark:text-white">
                        {{ $settings->website_name ?? 'Eskasaba Market' }}
                    </h2>

                    <p class="mt-4 text-sm leading-7 text-slate-600 dark:text-slate-300 sm:text-base">
                        {{ $settings->about ?? 'Eskasaba Market merupakan platform marketplace digital resmi sekolah yang memfasilitasi siswa, guru, dan staf untuk bertransaksi secara praktis, jujur, dan mendukung semangat kewirausahaan muda.' }}
                    </p>

                </div>

                <div class="grid gap-5 sm:grid-cols-2">

                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">

                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-800 text-white shadow-xs">
                            <i class="fa-solid fa-eye text-lg"></i>
                        </div>

                        <h3 class="mt-4 text-base font-bold text-slate-900 dark:text-white">
                            Visi Kami
                        </h3>

                        <p class="mt-2 text-xs leading-5 text-slate-600 dark:text-slate-400">
                            {{ $settings->vision ?? 'Menjadi marketplace sekolah yang modern, transparan, dan mendukung ekosistem digital warga sekolah.' }}
                        </p>

                    </div>


                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">

                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-800 text-white shadow-xs">
                            <i class="fa-solid fa-bullseye text-lg"></i>
                        </div>

                        <h3 class="mt-4 text-base font-bold text-slate-900 dark:text-white">
                            Misi Kami
                        </h3>

                        <p class="mt-2 text-xs leading-5 text-slate-600 dark:text-slate-400">
                            {{ $settings->mission ?? 'Mendorong jiwa wirausaha muda serta menciptakan kemudahan transaksi di lingkungan sekolah.' }}
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </section>

</x-layouts.app>