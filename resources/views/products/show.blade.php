<x-layouts.app>

    <section class="bg-slate-50 py-8 sm:py-12">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <nav class="mb-6 flex items-center gap-2 overflow-x-auto whitespace-nowrap text-xs text-slate-500 sm:text-sm">

                <a
                    href="{{ route('home') }}"
                    class="hover:text-slate-900"
                >
                    Beranda
                </a>

                <span>/</span>

                <a
                    href="{{ route('products.index') }}"
                    class="hover:text-slate-900"
                >
                    Produk
                </a>

                @if($product->category)

                    <span>/</span>

                    <span class="text-slate-700">
                        {{ $product->category->name }}
                    </span>

                @endif

            </nav>


            <div class="grid gap-8 lg:grid-cols-2 lg:gap-12">

                {{-- =================================================
                    IMAGE
                ================================================== --}}
                <div>

                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white">

                        @if($product->images->isNotEmpty())

                            <img
                                src="{{ asset('storage/' . $product->images->first()->image) }}"
                                alt="{{ $product->name }}"
                                class="aspect-square w-full object-cover"
                            >

                        @else

                            <div class="flex aspect-square items-center justify-center bg-slate-100 text-sm text-slate-400">
                                Tidak ada gambar
                            </div>

                        @endif

                    </div>


                    {{-- Gallery --}}
                    @if($product->images->count() > 1)

                        <div class="mt-3 grid grid-cols-5 gap-2">

                            @foreach($product->images->take(5) as $image)

                                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">

                                    <img
                                        src="{{ asset('storage/' . $image->image) }}"
                                        alt="{{ $product->name }}"
                                        class="aspect-square w-full object-cover"
                                    >

                                </div>

                            @endforeach

                        </div>

                    @endif

                </div>


                {{-- =================================================
                    PRODUCT INFORMATION
                ================================================== --}}
                <div>

                    @if($product->category)

                        <a
                            href="{{ route('products.index', ['category' => $product->category->id]) }}"
                            class="text-xs font-semibold uppercase tracking-wider text-slate-500 hover:text-slate-900"
                        >
                            {{ $product->category->name }}
                        </a>

                    @endif


                    <h1 class="mt-2 text-2xl font-bold leading-tight tracking-tight text-slate-900 sm:text-3xl lg:text-4xl">
                        {{ $product->name }}
                    </h1>


                    {{-- Rating --}}
                    <div class="mt-4 flex flex-wrap items-center gap-3">

                        <x-rating
                            :rating="$product->reviews_avg_rating ?? 0"
                            :count="$product->reviews_count ?? 0"
                        />

                        @if($product->seller?->user)

                            <span class="text-xs text-slate-400">
                                |
                            </span>

                            <span class="text-xs text-slate-500 sm:text-sm">
                                {{ $product->seller->user->name }}
                            </span>

                        @endif

                    </div>


                    {{-- Price --}}
                    <div class="mt-6">

                        <p class="text-2xl font-bold text-slate-900 sm:text-3xl">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </p>

                    </div>


                    {{-- Product Info --}}
                    <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3">

                        <div class="rounded-2xl border border-slate-200 bg-white p-4">

                            <p class="text-xs text-slate-400">
                                Stok
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-900">
                                {{ $product->stock }}
                            </p>

                        </div>


                        @if($product->condition)

                            <div class="rounded-2xl border border-slate-200 bg-white p-4">

                                <p class="text-xs text-slate-400">
                                    Kondisi
                                </p>

                                <p class="mt-1 text-sm font-semibold capitalize text-slate-900">
                                    {{ $product->condition }}
                                </p>

                            </div>

                        @endif


                        @if($product->weight)

                            <div class="rounded-2xl border border-slate-200 bg-white p-4">

                                <p class="text-xs text-slate-400">
                                    Berat
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-900">
                                    {{ number_format($product->weight, 2, ',', '.') }} kg
                                </p>

                            </div>

                        @endif

                    </div>


                    {{-- Description --}}
                    <div class="mt-8">

                        <h2 class="text-sm font-bold text-slate-900 sm:text-base">
                            Deskripsi
                        </h2>

                        <div class="mt-3 text-sm leading-7 text-slate-600">
                            {!! nl2br(e($product->description)) !!}
                        </div>

                    </div>


                    {{-- Seller --}}
                    @if($product->seller?->user)

                        <div class="mt-8 rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">

                            <p class="text-xs text-slate-400">
                                Dijual oleh
                            </p>

                            <div class="mt-3 flex items-center gap-3">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-900 text-sm font-bold text-white">
                                    {{ strtoupper(substr($product->seller->user->name, 0, 1)) }}
                                </div>

                                <div class="min-w-0">

                                    <p class="truncate text-sm font-semibold text-slate-900">
                                        {{ $product->seller->user->name }}
                                    </p>

                                    @if($product->seller->user->username)

                                        <p class="truncate text-xs text-slate-500">
                                            {{ '@' . $product->seller->user->username }}
                                        </p>

                                    @endif

                                </div>

                            </div>

                        </div>

                    @endif


                    {{-- Action --}}
                    <div class="mt-8">

                        @auth

                            @if($product->stock > 0)

                                <form
                                    action="{{ route('buyer.cart.store') }}"
                                    method="POST"
                                    class="flex flex-col gap-3 sm:flex-row"
                                >

                                    @csrf

                                    <input
                                        type="hidden"
                                        name="product_id"
                                        value="{{ $product->id }}"
                                    >

                                    <div class="w-full sm:w-28">

                                        <label
                                            for="quantity"
                                            class="sr-only"
                                        >
                                            Jumlah
                                        </label>

                                        <input
                                            id="quantity"
                                            name="quantity"
                                            type="number"
                                            min="1"
                                            max="{{ $product->stock }}"
                                            value="1"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-center text-sm font-semibold outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                                        >

                                    </div>

                                    <x-button
                                        type="submit"
                                        class="flex-1"
                                    >
                                        Tambah ke Keranjang
                                    </x-button>

                                </form>

                            @else

                                <x-button
                                    disabled
                                    class="w-full"
                                >
                                    Stok Habis
                                </x-button>

                            @endif

                        @else

                            <x-button
                                :href="route('login')"
                                class="w-full"
                            >
                                Login untuk Membeli
                            </x-button>

                        @endauth

                    </div>

                </div>

            </div>


            {{-- =================================================
                REVIEWS
            ================================================== --}}
            <div class="mt-12 border-t border-slate-200 pt-10 sm:mt-16 sm:pt-12">

                <div class="flex items-end justify-between gap-4">

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Ulasan
                        </p>

                        <h2 class="mt-2 text-2xl font-bold text-slate-900">
                            Review Pembeli
                        </h2>

                    </div>

                </div>


                @if($product->reviews?->isNotEmpty())

                    <div class="mt-8 space-y-4">

                        @foreach($product->reviews as $review)

                            <div class="rounded-2xl border border-slate-200 bg-white p-5">

                                <div class="flex items-start justify-between gap-4">

                                    <div class="flex items-center gap-3">

                                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-700">
                                            {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                                        </div>

                                        <div>

                                            <p class="text-sm font-semibold text-slate-900">
                                                {{ $review->user->name ?? 'User' }}
                                            </p>

                                            <x-rating
                                                :rating="$review->rating"
                                                size="xs"
                                            />

                                        </div>

                                    </div>

                                    @if($review->created_at)

                                        <span class="text-xs text-slate-400">
                                            {{ $review->created_at->format('d M Y') }}
                                        </span>

                                    @endif

                                </div>


                                @if($review->comment)

                                    <p class="mt-4 text-sm leading-6 text-slate-600">
                                        {{ $review->comment }}
                                    </p>

                                @endif

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="mt-8">

                        <x-empty-state
                            title="Belum ada review"
                            description="Belum ada pembeli yang memberikan ulasan untuk produk ini."
                        />

                    </div>

                @endif

            </div>

        </div>

    </section>

</x-layouts.app>