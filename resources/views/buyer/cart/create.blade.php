<x-layouts.buyer title="Tambah ke Keranjang">

    <div class="mx-auto flex min-h-[60vh] w-full max-w-2xl items-center px-4 py-8 sm:px-6 lg:px-8">

        <div class="w-full rounded-3xl border border-slate-200 bg-white p-6 text-center shadow-sm sm:p-10">

            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-2xl">
                🛒
            </div>

            <h1 class="mt-6 text-2xl font-bold text-slate-900">
                Tambahkan Produk
            </h1>

            @isset($product)
                <p class="mx-auto mt-3 max-w-md text-sm leading-relaxed text-slate-500">
                    Kamu akan menambahkan
                    <span class="font-semibold text-slate-900">
                        {{ $product->name }}
                    </span>
                    ke dalam keranjang.
                </p>

                <form
                    method="POST"
                    action="{{ route('buyer.cart.store') }}"
                    class="mx-auto mt-8 max-w-sm"
                >
                    @csrf

                    <input
                        type="hidden"
                        name="product_id"
                        value="{{ $product->id }}"
                    >

                    <x-input
                        name="quantity"
                        label="Jumlah"
                        type="number"
                        min="1"
                        value="1"
                        required
                    />

                    <x-button
                        type="submit"
                        class="mt-5 w-full"
                    >
                        Tambahkan ke Keranjang
                    </x-button>
                </form>
            @else
                <p class="mt-3 text-sm text-slate-500">
                    Pilih produk terlebih dahulu.
                </p>

                <a
                    href="{{ route('products.index') }}"
                    class="mt-6 inline-flex rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
                >
                    Lihat Produk
                </a>
            @endisset

        </div>

    </div>

</x-layouts.buyer>