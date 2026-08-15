{{-- resources/views/products/index.blade.php --}}
<x-layouts.app>
    <div class="container">
        <h1>Products index</h1>
    </div>
</x-layouts.app>
<x-layouts.app>

    <section class="bg-slate-50 py-10 sm:py-14">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div>

                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                    Marketplace
                </p>

                <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    Semua Produk
                </h1>

                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                    Temukan berbagai produk yang tersedia dari seller di lingkungan sekolah.
                </p>

            </div>


            {{-- Filter --}}
            <div class="mt-8 rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">

                <form
                    action="{{ route('products.index') }}"
                    method="GET"
                    class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4"
                >

                    {{-- Search --}}
                    <div class="sm:col-span-2">

                        <x-input
                            name="search"
                            label="Cari Produk"
                            placeholder="Cari nama produk..."
                            :value="request('search')"
                        />

                    </div>


                    {{-- Category --}}
                    <div class="space-y-1.5">

                        <label
                            for="category"
                            class="block text-sm font-medium text-slate-700"
                        >
                            Kategori
                        </label>

                        <select
                            id="category"
                            name="category"
                            class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                        >

                            <option value="">
                                Semua Kategori
                            </option>

                            @foreach($categories as $category)

                                <option
                                    value="{{ $category->id }}"
                                    @selected(request('category') == $category->id)
                                >
                                    {{ $category->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Sort --}}
                    <div class="space-y-1.5">

                        <label
                            for="sort"
                            class="block text-sm font-medium text-slate-700"
                        >
                            Urutkan
                        </label>

                        <select
                            id="sort"
                            name="sort"
                            class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                        >

                            <option value="">
                                Terbaru
                            </option>

                            <option
                                value="price_low"
                                @selected(request('sort') === 'price_low')
                            >
                                Harga Terendah
                            </option>

                            <option
                                value="price_high"
                                @selected(request('sort') === 'price_high')
                            >
                                Harga Tertinggi
                            </option>

                            <option
                                value="name"
                                @selected(request('sort') === 'name')
                            >
                                Nama A-Z
                            </option>

                        </select>

                    </div>


                    {{-- Button --}}
                    <div class="sm:col-span-2 lg:col-span-4">

                        <div class="flex flex-col gap-2 sm:flex-row sm:justify-end">

                            <x-button
                                type="submit"
                                class="w-full sm:w-auto"
                            >
                                Terapkan Filter
                            </x-button>

                            @if(request()->hasAny(['search', 'category', 'sort']))

                                <x-button
                                    variant="secondary"
                                    :href="route('products.index')"
                                    class="w-full sm:w-auto"
                                >
                                    Reset
                                </x-button>

                            @endif

                        </div>

                    </div>

                </form>

            </div>


            {{-- Products --}}
            <div class="mt-8">

                @if($products->isNotEmpty())

                    <div class="mb-5 flex items-center justify-between">

                        <p class="text-sm text-slate-500">
                            Menampilkan
                            <span class="font-semibold text-slate-700">
                                {{ $products->total() }}
                            </span>
                            produk
                        </p>

                    </div>


                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-5 lg:grid-cols-4">

                        @foreach($products as $product)

                            <x-product-card
                                :product="$product"
                            />

                        @endforeach

                    </div>


                    {{-- Pagination --}}
                    <div class="mt-8">
                        {{ $products->links('components.pagination') }}
                    </div>

                @else

                    <x-empty-state
                        icon="search"
                        title="Produk tidak ditemukan"
                        description="Coba ubah kata pencarian atau filter yang digunakan."
                        :action="route('products.index')"
                        action-text="Lihat Semua Produk"
                    />

                @endif

            </div>

        </div>

    </section>

</x-layouts.app>