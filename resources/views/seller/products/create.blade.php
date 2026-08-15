<x-layouts.seller title="Tambah Produk">

    <div class="mx-auto w-full max-w-4xl px-4 py-6 sm:px-6 lg:px-8">

        <div class="mb-8">

            <a
                href="{{ route('seller.products.index') }}"
                class="text-sm font-medium text-slate-500 transition hover:text-slate-900"
            >
                ← Kembali ke produk
            </a>

            <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                Tambah Produk
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Tambahkan produk yang ingin kamu jual di Eskasaba Market.
            </p>

        </div>


        <form
            method="POST"
            action="{{ route('seller.products.store') }}"
            enctype="multipart/form-data"
            class="space-y-6"
        >

            @csrf


            {{-- Basic Information --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">

                <h2 class="text-lg font-bold text-slate-900">
                    Informasi Produk
                </h2>

                <div class="mt-6 grid gap-5">

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
                            value="{{ old('name') }}"
                            required
                            placeholder="Contoh: Buku Tulis"
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

                                <option value="">
                                    Pilih kategori
                                </option>

                                @foreach ($categories as $category)

                                    <option
                                        value="{{ $category->id }}"
                                        @selected(old('category_id') == $category->id)
                                    >
                                        {{ $category->name }}
                                    </option>

                                @endforeach

                            </select>

                            @error('category_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror

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
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                            >

                                <option value="baru" @selected(old('condition', 'baru') === 'baru')}>
                                    Baru
                                </option>

                                <option value="bekas" @selected(old('condition') === 'bekas')}>
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
                            placeholder="Jelaskan produk secara lengkap..."
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                        >{{ old('description') }}</textarea>

                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                    </div>

                </div>

            </div>


            {{-- Price & Stock --}}
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
                            step="1"
                            value="{{ old('price') }}"
                            required
                            placeholder="0"
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                        >

                        @error('price')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror

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
                            value="{{ old('stock') }}"
                            required
                            placeholder="0"
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                        >

                        @error('stock')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror

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
                            value="{{ old('weight') }}"
                            placeholder="Contoh: 500"
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                        >

                        @error('weight')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror

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
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                        >

                            <option value="active" @selected(old('status', 'active') === 'active')}>
                                Aktif
                            </option>

                            <option value="inactive" @selected(old('status') === 'inactive')}>
                                Nonaktif
                            </option>

                        </select>

                    </div>

                </div>

            </div>


            {{-- Images --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">

                <h2 class="text-lg font-bold text-slate-900">
                    Foto Produk
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Upload satu atau beberapa foto produk.
                </p>

                <div class="mt-5">

                    <input
                        type="file"
                        name="images[]"
                        accept="image/*"
                        multiple
                        class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-slate-100 file:px-4 file:py-2 file:text-sm file:font-semibold"
                    >

                    @error('images')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    @error('images.*')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                </div>

            </div>


            {{-- Action --}}
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

                <a
                    href="{{ route('seller.products.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800"
                >
                    Simpan Produk
                </button>

            </div>

        </form>

    </div>

</x-layouts.seller>