<x-layouts.buyer title="Detail Keranjang">

    <div class="mx-auto w-full max-w-4xl px-4 py-8 sm:px-6 lg:px-8">

        <div class="mb-8">
            <a
                href="{{ route('buyer.cart.index') }}"
                class="text-sm font-medium text-slate-500 transition hover:text-slate-900"
            >
                ← Kembali ke keranjang
            </a>

            <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                Detail Item
            </h1>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

            @if ($item->product)

                <div class="flex flex-col gap-6 sm:flex-row">

                    <div class="h-48 w-full shrink-0 overflow-hidden rounded-2xl bg-slate-100 sm:h-40 sm:w-40">
                        @if ($item->product->images->first())
                            <img
                                src="{{ Storage::url($item->product->images->first()->image) }}"
                                alt="{{ $item->product->name }}"
                                class="h-full w-full object-cover"
                            >
                        @else
                            <div class="flex h-full items-center justify-center text-sm text-slate-400">
                                Tidak ada gambar
                            </div>
                        @endif
                    </div>

                    <div class="flex-1">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Produk
                        </p>

                        <h2 class="mt-1 text-xl font-bold text-slate-900">
                            {{ $item->product->name }}
                        </h2>

                        <p class="mt-3 text-lg font-bold text-slate-900">
                            Rp {{ number_format($item->price, 0, ',', '.') }}
                        </p>

                        <p class="mt-2 text-sm text-slate-500">
                            Jumlah: {{ $item->quantity }}
                        </p>
                    </div>

                </div>

            @endif

        </div>

    </div>

</x-layouts.buyer>