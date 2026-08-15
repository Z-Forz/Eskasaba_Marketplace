<x-layouts.seller title="Edit Produk">

    <div class="mx-auto w-full max-w-4xl px-4 py-6 sm:px-6 lg:px-8">

        <div class="mb-8">

            <a
                href="{{ route('seller.products.show', $product) }}"
                class="text-sm font-medium text-slate-500 hover:text-slate-900"
            >
                ← Kembali ke produk
            </a>

            <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                Edit Produk
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Perbarui informasi produkmu.
            </p>

        </div>


        <form
            method="POST"
            action="{{ route('seller.products.update', $product) }}"
            enctype="multipart/form-data"
            class="space-y-6"
        >

            @csrf
            @method('PUT')


            {{-- Basic --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">

                <h2 class="text-lg font-bold text-slate-900">
                    Informasi Produk
                </h2>

                <div class="mt-6 space-y-5">

                    <div>

                        <label
                            for="name"
                            class="mb-2 block text-sm font-semibold text-slate-900"
                        >
                            Nama Produk
                        </label>

                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name', $product->name) }}"
                            required
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                        >

                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                    </div>


                    <div class="grid gap-5 sm:grid-cols-2">

                        <div>

                            <label
                                for="category_id"
                                class="mb-2 block text-sm font-semibold text-slate-900"
                            >
                                Kategori
                            </label>

                            <select
                                id="category_id"
                                name="category_id"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                            >

                                @foreach ($categories as $category)

                                    <option
                                        value="{{ $category->id }}"
                                        @selected(old('category_id', $product->category_id) == $category->id)
                                    >
                                        {{ $category->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <div>

                            <label
                                for="condition"
                                class="mb-2 block text-sm font-semibold text-slate-900"
                            >
                                Kondisi
                            </label>

                            <select
                                id="condition"
                                name="condition"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm"
                            >

                                <option
                                    value="baru"
                                    @selected(old('condition', $product->condition) === 'baru')
                                >
                                    Baru
                                </option>

                                <option
                                    value="bekas"
                                    @selected(old('condition', $product->condition) === 'bekas')
                                >
                                    Bekas
                                </option>

                            </select>

                        </div>

                    </div>


                    <div>

                        <label
                            for="description"
                            class="mb-2 block text-sm font-semibold text-slate-900"
                        >
                            Deskripsi
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="6"
                            required
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                        >{{ old('description', $product->description) }}</textarea>

                    </div>

                </div>

            </div>


            {{-- Price --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">

                <h2 class="text-lg font-bold text-slate-900">
                    Harga & Stok
                </h2>

                <div class="mt-6 grid gap-5 sm:grid-cols-2">

                    <div>

                        <label
                            for="price"
                            class="mb-2 block text-sm font-semibold text-slate-900"
                        >
                            Harga
                        </label>

                        <input
                            id="price"
                            name="price"
                            type="number"
                            min="0"
                            value="{{ old('price', $product->price) }}"
                            required
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm"
                        >

                    </div>


                    <div>

                        <label
                            for="stock"
                            class="mb-2 block text-sm font-semibold text-slate-900"
                        >
                            Stok
                        </label>

                        <input
                            id="stock"
                            name="stock"
                            type="number"
                            min="0"
                            value="{{ old('stock', $product->stock) }}"
                            required
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm"
                        >

                    </div>


                    <div>

                        <label
                            for="weight"
                            class="mb-2 block text-sm font-semibold text-slate-900"
                        >
                            Berat (gram)
                        </label>

                        <input
                            id="weight"
                            name="weight"
                            type="number"
                            min="0"
                            step="0.01"
                            value="{{ old('weight', $product->weight) }}"
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm"
                        >

                    </div>


                    <div>

                        <label
                            for="status"
                            class="mb-2 block text-sm font-semibold text-slate-900"
                        >
                            Status
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm"
                        >

                            <option
                                value="active"
                                @selected(old('status', $product->status) === 'active')
                            >
                                Aktif
                            </option>

                            <option
                                value="inactive"
                                @selected(old('status', $product->status) === 'inactive')
                            >
                                Nonaktif
                            </option>

                        </select>

                    </div>

                </div>

            </div>


            {{-- Existing Images --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">

                <h2 class="text-lg font-bold text-slate-900">
                    Foto Produk
                </h2>

                @if ($product->images->count())

                    <div class="mt-5 grid grid-cols-3 gap-3 sm:grid-cols-4">

                        @foreach ($product->images as $image)

                            <div class="aspect-square overflow-hidden rounded-2xl bg-slate-100">

                                <img
                                    src="{{ Storage::url($image->image) }}"
                                    alt="{{ $product->name }}"
                                    class="h-full w-full object-cover"
                                >

                            </div>

                        @endforeach

                    </div>

                @endif


                <div class="mt-6">

                    <label
                        for="images"
                        class="mb-2 block text-sm font-semibold text-slate-900"
                    >
                        Tambah Foto
                    </label>

                    <input
                        id="images"
                        type="file"
                        name="images[]"
                        multiple
                        accept="image/*"
                        class="block w-full rounded-xl border border-slate-200 px-4 py-3 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-slate-100 file:px-4 file:py-2 file:text-sm file:font-semibold"
                    >

                </div>

            </div>


            {{-- Actions --}}
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

                <a
                    href="{{ route('seller.products.show', $product) }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800"
                >
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</x-layouts.seller>