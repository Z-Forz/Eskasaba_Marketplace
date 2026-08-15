<x-layouts.buyer title="Keranjang">

    <div class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        <div class="mb-8">
            <p class="text-sm font-medium text-slate-500">Belanja</p>

            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                Keranjang Saya
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Periksa produk yang ingin kamu beli sebelum checkout.
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

                {{-- Cart Items --}}
                <div class="space-y-4 lg:col-span-2">

                    @foreach ($cart->items as $item)
                        <x-cart-item
                            :item="$item"
                        />
                    @endforeach

                </div>

                {{-- Summary --}}
                <div class="lg:col-span-1">

                    <div class="sticky top-24 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

                        <h2 class="text-lg font-bold text-slate-900">
                            Ringkasan Belanja
                        </h2>

                        <div class="mt-6 space-y-4 text-sm">

                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">
                                    Jumlah Produk
                                </span>

                                <span class="font-medium text-slate-900">
                                    {{ $cart->items->sum('quantity') }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">
                                    Subtotal
                                </span>

                                <span class="font-semibold text-slate-900">
                                    Rp {{ number_format($cart->items->sum(fn ($item) => $item->quantity * $item->price), 0, ',', '.') }}
                                </span>
                            </div>

                        </div>

                        <div class="my-6 border-t border-slate-100"></div>

                        <div class="flex items-end justify-between gap-4">
                            <span class="text-sm text-slate-500">
                                Total
                            </span>

                            <span class="text-xl font-bold text-slate-900">
                                Rp {{ number_format($cart->items->sum(fn ($item) => $item->quantity * $item->price), 0, ',', '.') }}
                            </span>
                        </div>

                        <a
                            href="{{ route('buyer.checkout.index') }}"
                            class="mt-6 flex w-full items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
                        >
                            Lanjut Checkout
                        </a>

                        <a
                            href="{{ route('products.index') }}"
                            class="mt-3 flex w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                        >
                            Lanjut Belanja
                        </a>

                    </div>

                </div>

            </div>

        @else

            <x-empty-state
                title="Keranjang masih kosong"
                message="Belum ada produk yang kamu masukkan ke keranjang."
                action="{{ route('products.index') }}"
                actionText="Mulai Belanja"
            />

        @endif

    </div>

</x-layouts.buyer>