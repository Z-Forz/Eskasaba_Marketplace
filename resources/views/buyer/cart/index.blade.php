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
                Periksa produk, varian rasa yang dipilih, serta jumlah item sebelum melanjutkan ke checkout.
            </p>
        </div>

        @if (session('success'))
            <x-alert
                type="success"
                :message="session('success')"
                class="mb-6"
            />
        @endif

        @if (session('error'))
            <x-alert
                type="error"
                :message="session('error')"
                class="mb-6"
            />
        @endif

        @if ($cart && $cart->items->count())

            <div class="grid gap-6 lg:grid-cols-3">

                {{-- Cart Items List --}}
                <div class="space-y-4 lg:col-span-2">

                    @foreach ($cart->items as $item)
                        <x-cart-item
                            :item="$item"
                        />
                    @endforeach

                </div>

                {{-- Summary Sidebar --}}
                <div class="lg:col-span-1">

                    <div class="sticky top-24 rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">

                        <h2 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-calculator text-emerald-600"></i> Ringkasan Belanja
                        </h2>

                        <div class="mt-6 space-y-4 text-sm">

                            <div class="flex items-center justify-between">
                                <span class="text-slate-500 dark:text-slate-400 font-semibold">
                                    Total Item Produk
                                </span>

                                <span class="font-black text-slate-900 dark:text-white">
                                    {{ $cart->items->sum('quantity') }} Pcs
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-slate-500 dark:text-slate-400 font-semibold">
                                    Subtotal Nilai Pesanan
                                </span>

                                <span class="font-black text-slate-900 dark:text-white">
                                    Rp {{ number_format($cart->items->sum(fn ($item) => $item->quantity * ($item->price ?? $item->product->price)), 0, ',', '.') }}
                                </span>
                            </div>

                        </div>

                        <div class="my-6 border-t border-slate-100 dark:border-slate-800"></div>

                        <div class="flex items-end justify-between gap-4">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Total Bayar
                            </span>

                            <span class="text-2xl font-black text-emerald-700 dark:text-emerald-400">
                                Rp {{ number_format($cart->items->sum(fn ($item) => $item->quantity * ($item->price ?? $item->product->price)), 0, ',', '.') }}
                            </span>
                        </div>

                        <a
                            href="{{ route('buyer.checkout.index') }}"
                            class="mt-6 flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-5 py-3.5 text-sm font-bold text-white shadow-md transition hover:bg-emerald-800"
                        >
                            <span>Lanjut Checkout</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>

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