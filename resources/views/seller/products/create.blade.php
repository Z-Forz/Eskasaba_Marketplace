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
                <i class="fa-solid fa-plus text-emerald-600"></i> Tambah Produk Baru
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
                    <i class="fa-solid fa-boxes-stacked text-emerald-600"></i> Informasi Produk
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

                    {{-- Harga Jual --}}
                    <div x-data="{
                        raw: '{{ old('price', '') }}',
                        get display() {
                            if (!this.raw && this.raw !== 0) return '';
                            let clean = String(this.raw).replace(/\D/g, '');
                            return clean ? Number(clean).toLocaleString('id-ID') : '';
                        },
                        set display(val) {
                            this.raw = val.replace(/\D/g, '');
                        }
                    }">
                        <label
                            for="price_display"
                            class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white"
                        >
                            <i class="fa-solid fa-rupiah-sign text-emerald-600 mr-1"></i> Harga Jual (Rp) <span class="text-red-500">*</span>
                        </label>

                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400 dark:text-slate-500">Rp</span>
                            <input
                                id="price_display"
                                type="text"
                                x-model="display"
                                placeholder="15.000"
                                required
                                class="w-full rounded-2xl border border-slate-200 pl-11 pr-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >
                            <input type="hidden" name="price" :value="raw">
                        </div>

                        @error('price')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>


                    {{-- Potongan Diskon (Rp) --}}
                    <div x-data="{
                        raw: '{{ old('discount', '') }}',
                        get display() {
                            if (!this.raw || this.raw == '0') return '';
                            let clean = String(this.raw).replace(/\D/g, '');
                            return clean ? Number(clean).toLocaleString('id-ID') : '';
                        },
                        set display(val) {
                            this.raw = val.replace(/\D/g, '');
                        }
                    }">
                        <label
                            for="discount_display"
                            class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white"
                        >
                            <i class="fa-solid fa-tag text-emerald-600 mr-1"></i> Potongan Diskon (Rp) <span class="text-xs font-normal text-slate-400">(Opsional)</span>
                        </label>

                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400 dark:text-slate-500">Rp</span>
                            <input
                                id="discount_display"
                                type="text"
                                x-model="display"
                                placeholder="2.000"
                                class="w-full rounded-2xl border border-slate-200 pl-11 pr-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >
                            <input type="hidden" name="discount" :value="raw || 0">
                        </div>

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
            <div
                class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs sm:p-7 dark:border-slate-800 dark:bg-slate-900"
                x-data="{
                    previews: [],
                    dt: new DataTransfer(),
                    errorMsg: '',
                    maxFiles: 5,
                    handleFiles(e) {
                        const newFiles = Array.from(e.target.files);
                        this.errorMsg = '';

                        for (let file of newFiles) {
                            if (this.dt.items.length >= this.maxFiles) {
                                this.errorMsg = 'Maksimal foto produk adalah ' + this.maxFiles + ' foto.';
                                break;
                            }
                            if (file.type.startsWith('image/')) {
                                this.dt.items.add(file);
                                this.previews.push({
                                    name: file.name,
                                    url: URL.createObjectURL(file)
                                });
                            }
                        }

                        this.$refs.fileInput.files = this.dt.files;
                    },
                    removeFile(index) {
                        this.dt.items.remove(index);
                        if (this.previews[index]) {
                            URL.revokeObjectURL(this.previews[index].url);
                        }
                        this.previews.splice(index, 1);
                        this.$refs.fileInput.files = this.dt.files;
                        this.errorMsg = '';
                    }
                }"
            >

                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-image text-emerald-600"></i> Foto Produk
                    </h2>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                        <span x-text="previews.length"></span> / 5 Foto
                    </span>
                </div>

                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                    Upload maksimal 5 foto produk. Kamu bisa menghapus & menambahkan foto sekaligus sebelum disimpan.
                </p>

                {{-- Alert Error jika lebih dari 5 --}}
                <div x-show="errorMsg" class="mt-3 rounded-2xl bg-red-50 p-3 text-xs font-semibold text-red-600 dark:bg-red-950/40 dark:text-red-400">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i> <span x-text="errorMsg"></span>
                </div>

                {{-- Preview Grid --}}
                <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-5" x-show="previews.length > 0">
                    <template x-for="(prev, i) in previews" :key="i">
                        <div class="group relative aspect-square overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 dark:border-slate-700 dark:bg-slate-800">
                            <img :src="prev.url" :alt="prev.name" class="h-full w-full object-cover">
                            
                            {{-- Remove Button Overlay --}}
                            <button
                                type="button"
                                @click="removeFile(i)"
                                class="absolute right-2 top-2 flex h-8 w-8 items-center justify-center rounded-full bg-slate-900/75 text-white shadow-lg backdrop-blur-md border border-white/40 transition-all duration-200 hover:bg-red-600 hover:border-red-500 hover:scale-110 active:scale-95 focus:outline-none"
                                title="Hapus foto ini"
                            >
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </button>
                        </div>
                    </template>
                </div>

                <div class="mt-5">
                    <label
                        for="images"
                        x-show="previews.length < maxFiles"
                        class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 p-6 text-center transition hover:border-emerald-500 hover:bg-emerald-50/30 dark:border-slate-700 dark:hover:border-emerald-500 dark:hover:bg-slate-800/50"
                    >
                        <i class="fa-solid fa-cloud-arrow-up text-2xl text-slate-400 dark:text-slate-500"></i>
                        <span class="mt-2 text-xs font-bold text-slate-700 dark:text-slate-300">Klik atau Pilih Foto Produk</span>
                        <span class="mt-0.5 text-[11px] text-slate-400">PNG, JPG, WEBP (Maks 5MB per file)</span>

                        <input
                            id="images"
                            type="file"
                            name="images[]"
                            x-ref="fileInput"
                            accept="image/*"
                            multiple
                            @change="handleFiles($event)"
                            class="hidden"
                        >
                    </label>

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