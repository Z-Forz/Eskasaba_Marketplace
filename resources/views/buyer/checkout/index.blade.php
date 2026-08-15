<x-layouts.buyer title="Checkout">

    <div class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">

            <a
                href="{{ route('buyer.cart.index') }}"
                class="text-sm font-medium text-slate-500 transition hover:text-slate-900"
            >
                ← Kembali ke keranjang
            </a>

            <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                Checkout
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Periksa pesanan sebelum melakukan checkout.
            </p>

        </div>

        @if ($errors->any())
            <x-alert
                type="error"
                :message="$errors->first()"
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

            <form
                method="POST"
                action="{{ route('buyer.checkout.store') }}"
            >
                @csrf

                <div class="grid gap-6 lg:grid-cols-3">

                    {{-- Order --}}
                    <div class="space-y-6 lg:col-span-2">

                        @foreach ($cart->items->groupBy(fn ($item) => $item->product->seller_id) as $sellerId => $items)

                            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">

                                    <div class="flex items-center justify-between gap-4">

                                        <div>
                                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                                Penjual
                                            </p>

                                            <h2 class="mt-1 font-bold text-slate-900">
                                                {{ $items->first()->product->seller->user->name ?? 'Seller' }}
                                            </h2>
                                        </div>

                                        <x-badge type="seller">
                                            Seller
                                        </x-badge>

                                    </div>

                                </div>

                                <div class="divide-y divide-slate-100">

                                    @foreach ($items as $item)

                                        <div class="flex gap-4 p-5 sm:p-6">

                                            <div class="h-20 w-20 shrink-0 overflow-hidden rounded-2xl bg-slate-100 sm:h-24 sm:w-24">

                                                @if ($item->product->images->first())
                                                    <img
                                                        src="{{ Storage::url($item->product->images->first()->image) }}"
                                                        alt="{{ $item->product->name }}"
                                                        class="h-full w-full object-cover"
                                                    >
                                                @endif

                                            </div>

                                            <div class="min-w-0 flex-1">

                                                <h3 class="font-semibold text-slate-900">
                                                    {{ $item->product->name }}
                                                </h3>

                                                <p class="mt-1 text-sm text-slate-500">
                                                    {{ $item->quantity }} ×
                                                    Rp {{ number_format($item->price, 0, ',', '.') }}
                                                </p>

                                                <p class="mt-2 font-bold text-slate-900">
                                                    Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}
                                                </p>

                                            </div>

                                        </div>

                                    @endforeach

                                </div>

                            </div>

                        @endforeach

                        {{-- Pickup --}}
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

                            <h2 class="text-lg font-bold text-slate-900">
                                Pengambilan Pesanan
                            </h2>

                            <p class="mt-2 text-sm text-slate-500">
                                Semua pesanan menggunakan sistem pengambilan langsung.
                            </p>

                            <div class="mt-5 rounded-2xl bg-slate-50 p-4">

                                <div class="flex gap-3">

                                    <div class="mt-0.5 shrink-0">
                                        📍
                                    </div>

                                    <div>
                                        <p class="font-semibold text-slate-900">
                                            Pickup di sekolah
                                        </p>

                                        <p class="mt-1 text-sm leading-relaxed text-slate-500">
                                            Jadwal dan lokasi pengambilan akan
                                            ditentukan bersama seller setelah
                                            pesanan dikonfirmasi.
                                        </p>
                                    </div>

                                </div>

                            </div>

                        </div>

                        {{-- Note --}}
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

                            <h2 class="text-lg font-bold text-slate-900">
                                Catatan Pesanan
                            </h2>

                            <div class="mt-5">
                                <textarea
                                    name="note"
                                    rows="4"
                                    placeholder="Tambahkan catatan untuk seller jika diperlukan..."
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                                >{{ old('note') }}</textarea>
                            </div>

                        </div>

                    </div>

                    {{-- Summary --}}
                    <div>

                        <div class="sticky top-24 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

                            <h2 class="text-lg font-bold text-slate-900">
                                Ringkasan Pembayaran
                            </h2>

                            <div class="mt-6 space-y-4 text-sm">

                                <div class="flex justify-between gap-4">
                                    <span class="text-slate-500">
                                        Total Produk
                                    </span>

                                    <span class="font-medium text-slate-900">
                                        {{ $cart->items->sum('quantity') }}
                                    </span>
                                </div>

                                <div class="flex justify-between gap-4">
                                    <span class="text-slate-500">
                                        Subtotal
                                    </span>

                                    <span class="font-medium text-slate-900">
                                        Rp {{ number_format($cart->items->sum(fn ($item) => $item->quantity * $item->price), 0, ',', '.') }}
                                    </span>
                                </div>

                            </div>

                            <div class="my-6 border-t border-slate-100"></div>

                            <div class="flex items-end justify-between gap-4">

                                <span class="text-sm font-medium text-slate-500">
                                    Total
                                </span>

                                <span class="text-xl font-bold text-slate-900">
                                    Rp {{ number_format($cart->items->sum(fn ($item) => $item->quantity * $item->price), 0, ',', '.') }}
                                </span>

                            </div>

                            <button
                                type="submit"
                                class="mt-6 flex w-full items-center justify-center rounded-xl bg-slate-900 px-5 py-3.5 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:ring-offset-2"
                            >
                                Buat Pesanan
                            </button>

                            <p class="mt-4 text-center text-xs leading-relaxed text-slate-400">
                                Dengan melanjutkan, kamu menyetujui proses
                                pemesanan dan pengambilan barang sesuai
                                ketentuan Eskasaba Market.
                            </p>

                        </div>

                    </div>

                </div>

            </form>

        @else

            <x-empty-state
                title="Tidak ada produk untuk checkout"
                message="Keranjang kamu masih kosong. Tambahkan produk terlebih dahulu."
                action="{{ route('products.index') }}"
                actionText="Lihat Produk"
            />

        @endif

    </div>

</x-layouts.buyer>