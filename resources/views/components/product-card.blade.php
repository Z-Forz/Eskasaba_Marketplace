@props(['product'])

@php
    $firstImage = $product->images->first()?->image;
    $hasDiscount = !empty($product->discount) && $product->discount > 0;
    $finalPrice = $product->final_price;
    $stock = (int) ($product->stock ?? 0);
    $isOutofStock = $stock <= 0;
    $sellerUser = $product->seller?->user;
    $flavors = !empty($product->condition) ? array_values(array_filter(array_map('trim', explode(',', $product->condition)))) : [];
    $descriptionExcerpt = !empty($product->description) ? Str::limit(strip_tags($product->description), 55) : null;

    // Accurate Rating Calculation
    $avgRating = isset($product->reviews_avg_rating)
        ? (float) $product->reviews_avg_rating
        : ($product->relationLoaded('reviews') && $product->reviews->count() > 0 ? (float) $product->reviews->avg('rating') : 0);

    $reviewsCount = isset($product->reviews_count)
        ? (int) $product->reviews_count
        : ($product->relationLoaded('reviews') ? $product->reviews->count() : 0);
@endphp

<article
    {{ $attributes->merge([
        'class' => 'group relative flex h-full flex-col overflow-hidden rounded-[2rem] border border-slate-200/90 bg-white shadow-xs transition-all duration-300 hover:-translate-y-2 hover:border-emerald-500/50 hover:shadow-2xl hover:shadow-emerald-900/10 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-emerald-600/60 dark:hover:shadow-emerald-950/40'
    ]) }}
>

    {{-- Image Container --}}
    <div class="relative aspect-4/3 w-full overflow-hidden bg-slate-100 dark:bg-slate-800/80">

        <a href="{{ route('products.show', $product) }}" class="block h-full w-full">
            @if($firstImage)
                <img
                    src="{{ asset('storage/' . $firstImage) }}"
                    alt="{{ $product->name }}"
                    loading="lazy"
                    class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-110"
                >
            @else
                <div class="flex h-full w-full flex-col items-center justify-center bg-gradient-to-br from-emerald-50 via-slate-50 to-slate-100 text-slate-400 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 dark:text-slate-500">
                    <i class="fa-solid fa-store text-3xl opacity-40 text-emerald-600"></i>
                    <span class="mt-2 text-xs font-semibold text-slate-400">Foto Belum Tersedia</span>
                </div>
            @endif

            {{-- Soft Gradient Overlay on Hover --}}
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
        </a>

        {{-- Top Left Badges (Stock & Variant Info) --}}
        <div class="pointer-events-none absolute left-3 top-3 z-10 flex flex-col gap-1.5 items-start">
            @if($isOutofStock)
                <span class="rounded-full bg-red-600/95 px-3 py-1 text-[10px] font-black tracking-wide text-white shadow-md backdrop-blur-md border border-red-400/30">
                    Stok Habis
                </span>
            @elseif($stock <= 5)
                <span class="rounded-full bg-amber-500/95 px-3 py-1 text-[10px] font-black tracking-wide text-white shadow-md backdrop-blur-md border border-amber-300/30">
                    Sisa {{ $stock }} Pcs
                </span>
            @else
                <span class="rounded-full bg-slate-950/80 px-3 py-1 text-[10px] font-extrabold tracking-wide text-white shadow-md backdrop-blur-md border border-white/20">
                    Stok: {{ $stock }}
                </span>
            @endif

            @if(count($flavors) > 0)
                <span class="rounded-full bg-emerald-950/85 px-2.5 py-0.5 text-[10px] font-extrabold tracking-wide text-emerald-300 shadow-md backdrop-blur-md border border-emerald-500/30 flex items-center gap-1">
                    <i class="fa-solid fa-utensils text-[9px] text-emerald-400"></i> {{ count($flavors) }} Rasa
                </span>
            @endif
        </div>

        {{-- Top Right Tilted Miring Discount Badge --}}
        @if($hasDiscount)
            <div class="pointer-events-none absolute right-3 top-3 z-20">
                <span class="inline-flex items-center gap-1 rounded-2xl bg-gradient-to-r from-red-600 via-rose-600 to-red-500 px-3 py-1 text-xs font-black tracking-wide text-white shadow-md border border-white/20">
                    <i class="fa-solid fa-tag text-[10px]"></i>
                    @if($product->discount <= 100)
                        -{{ number_format($product->discount, 0) }}%
                    @else
                        Diskon Rp {{ number_format($product->discount, 0, ',', '.') }}
                    @endif
                </span>
            </div>
        @endif

    </div>

    {{-- Card Body --}}
    <div class="flex flex-1 flex-col p-4 sm:p-5">

        {{-- Category & Rating Header --}}
        <div class="mb-2 flex items-center justify-between gap-2">
            @if($product->category)
                <span class="inline-flex items-center gap-1 rounded-xl bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-800 border border-emerald-200/60 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-900/60">
                    <i class="fa-solid fa-layer-group text-[9px]"></i> {{ $product->category->name }}
                </span>
            @else
                <span></span>
            @endif

            {{-- Accurate Rating Badge --}}
            @if($avgRating > 0)
                <div class="flex items-center gap-1 text-[11px] font-extrabold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-xl border border-amber-200/60 dark:bg-amber-950/40 dark:border-amber-900/40 dark:text-amber-300">
                    <i class="fa-solid fa-star text-[10px] text-amber-500"></i>
                    <span>{{ number_format($avgRating, 1) }}</span>
                    @if($reviewsCount > 0)
                        <span class="text-slate-400 font-semibold">({{ $reviewsCount }})</span>
                    @endif
                </div>
            @else
                <div class="flex items-center gap-1 text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-lg dark:bg-slate-800 dark:text-slate-400">
                    <i class="fa-regular fa-star text-[9px] text-slate-400"></i>
                    <span>Baru</span>
                </div>
            @endif
        </div>

        {{-- Product Name --}}
        <h3 class="min-h-[2.8rem] text-sm font-black text-slate-900 transition-colors duration-200 group-hover:text-emerald-700 dark:text-white dark:group-hover:text-emerald-400">
            <a href="{{ route('products.show', $product) }}" class="line-clamp-2 leading-snug">
                {{ $product->name }}
            </a>
        </h3>

        {{-- Product Description Snippet --}}
        @if($descriptionExcerpt)
            <p class="mt-1 text-xs leading-relaxed text-slate-500 line-clamp-2 dark:text-slate-400">
                {{ $descriptionExcerpt }}
            </p>
        @endif

        {{-- Flavors Chips Preview --}}
        @if(count($flavors) > 0)
            <div class="mt-2.5 flex flex-wrap gap-1">
                @foreach(array_slice($flavors, 0, 3) as $flv)
                    <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700">
                        <i class="fa-solid fa-tag text-[8px] text-emerald-600"></i> {{ $flv }}
                    </span>
                @endforeach
                @if(count($flavors) > 3)
                    <span class="text-[10px] font-extrabold text-slate-400 self-center">+{{ count($flavors) - 3 }}</span>
                @endif
            </div>
        @endif

        {{-- Price & Stock Level Detail --}}
        <div class="mt-4 flex items-baseline justify-between gap-2 border-t border-slate-100 pt-3 dark:border-slate-800/80">
            <div>
                <p class="text-lg font-black text-emerald-800 dark:text-emerald-400 sm:text-xl">
                    Rp {{ number_format($finalPrice, 0, ',', '.') }}
                </p>

                @if($hasDiscount)
                    <p class="text-xs text-slate-400 line-through font-semibold">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </p>
                @endif
            </div>

            {{-- Detailed Stock Indicator --}}
            <div class="text-right">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 block">
                    <i class="fa-solid fa-box text-emerald-600 mr-0.5"></i> Stok: <strong class="text-slate-900 dark:text-white">{{ $stock }} pcs</strong>
                </span>
            </div>
        </div>

        {{-- Seller Info & Role Footer --}}
        @if($sellerUser)
            <div class="mt-3 flex items-center justify-between pt-2">

                <div class="flex min-w-0 items-center gap-2">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center overflow-hidden rounded-full bg-emerald-900 text-[10px] font-black text-white shadow-2xs border border-emerald-400/30">
                        @if($sellerUser->avatar)
                            <img src="{{ Storage::url($sellerUser->avatar) }}" alt="{{ $sellerUser->username }}" class="h-full w-full object-cover">
                        @else
                            {{ strtoupper(substr($sellerUser->username ?? 'S', 0, 1)) }}
                        @endif
                    </div>

                    <div class="min-w-0">
                        <span class="truncate text-xs font-bold text-slate-800 dark:text-slate-200 block leading-tight">
                            {{ $sellerUser->username }}
                        </span>
                        <span class="text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 block">
                            {{ $sellerUser->role === 'teacher' ? 'Guru' : 'Siswa' }}
                        </span>
                    </div>
                </div>

                <a
                    href="{{ route('products.show', $product) }}"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-700 px-3.5 py-1.5 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-800 dark:bg-emerald-600 dark:hover:bg-emerald-700"
                    title="Lihat Detail Produk"
                >
                    <span>Beli</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>

            </div>
        @endif

    </div>

</article>