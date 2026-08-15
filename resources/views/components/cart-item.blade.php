@props(['item'])

<div class="rounded-2xl border border-slate-200 bg-white p-3 sm:p-4">

    <div class="flex gap-3 sm:gap-4">

        {{-- Image --}}
        <div class="h-20 w-20 shrink-0 overflow-hidden rounded-xl bg-slate-100 sm:h-24 sm:w-24">

            @if($item->product?->images?->first())

                <img
                    src="{{ asset('storage/' . $item->product->images->first()->image) }}"
                    alt="{{ $item->product->name }}"
                    class="h-full w-full object-cover"
                >

            @else

                <div class="flex h-full w-full items-center justify-center text-xs text-slate-400">
                    No Image
                </div>

            @endif

        </div>

        {{-- Product Info --}}
        <div class="min-w-0 flex-1">

            <a
                href="{{ route('products.show', $item->product) }}"
                class="line-clamp-2 text-sm font-semibold leading-5 text-slate-900 hover:text-slate-600 sm:text-base"
            >
                {{ $item->product->name }}
            </a>

            @if($item->product?->seller?->user)

                <p class="mt-1 truncate text-xs text-slate-500">
                    {{ $item->product->seller->user->name }}
                </p>

            @endif

            <p class="mt-2 text-sm font-bold text-slate-900">
                Rp {{ number_format($item->price ?? $item->product->price, 0, ',', '.') }}
            </p>

        </div>

    </div>

    {{-- Bottom --}}
    <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">

        <div class="flex items-center gap-2">

            <span class="text-xs text-slate-500">
                Jumlah
            </span>

            <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                {{ $item->quantity }}
            </span>

        </div>

        <p class="text-sm font-bold text-slate-900">
            Rp {{ number_format(($item->price ?? $item->product->price) * $item->quantity, 0, ',', '.') }}
        </p>

    </div>

</div>