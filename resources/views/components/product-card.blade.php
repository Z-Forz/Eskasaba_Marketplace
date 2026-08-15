@props(['product'])

<article
    class="group flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white transition duration-300 hover:-translate-y-1 hover:shadow-xl"
>

    {{-- Product Image --}}
    <a
        href="{{ route('products.show', $product) }}"
        class="block overflow-hidden bg-slate-100"
    >

        @if($product->images->first())
            <img
                src="{{ asset('storage/' . $product->images->first()->image) }}"
                alt="{{ $product->name }}"
                loading="lazy"
                class="aspect-square w-full object-cover transition duration-500 group-hover:scale-105"
            >
        @else
            <div class="flex aspect-square w-full items-center justify-center text-sm text-slate-400">
                Tidak ada gambar
            </div>
        @endif

    </a>

    <div class="flex flex-1 flex-col p-3.5 sm:p-4">

        {{-- Category --}}
        @if($product->category)
            <p class="truncate text-xs font-medium text-slate-400">
                {{ $product->category->name }}
            </p>
        @endif

        {{-- Product Name --}}
        <a
            href="{{ route('products.show', $product) }}"
            class="mt-1 line-clamp-2 min-h-10 text-sm font-semibold leading-5 text-slate-900 transition hover:text-slate-600"
        >
            {{ $product->name }}
        </a>

        {{-- Price --}}
        <div class="mt-3">

            <p class="text-base font-bold text-slate-900 sm:text-lg">
                Rp {{ number_format($product->price, 0, ',', '.') }}
            </p>

            @if($product->condition)
                <p class="mt-1 text-xs text-slate-500">
                    {{ ucfirst($product->condition) }}
                </p>
            @endif

        </div>

        {{-- Seller --}}
        @if($product->seller?->user)

            <div class="mt-auto flex min-w-0 items-center gap-2 border-t border-slate-100 pt-3">

                <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-900 text-[10px] font-bold text-white">
                    {{ strtoupper(substr($product->seller->user->name, 0, 1)) }}
                </div>

                <span class="truncate text-xs text-slate-500">
                    {{ $product->seller->user->name }}
                </span>

            </div>

        @endif

    </div>

</article>