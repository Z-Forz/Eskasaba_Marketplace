@props(['item'])

@php
    $product = $item->product;
    $productName = $product?->name ?? 'Produk';
    $firstImage = $product?->images?->first()?->image;
    $unitPrice = (float) ($item->price ?? $product?->price ?? 0);
    $stock = (int) ($product?->stock ?? 99);
    $sellerName = $product?->seller?->user?->username;
@endphp

<div class="rounded-3xl border border-slate-200/80 bg-white p-4 sm:p-5 shadow-xs transition hover:border-slate-300 dark:border-slate-800 dark:bg-slate-900">

    <div class="flex gap-4 sm:gap-5">

        {{-- Product Image --}}
        <div class="h-24 w-24 shrink-0 overflow-hidden rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700 sm:h-28 sm:w-28">

            @if($firstImage)

                <img
                    src="{{ asset('storage/' . $firstImage) }}"
                    alt="{{ $productName }}"
                    class="h-full w-full object-cover"
                >

            @else

                <div class="flex h-full w-full flex-col items-center justify-center text-xs text-slate-400">
                    <i class="fa-solid fa-store text-xl opacity-40 text-emerald-600"></i>
                </div>

            @endif

        </div>

        {{-- Product Info & Selected Flavor Badge --}}
        <div class="min-w-0 flex-1 flex flex-col justify-between">

            <div>

                <div class="flex items-start justify-between gap-2">
                    @if($product)
                        <a
                            href="{{ route('products.show', $product) }}"
                            class="line-clamp-2 text-sm font-black leading-snug text-slate-900 hover:text-emerald-700 dark:text-white dark:hover:text-emerald-400 sm:text-base"
                        >
                            {{ $productName }}
                        </a>
                    @else
                        <span class="line-clamp-2 text-sm font-black text-slate-900 dark:text-white sm:text-base">
                            {{ $productName }}
                        </span>
                    @endif

                    {{-- Trigger Modal Hapus Button --}}
                    <button
                        type="button"
                        onclick="window.openDeleteCartModal({{ $item->id }})"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-red-50/80 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100 hover:text-red-700 transition dark:bg-red-950/40 dark:text-red-400 dark:hover:bg-red-900/60 cursor-pointer shrink-0 border border-red-200/60 dark:border-red-900/40"
                        title="Hapus dari Keranjang"
                    >
                        <i class="fa-solid fa-trash-can text-xs"></i>
                        <span>Hapus</span>
                    </button>
                </div>

                {{-- Selected Flavor / Variant Describe Badge --}}
                @if(!empty($item->variant_name) || !empty($item->note))
                    <div class="mt-2">
                        <span class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-100 px-3 py-1 text-xs font-extrabold text-emerald-900 border border-emerald-300/60 dark:bg-emerald-950/80 dark:text-emerald-300 dark:border-emerald-800">
                            <i class="fa-solid fa-layer-group text-[10px] text-emerald-600 dark:text-emerald-400"></i>
                            <span>Varian: <strong>{{ $item->variant_name ?: $item->note }}</strong></span>
                        </span>
                    </div>
                @else
                    <div class="mt-1">
                        <span class="text-[11px] font-semibold text-slate-400 italic">
                            (Tanpa varian khusus)
                        </span>
                    </div>
                @endif

                @if($sellerName)
                    <p class="mt-1.5 truncate text-xs font-semibold text-slate-500 dark:text-slate-400">
                        <i class="fa-solid fa-store text-emerald-600 mr-1"></i> Toko: {{ $sellerName }}
                    </p>
                @endif

            </div>

            {{-- Unit Price --}}
            <div class="mt-2">
                <p class="text-xs font-bold text-slate-400">Harga Satuan</p>
                <p class="text-sm font-black text-emerald-700 dark:text-emerald-400">
                    Rp {{ number_format($unitPrice, 0, ',', '.') }}
                </p>
            </div>

        </div>

    </div>

    {{-- Bottom Bar: Quantity Controls (+ / -) & Item Subtotal --}}
    <div class="mt-4 flex items-center justify-between border-t border-slate-100 dark:border-slate-800/80 pt-3">

        {{-- Increment / Decrement Quantity Form --}}
        <div class="flex items-center gap-3">
            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 hidden sm:inline">
                Jumlah:
            </span>

            <div class="flex items-center rounded-2xl border border-slate-200 bg-slate-50 p-1 dark:border-slate-700 dark:bg-slate-800">

                {{-- Decrement Form --}}
                @if($item->quantity > 1)
                    <form action="{{ route('buyer.cart.update', $item->id) }}" method="POST" class="inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="quantity" value="{{ $item->quantity - 1 }}">
                        <button
                            type="submit"
                            class="flex h-7 w-7 items-center justify-center rounded-xl bg-white text-slate-700 shadow-2xs hover:bg-slate-100 hover:text-emerald-700 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 transition font-bold cursor-pointer"
                            title="Kurangi 1"
                        >
                            <i class="fa-solid fa-minus text-[10px]"></i>
                        </button>
                    </form>
                @else
                    <button
                        type="button"
                        disabled
                        class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-600 cursor-not-allowed"
                    >
                        <i class="fa-solid fa-minus text-[10px]"></i>
                    </button>
                @endif

                {{-- Current Quantity --}}
                <span class="w-10 text-center text-xs font-black text-slate-900 dark:text-white">
                    {{ $item->quantity }}
                </span>

                {{-- Increment Form --}}
                @if($item->quantity < $stock)
                    <form action="{{ route('buyer.cart.update', $item->id) }}" method="POST" class="inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}">
                        <button
                            type="submit"
                            class="flex h-7 w-7 items-center justify-center rounded-xl bg-white text-slate-700 shadow-2xs hover:bg-slate-100 hover:text-emerald-700 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 transition font-bold cursor-pointer"
                            title="Tambah 1"
                        >
                            <i class="fa-solid fa-plus text-[10px]"></i>
                        </button>
                    </form>
                @else
                    <button
                        type="button"
                        disabled
                        class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-600 cursor-not-allowed"
                        title="Stok Maksimal Terpenuhi"
                    >
                        <i class="fa-solid fa-plus text-[10px]"></i>
                    </button>
                @endif

            </div>
        </div>

        {{-- Total Item Price --}}
        <div class="text-right">
            <span class="text-[10px] font-semibold text-slate-400 block">Subtotal Item</span>
            <p class="text-base font-black text-slate-900 dark:text-white sm:text-lg">
                Rp {{ number_format($unitPrice * $item->quantity, 0, ',', '.') }}
            </p>
        </div>

    </div>

    {{-- =========================================================
        CUSTOM MODAL KONFIRMASI HAPUS PRODUK DARI KERANJANG
    ========================================================== --}}
    <div
        id="delete-cart-item-modal-{{ $item->id }}"
        class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
    >
        {{-- Modal Backdrop --}}
        <div
            class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity"
            onclick="window.closeDeleteCartModal({{ $item->id }})"
        ></div>

        {{-- Modal Dialog Card --}}
        <div class="relative z-10 w-full max-w-md overflow-hidden rounded-3xl bg-white p-6 shadow-2xl dark:bg-slate-900 dark:border dark:border-slate-800 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex flex-col items-center text-center">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-red-100 text-red-600 dark:bg-red-950/60 dark:text-red-400 mb-4">
                    <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                </div>

                <h3 class="text-lg font-black text-slate-900 dark:text-white">
                    Hapus Produk dari Keranjang?
                </h3>

                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    Apakah kamu yakin ingin mengeluarkan produk <strong class="text-slate-800 dark:text-slate-200">"{{ $productName }}"</strong>
                    @if(!empty($item->note))
                        dengan varian <strong class="text-emerald-700 dark:text-emerald-400">({{ $item->note }})</strong>
                    @endif
                    dari keranjang belanjaanmu?
                </p>

                <div class="mt-6 flex w-full gap-3">
                    <button
                        type="button"
                        onclick="window.closeDeleteCartModal({{ $item->id }})"
                        class="flex-1 rounded-2xl border border-slate-200 bg-white py-3 text-xs font-bold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 transition cursor-pointer"
                    >
                        Batal
                    </button>

                    <form action="{{ route('buyer.cart.destroy', $item->id) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="w-full rounded-2xl bg-red-600 py-3 text-xs font-bold text-white shadow-xs hover:bg-red-700 transition cursor-pointer"
                        >
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

@once
<script>
    window.openDeleteCartModal = function (itemId) {
        const modal = document.getElementById('delete-cart-item-modal-' + itemId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    };
    window.closeDeleteCartModal = function (itemId) {
        const modal = document.getElementById('delete-cart-item-modal-' + itemId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    };
</script>
@endonce