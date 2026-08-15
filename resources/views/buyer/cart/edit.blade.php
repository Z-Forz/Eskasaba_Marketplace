<x-layouts.buyer title="Edit Keranjang">

    <div class="mx-auto w-full max-w-xl px-4 py-8 sm:px-6 lg:px-8">

        <div class="mb-8">
            <a
                href="{{ route('buyer.cart.index') }}"
                class="text-sm font-medium text-slate-500 transition hover:text-slate-900"
            >
                ← Kembali ke keranjang
            </a>

            <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900">
                Ubah Jumlah
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Sesuaikan jumlah produk yang ingin kamu beli.
            </p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

            @if ($item->product)

                <div class="mb-6 flex items-center gap-4">

                    <div class="h-20 w-20 shrink-0 overflow-hidden rounded-2xl bg-slate-100">

                        @if ($item->product->images->first())
                            <img
                                src="{{ Storage::url($item->product->images->first()->image) }}"
                                alt="{{ $item->product->name }}"
                                class="h-full w-full object-cover"
                            >
                        @endif

                    </div>

                    <div class="min-w-0">
                        <h2 class="truncate font-bold text-slate-900">
                            {{ $item->product->name }}
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Rp {{ number_format($item->price, 0, ',', '.') }}
                        </p>
                    </div>

                </div>

            @endif

            <form
                method="POST"
                action="{{ route('buyer.cart.update', $item->cart_id) }}"
                class="space-y-5"
            >
                @csrf
                @method('PUT')

                <x-input
                    name="quantity"
                    label="Jumlah Produk"
                    type="number"
                    min="1"
                    :value="old('quantity', $item->quantity)"
                    required
                />

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

                    <a
                        href="{{ route('buyer.cart.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Batal
                    </a>

                    <x-button type="submit">
                        Simpan Perubahan
                    </x-button>

                </div>

            </form>

        </div>

    </div>

</x-layouts.buyer>