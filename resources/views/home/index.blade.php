<x-layouts.app>

    {{-- =========================================================
        HERO
    ========================================================== --}}
    <section class="relative overflow-hidden bg-slate-950">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8 lg:py-28">

            <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16">

                {{-- Hero Content --}}
                <div class="max-w-2xl">

                    <span class="inline-flex items-center rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-semibold text-slate-300">
                        Marketplace Resmi Sekolah
                    </span>

                    <h1 class="mt-5 text-3xl font-bold leading-tight tracking-tight text-white sm:text-4xl lg:text-6xl">
                        {{ $settings->hero_title ?? 'Selamat Datang di Eskasaba Market' }}
                    </h1>

                    <p class="mt-5 max-w-xl text-sm leading-6 text-slate-300 sm:text-base sm:leading-7">
                        {{ $settings->hero_description ?? 'Marketplace internal sekolah untuk memudahkan warga sekolah melakukan transaksi jual beli dengan aman dan nyaman.' }}
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">

                        <x-button
                            :href="route('products.index')"
                        >
                            Mulai Belanja

                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6"
                                />
                            </svg>
                        </x-button>

                        <x-button
                            variant="ghost"
                            :href="route('products.index')"
                            class="text-white! hover:bg-white/10!"
                        >
                            Jelajahi Produk
                        </x-button>

                    </div>

                </div>

                {{-- Hero Image --}}
                <div class="relative">

                    <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-2xl">

                        @if(!empty($settings?->hero_image))

                            <img
                                src="{{ asset('storage/' . $settings->hero_image) }}"
                                alt="{{ $settings->hero_title ?? 'Eskasaba Market' }}"
                                class="aspect-4/3 w-full object-cover"
                            >

                        @else

                            <div class="flex aspect-4/3 items-center justify-center bg-linear-to-br from-slate-800 to-slate-900">
                                <div class="text-center">

                                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 text-white">
                                        <svg
                                            class="h-8 w-8"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1.5"
                                                d="M3 7l9-4 9 4v10l-9 4-9-4V7zm0 0l9 4 9-4M12 11v10"
                                            />
                                        </svg>
                                    </div>

                                    <p class="mt-4 text-sm font-medium text-slate-300">
                                        {{ $settings->website_name ?? 'Eskasaba Market' }}
                                    </p>

                                </div>
                            </div>

                        @endif

                    </div>

                </div>

            </div>

        </div>
    </section>


    {{-- =========================================================
        FEATURES
    ========================================================== --}}
    <section class="border-b border-slate-100 bg-white">
        <div class="mx-auto grid max-w-7xl grid-cols-1 divide-y divide-slate-100 px-4 sm:grid-cols-3 sm:divide-x sm:divide-y-0 sm:px-6 lg:px-8">

            <div class="flex items-center gap-4 px-0 py-6 sm:px-6 lg:py-8">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100">
                    <svg class="h-5 w-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.7"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-7a8 8 0 10-16 0v7a2 2 0 002 2zm6-11a2 2 0 100 4 2 2 0 000-4z"
                        />
                    </svg>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-slate-900">
                        Aman & Terpercaya
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Marketplace khusus lingkungan sekolah.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-4 px-0 py-6 sm:px-6 lg:py-8">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100">
                    <svg class="h-5 w-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.7"
                            d="M3 7h18M5 7v12h14V7M8 7V5a4 4 0 018 0v2"
                        />
                    </svg>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-slate-900">
                        Produk Sekolah
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Produk dari siswa dan guru.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-4 px-0 py-6 sm:px-6 lg:py-8">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100">
                    <svg class="h-5 w-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.7"
                            d="M12 21a9 9 0 100-18 9 9 0 000 18zm0-13v4l3 2"
                        />
                    </svg>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-slate-900">
                        Praktis
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Pesan dan ambil langsung di sekolah.
                    </p>
                </div>
            </div>

        </div>
    </section>


    {{-- =========================================================
        CATEGORIES
    ========================================================== --}}
    <section class="bg-slate-50 py-14 sm:py-16">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="flex items-end justify-between gap-4">

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                        Jelajahi
                    </p>

                    <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                        Kategori Produk
                    </h2>
                </div>

                <a
                    href="{{ route('products.index') }}"
                    class="hidden text-sm font-semibold text-slate-700 hover:text-slate-950 sm:inline-flex"
                >
                    Lihat Semua
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
    <section class="bg-white py-14 sm:py-16">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="flex items-end justify-between gap-4">

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                        Pilihan terbaru
                    </p>

                    <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                        Produk Terbaru
                    </h2>
                </div>

                <a
                    href="{{ route('products.index') }}"
                    class="hidden text-sm font-semibold text-slate-700 hover:text-slate-950 sm:inline-flex"
                >
                    Lihat Semua
                </a>

            </div>


            @if($products->isNotEmpty())

                <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-5 lg:grid-cols-4">

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

                <x-button
                    variant="secondary"
                    :href="route('products.index')"
                >
                    Lihat Semua Produk
                </x-button>

            </div>

        </div>

    </section>


    {{-- =========================================================
        ABOUT
    ========================================================== --}}
    <section class="bg-slate-50 py-14 sm:py-16">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="grid gap-10 lg:grid-cols-2 lg:items-center">

                <div>

                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                        Tentang Kami
                    </p>

                    <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                        {{ $settings->website_name ?? 'Eskasaba Market' }}
                    </h2>

                </div>

                <div>

                    <p class="text-sm leading-7 text-slate-600 sm:text-base">
                        {{ $settings->about ?? 'Eskasaba Market merupakan marketplace internal sekolah yang menjadi wadah bagi siswa dan guru untuk melakukan transaksi jual beli secara mudah, aman, dan terpercaya.' }}
                    </p>

                </div>

            </div>

        </div>

    </section>


    {{-- =========================================================
        VISION & MISSION
    ========================================================== --}}
    <section class="bg-white py-14 sm:py-16">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="grid gap-5 md:grid-cols-2">

                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8">

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-900 text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.7"
                                d="M2.5 12S6 5 12 5s9.5 7 9.5 7-3.5 7-9.5 7-9.5-7-9.5-7z"
                            />
                            <circle cx="12" cy="12" r="3" stroke-width="1.7"/>
                        </svg>
                    </div>

                    <h3 class="mt-5 text-lg font-bold text-slate-900">
                        Visi
                    </h3>

                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        {{ $settings->vision ?? 'Menjadi marketplace sekolah yang modern, aman, dan terpercaya.' }}
                    </p>

                </div>


                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8">

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-900 text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.7"
                                d="M5 12h14M12 5l7 7-7 7"
                            />
                        </svg>
                    </div>

                    <h3 class="mt-5 text-lg font-bold text-slate-900">
                        Misi
                    </h3>

                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        {{ $settings->mission ?? 'Mendukung kewirausahaan di lingkungan sekolah melalui platform digital.' }}
                    </p>

                </div>

            </div>

        </div>

    </section>

</x-layouts.app>