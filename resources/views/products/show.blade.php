<x-layouts.app title="{{ $product->name }}">

    <section class="bg-slate-50 py-8 sm:py-12 dark:bg-slate-950">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <nav class="mb-6 flex items-center gap-2 overflow-x-auto whitespace-nowrap text-xs text-slate-500 sm:text-sm">
                <a href="{{ route('home') }}" class="transition hover:text-emerald-700 dark:hover:text-emerald-400 flex items-center gap-1">
                    <i class="fa-solid fa-house"></i> Beranda
                </a>

                <span>/</span>

                <a href="{{ route('products.index') }}" class="transition hover:text-emerald-700 dark:hover:text-emerald-400">
                    Produk
                </a>

                @if($product->category)
                    <span>/</span>
                    <a href="{{ route('products.index', ['category' => $product->category->id]) }}" class="font-semibold text-slate-800 dark:text-slate-200">
                        {{ $product->category->name }}
                    </a>
                @endif
            </nav>

            <div class="grid gap-8 lg:grid-cols-2 lg:gap-12">

                {{-- Image Gallery --}}
                <div x-data="{ activeImage: '{{ $product->images->first() ? asset('storage/' . $product->images->first()->image) : '' }}' }">

                    <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        @if($product->images->isNotEmpty())
                            <img
                                :src="activeImage || '{{ asset('storage/' . $product->images->first()->image) }}'"
                                alt="{{ $product->name }}"
                                class="aspect-square w-full object-cover transition-all duration-300"
                            >
                        @else
                            <div class="flex aspect-square items-center justify-center bg-slate-100 text-sm text-slate-400 dark:bg-slate-900 dark:text-slate-600">
                                <i class="fa-solid fa-image text-3xl"></i>
                            </div>
                        @endif
                    </div>

                    {{-- Gallery Thumbnails --}}
                    @if($product->images->count() > 1)
                        <div class="mt-4 flex gap-3 overflow-x-auto pb-2">
                            @foreach($product->images as $img)
                                @php $imgUrl = asset('storage/' . $img->image); @endphp
                                <button
                                    type="button"
                                    @click="activeImage = '{{ $imgUrl }}'"
                                    :class="activeImage === '{{ $imgUrl }}' ? 'border-emerald-600 ring-2 ring-emerald-200 dark:ring-emerald-950' : 'border-slate-200 dark:border-slate-800'"
                                    class="h-20 w-20 shrink-0 overflow-hidden rounded-2xl border bg-white transition hover:opacity-90 dark:bg-slate-900"
                                >
                                    <img src="{{ $imgUrl }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif

                </div>

                {{-- Product Info & Action --}}
                <div class="flex flex-col justify-between">

                    <div>
                        @if($product->category)
                            <a
                                href="{{ route('products.index', ['category' => $product->category->id]) }}"
                                class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300"
                            >
                                <i class="fa-solid fa-layer-group text-[10px]"></i> {{ $product->category->name }}
                            </a>
                        @endif

                        <h1 class="mt-3 text-2xl font-black leading-tight text-slate-900 dark:text-white sm:text-3xl lg:text-4xl">
                            {{ $product->name }}
                        </h1>

                        {{-- Rating & Seller info --}}
                        <div class="mt-4 flex flex-wrap items-center gap-3">
                            <x-rating
                                :rating="$product->reviews_avg_rating ?? 0"
                                :count="$product->reviews_count ?? 0"
                            />

                            @if($product->seller?->user)
                                <span class="text-slate-300 dark:text-slate-700">|</span>
                                <span class="text-xs font-semibold text-slate-600 dark:text-slate-400 sm:text-sm">
                                    <i class="fa-solid fa-store text-emerald-600 mr-1"></i> Toko: <strong class="text-slate-900 dark:text-white">{{ $product->seller->user->username }}</strong>
                                </span>
                            @endif
                        </div>

                        {{-- Price Card --}}
                        <div class="mt-6 rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                                <i class="fa-solid fa-tag text-emerald-600"></i> Harga Produk
                            </p>
                            <div class="mt-1 flex items-baseline gap-3">
                                <p class="text-3xl font-black text-emerald-700 dark:text-emerald-400">
                                    Rp {{ number_format($product->final_price ?? $product->price, 0, ',', '.') }}
                                </p>
                                @if(!empty($product->discount) && $product->discount > 0)
                                    <p class="text-sm font-bold text-slate-400 line-through">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Specs Grid --}}
                        <div class="mt-6 grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <div class="rounded-2xl border border-slate-200/80 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
                                <p class="text-xs font-medium text-slate-400 flex items-center gap-1">
                                    <i class="fa-solid fa-boxes-stacked"></i> Sisa Stok
                                </p>
                                <p class="mt-1 text-sm font-bold text-slate-900 dark:text-white">
                                    {{ $product->stock }} {{ $product->stock > 0 ? 'Pcs' : '(Habis)' }}
                                </p>
                            </div>

                            @if($product->condition)
                                <div class="rounded-2xl border border-slate-200/80 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
                                    <p class="text-xs font-medium text-slate-400 flex items-center gap-1">
                                        <i class="fa-solid fa-list"></i> Varian Tersedia
                                    </p>
                                    <p class="mt-1 text-sm font-bold text-emerald-700 dark:text-emerald-400">
                                        {{ $product->condition }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        {{-- Description --}}
                        <div class="mt-6">
                            <h2 class="text-sm font-bold text-slate-900 dark:text-white sm:text-base flex items-center gap-1.5">
                                <i class="fa-solid fa-align-left text-slate-500"></i> Deskripsi Produk
                            </h2>
                            <div class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                                {!! nl2br(e($product->description)) !!}
                            </div>
                        </div>

                        {{-- Seller Card --}}
                        @if($product->seller?->user)
                            <div class="mt-6 rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                                    <i class="fa-solid fa-store text-emerald-600"></i> Informasi Penjual
                                </p>

                                <div class="mt-3 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-slate-900 font-bold text-white shadow-xs">
                                            {{ strtoupper(substr($product->seller->user->username ?? 'S', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 dark:text-white text-sm">
                                                {{ $product->seller->user->username }}
                                            </p>
                                            <p class="text-xs text-emerald-700 dark:text-emerald-400 font-semibold flex items-center gap-1">
                                                <i class="fa-solid fa-circle-check"></i> Penjual Terverifikasi Sekolah
                                            </p>
                                        </div>
                                    </div>

                                    @if($product->seller->whatsapp_number)
                                        <a
                                            href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $product->seller->whatsapp_number) }}?text=Halo%20{{ urlencode($product->seller->user->username) }},%20saya%20tertarik%20dengan%20produk%20{{ urlencode($product->name) }}"
                                            target="_blank"
                                            class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 px-3.5 py-2 text-xs font-bold text-emerald-800 transition hover:bg-emerald-100 dark:bg-emerald-950/50 dark:text-emerald-300"
                                        >
                                            <i class="fa-brands fa-whatsapp text-sm"></i> Chat WA
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif

                    </div>

                    {{-- Add to Cart Form with Shopee-Style Dual-Engine Interactive Variant Selector --}}
                    @php
                        $flavors = !empty($product->condition) ? array_values(array_filter(array_map('trim', explode(',', $product->condition)))) : [];
                        $firstFlavor = count($flavors) > 0 ? $flavors[0] : '';
                        $isOwnProduct = auth()->check() && $product->seller && $product->seller->user_id === auth()->id();
                    @endphp

                    <div class="mt-8">

                        {{-- Shopee-Style Flavor Option Buttons (Interactive Pill Chips) --}}
                        @if(count($flavors) > 0 && ! $isOwnProduct)
                            <div
                                id="variant-selector-wrapper"
                                x-data="{ selectedFlavor: @js($firstFlavor) }"
                                class="mb-5 rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900"
                            >
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3 flex items-center justify-between">
                                    <span><i class="fa-solid fa-utensils text-emerald-600 mr-1.5"></i> Pilih Varian / Rasa Produk:</span>
                                    <span id="variant-label-display" class="text-xs text-emerald-700 dark:text-emerald-400 font-extrabold" x-text="selectedFlavor ? 'Varian: ' + selectedFlavor : ''">
                                        Varian: {{ $firstFlavor }}
                                    </span>
                                </label>

                                <div class="flex flex-wrap gap-2.5" id="variant-buttons-container">
                                    @foreach($flavors as $index => $flavor)
                                        <button
                                            type="button"
                                            data-flavor="{{ e($flavor) }}"
                                            @click="selectedFlavor = @js($flavor); document.getElementById('product_variant_note_input').value = @js($flavor)"
                                            onclick="window.selectVariantPill(this)"
                                            :class="selectedFlavor === @js($flavor)
                                                ? 'border-emerald-600 bg-emerald-50 text-emerald-900 ring-2 ring-emerald-200 dark:bg-emerald-950 dark:text-emerald-300 dark:ring-emerald-900 font-bold shadow-xs variant-active'
                                                : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 font-semibold'"
                                            class="variant-pill-btn inline-flex items-center gap-2 rounded-xl border px-4 py-2.5 text-xs transition cursor-pointer {{ $index === 0 ? 'border-emerald-600 bg-emerald-50 text-emerald-900 ring-2 ring-emerald-200 font-bold shadow-xs variant-active' : 'border-slate-200 bg-white text-slate-700 font-semibold' }}"
                                        >
                                            <i class="fa-solid fa-circle-dot text-[10px] pill-dot {{ $index === 0 ? 'text-emerald-600' : 'text-slate-300' }}" :class="selectedFlavor === @js($flavor) ? 'text-emerald-600' : 'text-slate-300'"></i>
                                            <span>{{ $flavor }}</span>
                                            <i class="fa-solid fa-check text-emerald-600 text-xs ml-1 font-bold pill-check {{ $index === 0 ? '' : 'hidden' }}" x-show="selectedFlavor === @js($flavor)"></i>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @auth
                            @if($isOwnProduct)
                                {{-- Own product notice --}}
                                <div class="rounded-3xl border border-amber-200 bg-amber-50 p-5 shadow-xs dark:border-amber-900/60 dark:bg-amber-950/40">
                                    <div class="flex items-center gap-3.5">
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-500 text-white shadow-xs">
                                            <i class="fa-solid fa-store text-xl"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-amber-900 dark:text-amber-300">
                                                Ini Adalah Produk Tokomu Sendiri
                                            </h3>
                                            <p class="mt-0.5 text-xs font-semibold text-amber-700 dark:text-amber-400">
                                                Kamu tidak dapat membeli atau menambahkan produk toko sendiri ke keranjang.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex gap-2">
                                        <a
                                            href="{{ route('seller.products.index') }}"
                                            class="inline-flex items-center gap-2 rounded-2xl bg-amber-700 px-4 py-2.5 text-xs font-bold text-white shadow-xs transition hover:bg-amber-800"
                                        >
                                            <i class="fa-solid fa-pen-to-square"></i> Kelola Produk Toko
                                        </a>
                                    </div>
                                </div>
                            @elseif($product->stock > 0)
                                <form
                                    action="{{ route('buyer.cart.store') }}"
                                    method="POST"
                                    class="space-y-4"
                                >
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" id="product_variant_note_input" name="note" value="{{ $firstFlavor }}">

                                    @if(count($flavors) === 0)
                                        <div class="mb-4">
                                            <label for="note" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5 flex items-center gap-1.5">
                                                <i class="fa-solid fa-pen-to-square text-emerald-600"></i> Catatan Pilihan Rasa (Opsional):
                                            </label>
                                            <input
                                                type="text"
                                                id="note"
                                                name="note"
                                                placeholder="Contoh: Rasa Cokelat, Extra Pedas, Original"
                                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                            >
                                        </div>
                                    @endif

                                    <div class="flex flex-col gap-3 sm:flex-row">
                                        <div class="w-full sm:w-32">
                                            <label for="quantity" class="sr-only">Jumlah</label>
                                            <input
                                                id="quantity"
                                                name="quantity"
                                                type="number"
                                                min="1"
                                                max="{{ $product->stock }}"
                                                value="1"
                                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-center text-sm font-bold outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                            >
                                        </div>

                                        <button
                                            type="submit"
                                            class="flex-1 flex items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-6 py-3.5 text-sm font-bold text-white shadow-md transition hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-400 cursor-pointer"
                                        >
                                            <i class="fa-solid fa-cart-shopping"></i> Tambah ke Keranjang
                                        </button>
                                    </div>
                                </form>
                            @else
                                <button
                                    disabled
                                    class="w-full rounded-2xl bg-slate-200 px-6 py-3.5 text-sm font-bold text-slate-500 dark:bg-slate-800 dark:text-slate-400 flex items-center justify-center gap-2"
                                >
                                    <i class="fa-solid fa-ban"></i> Stok Habis
                                </button>
                            @endif
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="flex w-full items-center justify-center gap-2 rounded-2xl bg-slate-900 px-6 py-3.5 text-sm font-bold text-white transition hover:bg-slate-800"
                            >
                                <i class="fa-solid fa-right-to-bracket"></i> Login untuk Membeli
                            </a>
                        @endauth
                    </div>

                </div>

            </div>

            {{-- Reviews Section --}}
            <div class="mt-16 border-t border-slate-200/80 pt-12 dark:border-slate-800">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white sm:text-2xl flex items-center gap-2">
                    <i class="fa-solid fa-star text-amber-400"></i> Ulasan Pembeli ({{ $product->reviews_count ?? $product->reviews->count() }})
                </h2>

                @if($product->reviews && $product->reviews->isNotEmpty())
                    <div class="mt-6 divide-y divide-slate-100 rounded-3xl border border-slate-200/80 bg-white shadow-xs dark:divide-slate-800 dark:border-slate-800 dark:bg-slate-900">
                        @foreach($product->reviews as $review)
                            <div class="p-5 sm:p-6">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-900 text-xs font-bold text-white">
                                            {{ strtoupper(substr($review->user?->username ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900 dark:text-white">
                                                {{ $review->user?->username ?? 'Pembeli' }}
                                            </p>
                                            <p class="text-xs text-slate-400">
                                                {{ $review->created_at?->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center text-amber-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span class="text-base">{{ $i <= $review->rating ? '★' : '☆' }}</span>
                                        @endfor
                                    </div>
                                </div>

                                @if($review->comment)
                                    <p class="mt-3 text-sm text-slate-700 dark:text-slate-300">
                                        "{{ $review->comment }}"
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="mt-6 rounded-3xl border border-slate-200/80 bg-white p-8 text-center dark:border-slate-800 dark:bg-slate-900">
                        <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada ulasan untuk produk ini.</p>
                    </div>
                @endif
            </div>

        </div>

    </section>

    {{-- Global Script for 100% Reliable Dual-Engine Variant Selector --}}
    <script>
        (function() {
            window.selectVariantPill = function(btn) {
                if (!btn) return;
                const flavor = btn.getAttribute('data-flavor');
                const hiddenInput = document.getElementById('product_variant_note_input');
                const labelDisplay = document.getElementById('variant-label-display');

                if (hiddenInput) hiddenInput.value = flavor;
                if (labelDisplay) labelDisplay.textContent = 'Varian: ' + flavor;

                const container = document.getElementById('variant-buttons-container');
                if (container) {
                    const allBtns = container.querySelectorAll('.variant-pill-btn');
                    allBtns.forEach(function(b) {
                        b.classList.remove('border-emerald-600', 'bg-emerald-50', 'text-emerald-900', 'ring-2', 'ring-emerald-200', 'font-bold', 'shadow-xs', 'variant-active');
                        b.classList.add('border-slate-200', 'bg-white', 'text-slate-700', 'font-semibold');

                        const dot = b.querySelector('.pill-dot');
                        if (dot) {
                            dot.classList.remove('text-emerald-600');
                            dot.classList.add('text-slate-300');
                        }
                        const check = b.querySelector('.pill-check');
                        if (check) {
                            check.classList.add('hidden');
                        }
                    });

                    btn.classList.remove('border-slate-200', 'bg-white', 'text-slate-700', 'font-semibold');
                    btn.classList.add('border-emerald-600', 'bg-emerald-50', 'text-emerald-900', 'ring-2', 'ring-emerald-200', 'font-bold', 'shadow-xs', 'variant-active');

                    const activeDot = btn.querySelector('.pill-dot');
                    if (activeDot) {
                        activeDot.classList.remove('text-slate-300');
                        activeDot.classList.add('text-emerald-600');
                    }
                    const activeCheck = btn.querySelector('.pill-check');
                    if (activeCheck) {
                        activeCheck.classList.remove('hidden');
                    }
                }
            };
        })();
    </script>

</x-layouts.app>