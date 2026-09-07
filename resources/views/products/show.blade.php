<x-layouts.app :title="$product->name">
    @php
        $hasVariants = $product->hasVariants();
        $variantsList = $hasVariants ? $product->variants : [];
        $firstVariant = $hasVariants && count($variantsList) > 0 ? $variantsList[0] : null;
        $initialPrice = $firstVariant ? $firstVariant['price'] : ($product->final_price ?? $product->price);
        $initialStock = $firstVariant && isset($firstVariant['stock']) ? $firstVariant['stock'] : $product->stock;

        $flavors = !empty($product->condition) ? array_values(array_filter(array_map('trim', explode(',', $product->condition)))) : [];
        $firstFlavor = count($flavors) > 0 ? $flavors[0] : '';
        $isOwnProduct = auth()->check() && $product->seller && $product->seller->user_id === auth()->id();
    @endphp

    <div class="py-8 sm:py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <nav class="mb-6 flex flex-wrap items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400">
                <a href="{{ route('home') }}" class="transition hover:text-emerald-700 dark:hover:text-emerald-400">
                    <i class="fa-solid fa-house mr-1"></i> Beranda
                </a>
                <span>/</span>
                <a href="{{ route('products.index') }}" class="transition hover:text-emerald-700 dark:hover:text-emerald-400">
                    Katalog Produk
                </a>
                @if($product->category)
                    <span>/</span>
                    <a href="{{ route('products.index', ['category' => $product->category->id]) }}" class="font-bold text-slate-900 dark:text-white transition hover:text-emerald-700">
                        {{ $product->category->name }}
                    </a>
                @endif
                <span>/</span>
                <span class="truncate max-w-[200px] text-slate-400">{{ $product->name }}</span>
            </nav>

            {{-- Main Product Layout --}}
            <div
                class="grid gap-8 lg:grid-cols-2 lg:gap-12 items-start"
                x-data="{
                    activeImage: '{{ $product->images->first() ? asset('storage/' . $product->images->first()->image) : '' }}',
                    activeVariant: @js($firstVariant),
                    activePrice: @js($initialPrice),
                    activeStock: @js($initialStock),
                    selectedFlavor: @js($firstVariant ? $firstVariant['name'] : $firstFlavor)
                }"
            >

                {{-- Left: Image Gallery (Responsif & Alami, Tidak Kaku Ngotak) --}}
                <div class="space-y-4">
                    <div class="relative min-h-[320px] max-h-[500px] w-full overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-100/70 p-3 shadow-xs dark:border-slate-800 dark:bg-slate-900/60 flex items-center justify-center">
                        @if($product->images->isNotEmpty())
                            <img
                                :src="activeImage || '{{ asset('storage/' . $product->images->first()->image) }}'"
                                alt="{{ $product->name }}"
                                class="max-h-[460px] w-auto max-w-full rounded-2xl object-contain shadow-xs transition-all duration-300 hover:scale-[1.02]"
                            >
                        @else
                            <div class="flex h-72 w-full flex-col items-center justify-center text-slate-400 dark:text-slate-600">
                                <i class="fa-solid fa-store text-4xl mb-2 text-emerald-600 opacity-50"></i>
                                <span class="text-xs font-semibold text-slate-500">Foto produk belum diunggah</span>
                            </div>
                        @endif

                        {{-- Diskon Badge Overlay --}}
                        @if(!$hasVariants && !empty($product->discount) && $product->discount > 0)
                            <div class="absolute right-4 top-4 z-10">
                                <span class="inline-flex items-center gap-1 rounded-2xl bg-gradient-to-r from-red-600 to-rose-600 px-3 py-1.5 text-xs font-black text-white shadow-md border border-white/20">
                                    <i class="fa-solid fa-tag text-[10px]"></i> Hemat Rp {{ number_format($product->discount, 0, ',', '.') }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Thumbnails --}}
                    @if($product->images->count() > 1)
                        <div class="flex gap-3 overflow-x-auto pb-1">
                            @foreach($product->images as $img)
                                @php $imgUrl = asset('storage/' . $img->image); @endphp
                                <button
                                    type="button"
                                    @click="activeImage = '{{ $imgUrl }}'"
                                    :class="activeImage === '{{ $imgUrl }}' ? 'border-emerald-600 ring-2 ring-emerald-200 dark:ring-emerald-950 font-bold' : 'border-slate-200 dark:border-slate-800 opacity-70 hover:opacity-100'"
                                    class="h-20 w-20 shrink-0 overflow-hidden rounded-2xl border bg-white p-1 transition dark:bg-slate-900 cursor-pointer"
                                >
                                    <img src="{{ $imgUrl }}" alt="{{ $product->name }}" class="h-full w-full rounded-xl object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Right: Product Info & Action Box --}}
                <div class="flex flex-col gap-6">

                    <div>
                        {{-- Category Badge --}}
                        @if($product->category)
                            <a
                                href="{{ route('products.index', ['category' => $product->category->id]) }}"
                                class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3.5 py-1 text-xs font-bold text-emerald-800 border border-emerald-200/60 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-900/60"
                            >
                                <i class="fa-solid fa-layer-group text-[10px]"></i> {{ $product->category->name }}
                            </a>
                        @endif

                        {{-- Product Name --}}
                        <h1 class="mt-3 text-2xl font-black leading-tight text-slate-900 dark:text-white sm:text-3xl lg:text-4xl">
                            {{ $product->name }}
                        </h1>

                        {{-- Rating & Store Owner Header --}}
                        <div class="mt-4 flex flex-wrap items-center gap-3">
                            <x-rating
                                :rating="$product->reviews_avg_rating ?? 0"
                                :count="$product->reviews_count ?? 0"
                            />

                            @if($product->seller?->user)
                                <span class="text-slate-300 dark:text-slate-700">|</span>
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                    <i class="fa-solid fa-store text-emerald-600"></i> Toko:
                                    <span class="text-slate-900 dark:text-white font-extrabold">{{ $product->seller->user->username }}</span>
                                    <span class="rounded-md bg-emerald-100 px-1.5 py-0.5 text-[10px] font-bold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                        {{ $product->seller->user->role === 'teacher' ? 'Guru' : 'Siswa' }}
                                    </span>
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Price Card Showcase --}}
                    <div class="rounded-3xl border border-slate-200/90 bg-gradient-to-br from-white via-slate-50/50 to-emerald-50/30 p-5 sm:p-6 shadow-xs dark:border-slate-800 dark:from-slate-900 dark:via-slate-900 dark:to-emerald-950/20">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                            <i class="fa-solid fa-tags text-emerald-600"></i> Harga Produk
                        </p>
                        <div class="mt-2 flex flex-col gap-1">
                            <div class="flex items-baseline gap-3 flex-wrap">
                                <p class="text-3xl sm:text-4xl font-black text-emerald-800 dark:text-emerald-400">
                                    @if($hasVariants)
                                        <span x-text="'Rp ' + Number(activePrice).toLocaleString('id-ID')">
                                            Rp {{ number_format($initialPrice, 0, ',', '.') }}
                                        </span>
                                    @else
                                        Rp {{ number_format($product->final_price ?? $product->price, 0, ',', '.') }}
                                    @endif
                                </p>
                                @if(!$hasVariants && !empty($product->discount) && $product->discount > 0)
                                    <p class="text-sm font-bold text-slate-400 line-through">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </p>
                                @endif
                            </div>
                            @if($hasVariants)
                                <p class="mt-1 text-xs font-extrabold text-emerald-700 dark:text-emerald-400 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-info"></i>
                                    <span>Harga menyesuaikan varian rasa/ukuran yang kamu pilih di bawah ini.</span>
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Description Block --}}
                    <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white sm:text-base flex items-center gap-2 border-b border-slate-100 pb-3 dark:border-slate-800">
                            <i class="fa-solid fa-align-left text-emerald-600"></i> Deskripsi & Detail Produk
                        </h2>
                        <div class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </div>

                    {{-- Size / Variant Selection Pills --}}
                    @if($hasVariants && ! $isOwnProduct)
                        <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3 flex items-center justify-between">
                                <span><i class="fa-solid fa-layer-group text-emerald-600 mr-1.5"></i> Pilih Rasa / Ukuran:</span>
                                <span class="text-xs text-emerald-700 dark:text-emerald-400 font-extrabold" x-text="activeVariant ? 'Pilihan: ' + activeVariant.name + ' (Rp ' + Number(activeVariant.price).toLocaleString('id-ID') + ')' : ''"></span>
                            </label>

                            <div class="flex flex-wrap gap-2.5">
                                @foreach($variantsList as $index => $varItem)
                                    @php
                                        $vStock = isset($varItem['stock']) ? (int) $varItem['stock'] : (int) $product->stock;
                                    @endphp
                                    <button
                                        type="button"
                                        @click="activeVariant = @js($varItem); activePrice = @js($varItem['price']); activeStock = @js($vStock); selectedFlavor = @js($varItem['name']);"
                                        :class="activeVariant && activeVariant.name === @js($varItem['name'])
                                            ? 'border-emerald-600 bg-emerald-50 text-emerald-900 ring-2 ring-emerald-200 dark:bg-emerald-950 dark:text-emerald-300 dark:ring-emerald-900 font-bold shadow-xs'
                                            : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 font-semibold'"
                                        class="inline-flex items-center gap-2 rounded-2xl border px-4 py-2.5 text-xs transition cursor-pointer"
                                    >
                                        <i class="fa-solid fa-circle-dot text-[10px]" :class="activeVariant && activeVariant.name === @js($varItem['name']) ? 'text-emerald-600' : 'text-slate-300'"></i>
                                        <span>{{ $varItem['name'] }}</span>
                                        <span class="text-[11px] font-bold text-emerald-700 dark:text-emerald-400">(Rp {{ number_format($varItem['price'], 0, ',', '.') }})</span>
                                        <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded {{ $vStock > 0 ? 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' : 'bg-red-100 text-red-600 dark:bg-red-950 dark:text-red-300' }}">
                                            {{ $vStock > 0 ? 'Stok: ' . $vStock : 'Habis' }}
                                        </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Additional Flavor Pills --}}
                    @if(count($flavors) > 0 && ! $isOwnProduct)
                        <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3 flex items-center justify-between">
                                <span><i class="fa-solid fa-utensils text-emerald-600 mr-1.5"></i> Pilih Rasa / Option Tambahan:</span>
                                <span class="text-xs text-emerald-700 dark:text-emerald-400 font-extrabold" x-text="selectedFlavor ? 'Pilihan: ' + selectedFlavor : ''"></span>
                            </label>

                            <div class="flex flex-wrap gap-2.5">
                                @foreach($flavors as $index => $flavor)
                                    <button
                                        type="button"
                                        @click="selectedFlavor = @js($flavor); if(document.getElementById('product_variant_note_input')) document.getElementById('product_variant_note_input').value = @js($flavor)"
                                        :class="selectedFlavor === @js($flavor)
                                            ? 'border-emerald-600 bg-emerald-50 text-emerald-900 ring-2 ring-emerald-200 dark:bg-emerald-950 dark:text-emerald-300 dark:ring-emerald-900 font-bold shadow-xs'
                                            : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 font-semibold'"
                                        class="inline-flex items-center gap-2 rounded-2xl border px-4 py-2.5 text-xs transition cursor-pointer"
                                    >
                                        <i class="fa-solid fa-circle-dot text-[10px]" :class="selectedFlavor === @js($flavor) ? 'text-emerald-600' : 'text-slate-300'"></i>
                                        <span>{{ $flavor }}</span>
                                        <i class="fa-solid fa-check text-emerald-600 text-xs ml-1 font-bold" x-show="selectedFlavor === @js($flavor)"></i>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Add to Cart Form / Buy Options --}}
                    @auth
                        @if($isOwnProduct)
                            <div class="rounded-3xl border border-amber-200 bg-amber-50 p-5 shadow-xs dark:border-amber-900/60 dark:bg-amber-950/40">
                                <div class="flex items-center gap-3.5">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-amber-500 text-white shadow-xs">
                                        <i class="fa-solid fa-store text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-black text-amber-900 dark:text-amber-300">
                                            Ini Adalah Produk Tokomu Sendiri
                                        </h3>
                                        <p class="mt-0.5 text-xs font-semibold text-amber-700 dark:text-amber-400">
                                            Kamu dapat mengedit stok, varian rasa, atau harga via Dashboard Seller.
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <a
                                        href="{{ route('seller.products.edit', $product) }}"
                                        class="inline-flex items-center gap-2 rounded-2xl bg-amber-700 px-4 py-2.5 text-xs font-bold text-white shadow-xs transition hover:bg-amber-800"
                                    >
                                        <i class="fa-solid fa-pen-to-square"></i> Kelola & Edit Produk
                                    </a>
                                </div>
                            </div>
                        @else
                            <form
                                action="{{ route('buyer.cart.store') }}"
                                method="POST"
                                class="space-y-4"
                            >
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="variant_name" :value="activeVariant ? activeVariant.name : selectedFlavor">
                                <input type="hidden" id="product_variant_note_input" name="note" :value="activeVariant ? activeVariant.name : selectedFlavor">

                                @if(count($flavors) === 0 && !$hasVariants)
                                    <div>
                                        <label for="note" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5 flex items-center gap-1.5">
                                            <i class="fa-solid fa-pen-to-square text-emerald-600"></i> Catatan Pesanan (Opsional):
                                        </label>
                                        <input
                                            type="text"
                                            id="note"
                                            name="note"
                                            placeholder="Contoh: Rasa Cokelat, Extra Pedas, Pedas Sedang"
                                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                        >
                                    </div>
                                @endif

                                <div class="flex flex-col gap-3 sm:flex-row">
                                    <div class="w-full sm:w-36">
                                        <label for="quantity" class="sr-only">Jumlah</label>
                                        <input
                                            id="quantity"
                                            name="quantity"
                                            type="number"
                                            min="1"
                                            :max="activeStock"
                                            value="1"
                                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-center text-sm font-bold outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                        >
                                    </div>

                                    <button
                                        type="submit"
                                        :disabled="activeStock <= 0"
                                        :class="activeStock > 0 ? 'bg-emerald-700 hover:bg-emerald-800 cursor-pointer' : 'bg-slate-300 dark:bg-slate-800 text-slate-500 cursor-not-allowed'"
                                        class="flex-1 flex items-center justify-center gap-2 rounded-2xl px-6 py-3.5 text-sm font-bold text-white shadow-md transition focus:outline-none focus:ring-2 focus:ring-emerald-400"
                                    >
                                        <i class="fa-solid fa-cart-shopping" x-show="activeStock > 0"></i>
                                        <span x-text="activeStock > 0 ? 'Tambah ke Keranjang' : 'Stok Varian Habis'">Tambah ke Keranjang</span>
                                    </button>
                                </div>
                            </form>
                        @endif
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="flex w-full items-center justify-center gap-2 rounded-2xl bg-slate-900 px-6 py-3.5 text-sm font-bold text-white transition hover:bg-slate-800"
                        >
                            <i class="fa-solid fa-right-to-bracket"></i> Login untuk Membeli
                        </a>
                    @endauth

                    {{-- Store Seller Card Info --}}
                    @if($product->seller?->user)
                        <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                                <i class="fa-solid fa-store text-emerald-600"></i> Informasi Penjual & Pemilik Toko
                            </p>

                            <div class="mt-3 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-950 font-black text-white shadow-xs border border-emerald-400/30">
                                        {{ strtoupper(substr($product->seller->user->username ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 dark:text-white text-sm">
                                            {{ $product->seller->user->username }}
                                        </p>
                                        <p class="text-xs text-emerald-700 dark:text-emerald-400 font-semibold flex items-center gap-1">
                                            <i class="fa-solid fa-circle-check"></i> Penjual Terverifikasi Sekolah ({{ $product->seller->user->role === 'teacher' ? 'Guru' : 'Siswa' }})
                                        </p>
                                    </div>
                                </div>

                                @if($product->seller->whatsapp_number)
                                    <a
                                        href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $product->seller->whatsapp_number) }}?text=Halo%20{{ urlencode($product->seller->user->username) }},%20saya%20tertarik%20dengan%20produk%20{{ urlencode($product->name) }}"
                                        target="_blank"
                                        class="inline-flex items-center gap-1.5 rounded-2xl bg-emerald-50 px-4 py-2.5 text-xs font-bold text-emerald-800 transition hover:bg-emerald-100 dark:bg-emerald-950/50 dark:text-emerald-300"
                                    >
                                        <i class="fa-brands fa-whatsapp text-sm text-emerald-600"></i> Tanya Penjual
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                </div>

            </div>

            {{-- Reviews Section --}}
            <div x-data="{ selectedRating: 'all' }" class="mt-16 border-t border-slate-200/80 pt-12 dark:border-slate-800">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white sm:text-2xl flex items-center gap-2">
                        <i class="fa-solid fa-star text-amber-400"></i> Ulasan Pembeli ({{ $product->reviews_count ?? $product->reviews->count() }})
                    </h2>
                </div>

                @if($product->reviews && $product->reviews->isNotEmpty())
                    @php
                        $totalReviews = $product->reviews_count ?? $product->reviews->count();
                        $avgRating = number_format($product->reviews_avg_rating ?? ($totalReviews > 0 ? $product->reviews->avg('rating') : 0), 1);
                        
                        $ratingCounts = [
                            5 => $product->reviews->where('rating', 5)->count(),
                            4 => $product->reviews->where('rating', 4)->count(),
                            3 => $product->reviews->where('rating', 3)->count(),
                            2 => $product->reviews->where('rating', 2)->count(),
                            1 => $product->reviews->where('rating', 1)->count(),
                        ];
                    @endphp

                    {{-- Summary Rating Header Card --}}
                    <div class="mt-6 rounded-3xl border border-slate-200/80 bg-gradient-to-br from-white to-slate-50/60 p-6 sm:p-8 shadow-xs dark:border-slate-800 dark:from-slate-900 dark:to-slate-900/60">
                        <div class="grid grid-cols-1 gap-8 md:grid-cols-12 md:items-center">
                            <!-- Average Score Column -->
                            <div class="text-center md:col-span-4 md:border-r md:border-slate-200/80 md:pr-8 dark:md:border-slate-800">
                                <p class="text-5xl font-black tracking-tight text-slate-900 dark:text-white">
                                    {{ $avgRating }} <span class="text-lg font-bold text-slate-400">/ 5.0</span>
                                </p>
                                <div class="mt-2 flex justify-center gap-1 text-amber-400 text-lg">
                                    @for($star = 1; $star <= 5; $star++)
                                        <i class="fa-solid fa-star {{ $star <= round($avgRating) ? 'text-amber-400' : 'text-slate-200 dark:text-slate-700' }}"></i>
                                    @endfor
                                </div>
                                <p class="mt-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                    Berdasarkan {{ $totalReviews }} ulasan pembeli terverifikasi
                                </p>
                            </div>

                            <!-- Rating Distribution Progress Bars (Clickable Filters) -->
                            <div class="space-y-1.5 md:col-span-8">
                                @for($star = 5; $star >= 1; $star--)
                                    @php
                                        $count = $ratingCounts[$star];
                                        $percent = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                                    @endphp
                                    <button
                                        type="button"
                                        @click="selectedRating = selectedRating === '{{ $star }}' ? 'all' : '{{ $star }}'"
                                        class="flex w-full items-center gap-3 text-xs rounded-xl px-2 py-1.5 transition hover:bg-slate-200/50 dark:hover:bg-slate-800/60 text-left group cursor-pointer"
                                        :class="selectedRating === '{{ $star }}' ? 'bg-amber-500/10 dark:bg-amber-500/20 ring-1 ring-amber-400/50' : ''"
                                    >
                                        <span class="flex w-12 items-center gap-1 font-bold text-slate-700 dark:text-slate-300 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition">
                                            {{ $star }} <i class="fa-solid fa-star text-[10px] text-amber-400"></i>
                                        </span>
                                        <div class="h-2.5 flex-1 overflow-hidden rounded-full bg-slate-200/70 dark:bg-slate-800">
                                            <div class="h-full rounded-full bg-gradient-to-r from-amber-400 to-amber-500 transition-all duration-500" style="width: {{ $percent }}%"></div>
                                        </div>
                                        <span class="w-16 text-right font-semibold text-slate-500 dark:text-slate-400 group-hover:text-slate-800 dark:group-hover:text-slate-200">
                                            {{ $count }} ({{ $percent }}%)
                                        </span>
                                    </button>
                                @endfor
                            </div>
                        </div>
                    </div>

                    {{-- Rating Filter Tabs (All, 5 Star, 4 Star, etc) --}}
                    <div class="mt-6 flex flex-wrap items-center gap-2">
                        <span class="mr-1 text-xs font-bold text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <i class="fa-solid fa-filter text-emerald-600"></i> Filter Rating:
                        </span>

                        <button
                            type="button"
                            @click="selectedRating = 'all'"
                            class="inline-flex items-center gap-1.5 rounded-2xl px-4 py-2 text-xs font-bold transition shadow-2xs cursor-pointer"
                            :class="selectedRating === 'all' ? 'bg-emerald-700 text-white shadow-emerald-700/20 ring-2 ring-emerald-700' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/80 dark:bg-slate-900 dark:text-slate-200 dark:border-slate-800 dark:hover:bg-slate-800'"
                        >
                            Semua Ulasan ({{ $totalReviews }})
                        </button>

                        @for($star = 5; $star >= 1; $star--)
                            @php $count = $ratingCounts[$star]; @endphp
                            <button
                                type="button"
                                @click="selectedRating = '{{ $star }}'"
                                class="inline-flex items-center gap-1.5 rounded-2xl px-3.5 py-2 text-xs font-bold transition shadow-2xs cursor-pointer"
                                :class="selectedRating === '{{ $star }}' ? 'bg-amber-500 text-white shadow-amber-500/20 ring-2 ring-amber-500' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/80 dark:bg-slate-900 dark:text-slate-200 dark:border-slate-800 dark:hover:bg-slate-800'"
                            >
                                <span>{{ $star }}</span>
                                <i class="fa-solid fa-star text-[10px]" :class="selectedRating === '{{ $star }}' ? 'text-white' : 'text-amber-400'"></i>
                                <span class="rounded-full px-1.5 py-0.5 text-[10px]" :class="selectedRating === '{{ $star }}' ? 'bg-white/25 text-white font-bold' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300'">
                                    {{ $count }}
                                </span>
                            </button>
                        @endfor
                    </div>

                    {{-- Review Cards Grid --}}
                    <div class="mt-6 grid grid-cols-1 gap-4 sm:gap-6 md:grid-cols-2">
                        @foreach($product->reviews as $review)
                            @php
                                $colors = [
                                    'from-emerald-500 to-teal-600',
                                    'from-indigo-500 to-purple-600',
                                    'from-amber-500 to-orange-600',
                                    'from-sky-500 to-blue-600',
                                    'from-rose-500 to-pink-600',
                                ];
                                $colorClass = $colors[abs(crc32($review->user?->username ?? 'User')) % count($colors)];
                            @endphp
                            <div
                                x-show="selectedRating === 'all' || selectedRating == {{ $review->rating }}"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                class="flex flex-col justify-between rounded-3xl border border-slate-200/80 bg-white p-6 shadow-2xs transition hover:border-emerald-200 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-slate-700"
                            >
                                <div>
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br {{ $colorClass }} font-bold text-white shadow-xs text-sm">
                                                {{ strtoupper(substr($review->user?->username ?? 'U', 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-900 dark:text-white">
                                                    {{ $review->user?->username ?? 'Pembeli' }}
                                                </p>
                                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 mt-0.5">
                                                    <i class="fa-solid fa-circle-check text-[9px]"></i> Pembeli Terverifikasi
                                                </span>
                                            </div>
                                        </div>

                                        <span class="text-[11px] font-medium text-slate-400 shrink-0">
                                            {{ $review->created_at?->diffForHumans() }}
                                        </span>
                                    </div>

                                    <div class="mt-3 flex items-center gap-1 text-amber-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fa-solid fa-star text-xs {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-200 dark:text-slate-700' }}"></i>
                                        @endfor
                                    </div>

                                    @if($review->comment)
                                        <p class="mt-3 text-sm text-slate-600 dark:text-slate-300 leading-relaxed font-normal bg-slate-50/80 dark:bg-slate-800/40 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800/80">
                                            "{{ $review->comment }}"
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Empty State when selected filter has 0 reviews --}}
                    @php
                        $availableRatings = $product->reviews->pluck('rating')->toArray();
                    @endphp
                    <div
                        x-show="selectedRating !== 'all' && !{{ json_encode($availableRatings) }}.includes(parseInt(selectedRating))"
                        x-cloak
                        class="mt-6 rounded-3xl border border-slate-200/80 bg-white p-8 text-center shadow-xs dark:border-slate-800 dark:bg-slate-900"
                    >
                        <i class="fa-solid fa-star-half-stroke text-3xl text-amber-400/70 mb-2"></i>
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-200">
                            Belum ada ulasan untuk rating <span x-text="selectedRating" class="text-amber-500"></span> Bintang.
                        </p>
                        <button
                            type="button"
                            @click="selectedRating = 'all'"
                            class="mt-3 inline-flex items-center gap-1.5 rounded-2xl bg-emerald-700 px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-emerald-800 transition cursor-pointer"
                        >
                            <i class="fa-solid fa-rotate-left text-[10px]"></i> Tampilkan Semua Ulasan
                        </button>
                    </div>
                @else
                    <div class="mt-6 rounded-3xl border border-slate-200/80 bg-white p-8 text-center shadow-xs dark:border-slate-800 dark:bg-slate-900">
                        <i class="fa-regular fa-star text-3xl text-slate-300 dark:text-slate-700"></i>
                        <p class="mt-2 text-sm font-bold text-slate-700 dark:text-slate-300">Belum ada ulasan untuk produk ini.</p>
                        <p class="mt-0.5 text-xs text-slate-400">Jadilah yang pertama membeli dan memberikan ulasan!</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-layouts.app>