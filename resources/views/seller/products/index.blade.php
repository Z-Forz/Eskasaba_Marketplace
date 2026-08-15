<x-layouts.seller title="Produk Saya">

    <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

            <div>
                <p class="text-sm font-medium text-slate-500">
                    Seller
                </p>

                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    Produk Saya
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Kelola seluruh produk yang kamu jual.
                </p>
            </div>

            <a
                href="{{ route('seller.products.create') }}"
                class="inline-flex w-full items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 sm:w-auto"
            >
                + Tambah Produk
            </a>

        </div>


        {{-- Success --}}
        @if (session('success'))

            <x-alert
                type="success"
                :message="session('success')"
                class="mb-6"
            />

        @endif


        {{-- Search / Filter --}}
        <div class="mb-6 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">

            <form
                method="GET"
                action="{{ route('seller.products.index') }}"
                class="grid gap-3 md:grid-cols-[1fr_auto_auto]"
            >

                <input
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari produk..."
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                >

                <select
                    name="status"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                >
                    <option value="">
                        Semua Status
                    </option>

                    <option
                        value="active"
                        @selected(request('status') === 'active')
                    >
                        Aktif
                    </option>

                    <option
                        value="inactive"
                        @selected(request('status') === 'inactive')
                    >
                        Nonaktif
                    </option>

                    <option
                        value="out_of_stock"
                        @selected(request('status') === 'out_of_stock')
                    >
                        Habis
                    </option>
                </select>

                <button
                    type="submit"
                    class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
                >
                    Cari
                </button>

            </form>

        </div>


        {{-- Products --}}
        @if ($products->count())

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">

                @foreach ($products as $product)

                    <div class="group relative">

                        <x-product-card :product="$product" />

                        <div class="mt-3 flex gap-2">

                            <a
                                href="{{ route('seller.products.show', $product) }}"
                                class="flex-1 rounded-xl border border-slate-200 px-3 py-2 text-center text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
                            >
                                Detail
                            </a>

                            <a
                                href="{{ route('seller.products.edit', $product) }}"
                                class="flex-1 rounded-xl bg-slate-900 px-3 py-2 text-center text-xs font-semibold text-white transition hover:bg-slate-800"
                            >
                                Edit
                            </a>

                        </div>

                    </div>

                @endforeach

            </div>


            @if ($products->hasPages())

                <div class="mt-8">
                    {{ $products->withQueryString()->links() }}
                </div>

            @endif

        @else

            <x-empty-state
                title="Belum ada produk"
                message="Tambahkan produk pertamamu untuk mulai berjualan."
                action="{{ route('seller.products.create') }}"
                actionText="Tambah Produk"
            />

        @endif

    </div>

</x-layouts.seller>