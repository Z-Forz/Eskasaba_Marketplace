<x-layouts.buyer title="Keranjang Belanja">

    <div class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 flex items-center gap-1.5">
                <i class="fa-solid fa-cart-shopping"></i> Keranjang Belanja Anda
            </p>

            <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-900 dark:text-white sm:text-3xl">
                Keranjang Saya
            </h1>

            <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">
                Periksa produk, varian rasa yang dipilih, serta jumlah item sebelum melanjutkan ke checkout (checkout hanya dapat dilakukan dari 1 toko per transaksi).
            </p>
        </div>

        @if ($cart && $cart->items->count())

            <div
                x-data="{
                    items: {{ json_encode($cart->items->map(function($i) {
                        return [
                            'id' => $i->id,
                            'seller_id' => $i->product?->seller_id ?? 0,
                            'seller_name' => $i->product?->seller?->user?->username ?? 'Toko',
                            'qty' => (int) $i->quantity,
                            'price' => (float) ($i->price ?? $i->product?->price ?? 0),
                        ];
                    })->values()) }},

                    selectedItems: {{ json_encode($cart->items->pluck('id')) }},

                    toggleItem(id) {
                        const idx = this.selectedItems.indexOf(id);
                        if (idx > -1) {
                            this.selectedItems.splice(idx, 1);
                        } else {
                            this.selectedItems.push(id);
                        }
                    },

                    isSelected(id) {
                        return this.selectedItems.includes(id);
                    },

                    get isAllSelected() {
                        return this.items.length > 0 && this.selectedItems.length === this.items.length;
                    },

                    toggleSelectAll() {
                        if (this.isAllSelected) {
                            this.selectedItems = [];
                        } else {
                            this.selectedItems = this.items.map(i => i.id);
                        }
                    },

                    get selectedSellers() {
                        const set = new Set();
                        this.items.forEach(i => {
                            if (this.selectedItems.includes(i.id)) {
                                set.add(i.seller_id);
                            }
                        });
                        return Array.from(set);
                    },

                    get hasMultipleSellers() {
                        return this.selectedSellers.length > 1;
                    },

                    get selectedTotalQty() {
                        let total = 0;
                        this.items.forEach(i => {
                            if (this.selectedItems.includes(i.id)) {
                                total += i.qty;
                            }
                        });
                        return total;
                    },

                    get selectedTotalPrice() {
                        let total = 0;
                        this.items.forEach(i => {
                            if (this.selectedItems.includes(i.id)) {
                                total += (i.qty * i.price);
                            }
                        });
                        return total;
                    },

                    formatRupiah(val) {
                        return 'Rp ' + Number(val).toLocaleString('id-ID');
                    },

                    onCartUpdated(detail) {
                        const item = this.items.find(i => i.id === detail.item_id);
                        if (item) {
                            item.qty = detail.item_quantity;
                        }
                    }
                }"
                @cart-updated.window="onCartUpdated($event.detail)"
                class="grid gap-6 lg:grid-cols-3"
            >

                {{-- Cart Items & Selection Area --}}
                <div class="space-y-4 lg:col-span-2">

                    {{-- Top Selection Control Bar --}}
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                        <label class="flex items-center gap-3 cursor-pointer select-none">
                            <input
                                type="checkbox"
                                :checked="isAllSelected"
                                @change="toggleSelectAll()"
                                class="h-5 w-5 rounded-lg border-slate-300 text-emerald-600 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-800 dark:focus:ring-emerald-600 cursor-pointer"
                            >
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200">
                                Pilih Semua Produk ({{ $cart->items->count() }} Item)
                            </span>
                        </label>

                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400">
                                Terpilih: <strong class="text-emerald-700 dark:text-emerald-400" x-text="selectedItems.length"></strong> dari {{ $cart->items->count() }} item
                            </span>
                        </div>
                    </div>

                    {{-- Multi-Seller Notice Banner --}}
                    <div
                        x-show="hasMultipleSellers"
                        x-transition
                        class="rounded-2xl border border-amber-300 bg-amber-50 p-4 text-xs text-amber-900 shadow-xs dark:border-amber-900/60 dark:bg-amber-950/40 dark:text-amber-300 flex items-start gap-3"
                    >
                        <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base shrink-0 mt-0.5"></i>
                        <div>
                            <p class="font-bold text-sm">Produk dari beberapa toko dipilih!</p>
                            <p class="mt-0.5 leading-relaxed">
                                Transaksi checkout hanya dapat dilakukan untuk <strong>1 toko dalam sekali pemesanan</strong>. Silakan hilangkan centang pada produk dari toko lain untuk melanjutkan checkout.
                            </p>
                        </div>
                    </div>

                    {{-- Cart Items List --}}
                    @foreach ($cart->items as $item)
                        <x-cart-item :item="$item" />
                    @endforeach

                </div>

                {{-- Summary Sidebar --}}
                <div class="lg:col-span-1">

                    <div class="lg:sticky lg:top-24 rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">

                        <h2 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-calculator text-emerald-600"></i> Ringkasan Belanja
                        </h2>

                        <div class="mt-6 space-y-4 text-sm">

                            <div class="flex items-center justify-between">
                                <span class="text-slate-500 dark:text-slate-400 font-semibold">
                                    Total Item Produk
                                </span>

                                <span class="font-black text-slate-900 dark:text-white">
                                    <span x-text="selectedTotalQty"></span> Pcs
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-slate-500 dark:text-slate-400 font-semibold">
                                    Subtotal Nilai Pesanan
                                </span>

                                <span class="font-black text-slate-900 dark:text-white" x-text="formatRupiah(selectedTotalPrice)">
                                    Rp 0
                                </span>
                            </div>

                        </div>

                        <div class="my-6 border-t border-slate-100 dark:border-slate-800"></div>

                        <div class="flex items-end justify-between gap-4">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Total Bayar
                            </span>

                            <span class="text-2xl font-black text-emerald-700 dark:text-emerald-400" x-text="formatRupiah(selectedTotalPrice)">
                                Rp 0
                            </span>
                        </div>

                        {{-- Checkout Form submitting GET with selected item IDs --}}
                        <form method="GET" action="{{ route('buyer.checkout.index') }}">
                            <template x-for="id in selectedItems" :key="id">
                                <input type="hidden" name="items[]" :value="id">
                            </template>

                            <button
                                type="submit"
                                :disabled="selectedItems.length === 0 || hasMultipleSellers"
                                :class="(selectedItems.length === 0 || hasMultipleSellers)
                                    ? 'bg-slate-300 text-slate-500 cursor-not-allowed dark:bg-slate-800 dark:text-slate-600'
                                    : 'bg-emerald-700 text-white hover:bg-emerald-800 shadow-md cursor-pointer'"
                                class="mt-6 flex w-full items-center justify-center gap-2 rounded-2xl px-5 py-3.5 text-sm font-bold transition"
                            >
                                <template x-if="selectedItems.length === 0">
                                    <span>Pilih Item untuk Checkout</span>
                                </template>
                                <template x-if="hasMultipleSellers">
                                    <span>Pilih 1 Toko Saja</span>
                                </template>
                                <template x-if="selectedItems.length > 0 && !hasMultipleSellers">
                                    <span class="flex items-center gap-2">
                                        <span>Lanjut Checkout</span>
                                        <i class="fa-solid fa-arrow-right text-xs"></i>
                                    </span>
                                </template>
                            </button>
                        </form>

                        <a
                            href="{{ route('products.index') }}"
                            class="mt-3 flex w-full items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-xs font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                        >
                            <i class="fa-solid fa-cart-plus"></i> Tambah Produk Lain
                        </a>

                    </div>

                </div>

            </div>

        @else

            <x-empty-state
                title="Keranjang masih kosong"
                description="Belum ada produk yang kamu masukkan ke keranjang."
                :action="route('products.index')"
                action-text="Mulai Belanja Sekarang"
            />

        @endif

    </div>

</x-layouts.buyer>