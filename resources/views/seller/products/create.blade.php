<x-layouts.seller title="Tambah Produk">

    <div class="mx-auto w-full max-w-4xl px-4 py-6 sm:px-6 lg:px-8">

        <div class="mb-8">
            <a
                href="{{ route('seller.products.index') }}"
                class="text-sm font-bold text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-white flex items-center gap-1.5"
            >
                <i class="fa-solid fa-arrow-left"></i> Kembali ke daftar produk
            </a>

            <h1 class="mt-4 text-2xl font-black tracking-tight text-slate-900 sm:text-3xl dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-square-plus text-emerald-600"></i> Tambah Produk Baru
            </h1>

            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Tambahkan produk baru yang ingin kamu jual di Eskasaba Marketplace.
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
            <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs sm:p-7 dark:border-slate-800 dark:bg-slate-900">

                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-box text-emerald-600"></i> Informasi Produk
                </h2>

                <div class="mt-6 grid gap-5">

                    <div>
                        <label
                            for="name"
                            class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white"
                        >
                            <i class="fa-solid fa-heading text-slate-400 mr-1"></i> Nama Produk <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            required
                            placeholder="Contoh: Es Teh Manis / Rotbak Cokelat Keju"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >

                        @error('name')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>


                    <div class="grid gap-5 sm:grid-cols-2">

                        <div>
                            <label
                                for="category_id"
                                class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white"
                            >
                                <i class="fa-solid fa-layer-group text-slate-400 mr-1"></i> Kategori <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="category_id"
                                name="category_id"
                                required
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >
                                <option value="">Pilih Kategori</option>
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
                                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Multi-Flavor / Variant Input with Quick Presets --}}
                        <div x-data="{
                            flavorText: @js(old('condition', '')),
                            addPreset(flavor) {
                                if (!this.flavorText) {
                                    this.flavorText = flavor;
                                } else {
                                    let list = this.flavorText.split(',').map(s => s.trim()).filter(Boolean);
                                    if (!list.includes(flavor)) {
                                        list.push(flavor);
                                        this.flavorText = list.join(', ');
                                    }
                                }
                            },
                            get flavorList() {
                                if (!this.flavorText) return [];
                                return this.flavorText.split(',').map(s => s.trim()).filter(Boolean);
                            }
                        }">
                            <label for="condition" class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white">
                                <i class="fa-solid fa-tags text-emerald-600 mr-1"></i> Pilihan Rasa / Varian <span class="text-xs font-normal text-slate-400">(Pisahkan koma untuk banyak rasa)</span>
                            </label>

                            <input
                                id="condition"
                                name="condition"
                                type="text"
                                value="{{ old('condition') }}"
                                x-model="flavorText"
                                placeholder="Contoh: Cokelat, Keju, Strawberry, Pedas, Original"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >

                            {{-- Quick Preset Chips --}}
                            <div class="mt-2.5 flex flex-wrap items-center gap-1.5">
                                <span class="text-[11px] font-bold text-slate-400 mr-1">Rekomendasi Cepat:</span>
                                <button type="button" @click="addPreset('Cokelat')" class="rounded-xl bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-700 hover:bg-emerald-100 hover:text-emerald-800 transition dark:bg-slate-800 dark:text-slate-300">+ Cokelat</button>
                                <button type="button" @click="addPreset('Keju')" class="rounded-xl bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-700 hover:bg-emerald-100 hover:text-emerald-800 transition dark:bg-slate-800 dark:text-slate-300">+ Keju</button>
                                <button type="button" @click="addPreset('Strawberry')" class="rounded-xl bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-700 hover:bg-emerald-100 hover:text-emerald-800 transition dark:bg-slate-800 dark:text-slate-300">+ Strawberry</button>
                                <button type="button" @click="addPreset('Pedas')" class="rounded-xl bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-700 hover:bg-emerald-100 hover:text-emerald-800 transition dark:bg-slate-800 dark:text-slate-300">+ Pedas</button>
                                <button type="button" @click="addPreset('Original')" class="rounded-xl bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-700 hover:bg-emerald-100 hover:text-emerald-800 transition dark:bg-slate-800 dark:text-slate-300">+ Original</button>
                                <button type="button" @click="addPreset('Baru')" class="rounded-xl bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-700 hover:bg-emerald-100 hover:text-emerald-800 transition dark:bg-slate-800 dark:text-slate-300">+ Baru</button>
                            </div>

                            {{-- Live Flavor Badges Preview --}}
                            <div class="mt-3 flex flex-wrap gap-2" x-show="flavorList.length > 0">
                                <template x-for="(f, i) in flavorList" :key="i">
                                    <span class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300">
                                        <i class="fa-solid fa-tag text-[10px]"></i>
                                        <span x-text="f"></span>
                                    </span>
                                </template>
                            </div>

                            <p class="mt-1.5 text-[11px] text-slate-400">
                                <i class="fa-solid fa-circle-info text-emerald-600 mr-1"></i> Tulis varian rasa yang tersedia dipisahkan koma. Pembeli akan memilih salah satu varian ini seperti di Shopee.
                            </p>

                            @error('condition')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>


                    <div>
                        <label
                            for="description"
                            class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white"
                        >
                            <i class="fa-solid fa-align-left text-slate-400 mr-1"></i> Deskripsi Produk
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="5"
                            placeholder="Jelaskan produk secara jelas dan menarik..."
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >{{ old('description') }}</textarea>

                        @error('description')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

            </div>


            {{-- Price & Stock --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs sm:p-7 dark:border-slate-800 dark:bg-slate-900">

                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-coins text-emerald-600"></i> Harga & Stok
                </h2>

                <div class="mt-6 grid gap-5 sm:grid-cols-2">

                    <div>
                        <label
                            for="price"
                            class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white"
                        >
                            <i class="fa-solid fa-rupiah-sign text-slate-400 mr-1"></i> Harga Jual (Rp) <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="price"
                            name="price"
                            type="number"
                            min="0"
                            step="1"
                            value="{{ old('price') }}"
                            required
                            placeholder="Contoh: 15000"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >

                        @error('price')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>


                    <div>
                        <label
                            for="discount"
                            class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white"
                        >
                            <i class="fa-solid fa-percent text-slate-400 mr-1"></i> Diskon Potongan (Rp atau %) <span class="text-xs font-normal text-slate-400">(Opsional)</span>
                        </label>

                        <input
                            id="discount"
                            name="discount"
                            type="number"
                            min="0"
                            value="{{ old('discount', 0) }}"
                            placeholder="Contoh: 2000 (Rp) atau 10 (%)"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >

                        @error('discount')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>


                    <div>
                        <label
                            for="stock"
                            class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white"
                        >
                            <i class="fa-solid fa-cubes text-slate-400 mr-1"></i> Jumlah Stok <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="stock"
                            name="stock"
                            type="number"
                            min="0"
                            value="{{ old('stock') }}"
                            required
                            placeholder="Contoh: 20"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >

                        @error('stock')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>


                    <div>
                        <label
                            for="status"
                            class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white"
                        >
                            <i class="fa-solid fa-toggle-on text-slate-400 mr-1"></i> Status Produk
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >
                            <option value="active" @selected(old('status', 'active') === 'active')>Aktif (Dapat Dilihat & Dibeli)</option>
                            <option value="inactive" @selected(old('status') === 'inactive')>Nonaktif (Disembunyikan)</option>
                        </select>

                        @error('status')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

            </div>


            {{-- Images --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs sm:p-7 dark:border-slate-800 dark:bg-slate-900">

                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-image text-emerald-600"></i> Foto Produk
                </h2>

                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                    Upload satu atau beberapa foto produk (Otomatis dikompresi ke 300-400KB).
                </p>

                <div class="mt-5">
                    <input
                        type="file"
                        name="images[]"
                        accept="image/*"
                        multiple
                        class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 file:mr-4 file:rounded-xl file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:text-xs file:font-bold file:text-emerald-700 hover:file:bg-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:file:bg-slate-700 dark:file:text-emerald-400"
                    >

                    @error('images')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    @error('images.*')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>


            {{-- Action --}}
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a
                    href="{{ route('seller.products.index') }}"
                    class="inline-flex items-center justify-center gap-1.5 rounded-2xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                >
                    <i class="fa-solid fa-xmark"></i> Batal
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-6 py-3 text-sm font-bold text-white shadow-xs transition hover:bg-emerald-800"
                >
                    <i class="fa-solid fa-check"></i> Simpan Produk
                </button>
            </div>

        </form>

    </div>

</x-layouts.seller>