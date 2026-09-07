<x-layouts.seller title="Tambah Produk">

    <div class="mx-auto w-full max-w-4xl px-4 py-6 sm:px-6 lg:px-8">

        {{-- Header --}}
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
                Isi rincian produk, atur harga tunggal atau harga per size, dan unggah foto produk jualanmu.
            </p>
        </div>

        <form
            method="POST"
            action="{{ route('seller.products.store') }}"
            enctype="multipart/form-data"
            class="space-y-6"
            x-data="{
                hasSizes: @js(!empty(old('variants', []))),
                variants: @js(old('variants', [])).map(v => ({
                    name: v.name || '',
                    price: v.price ? Number(v.price).toLocaleString('id-ID') : '',
                    stock: v.stock !== undefined ? v.stock : ''
                })),
                basePriceRaw: '{{ old('price', '') }}',
                mainStockRaw: '{{ old('stock', '') }}',
                
                get basePriceDisplay() {
                    if (!this.basePriceRaw && this.basePriceRaw !== 0) return '';
                    let clean = String(this.basePriceRaw).replace(/\D/g, '');
                    return clean ? Number(clean).toLocaleString('id-ID') : '';
                },
                set basePriceDisplay(val) {
                    this.basePriceRaw = val.replace(/\D/g, '');
                },
                get totalStock() {
                    if (this.hasSizes) {
                        return this.variants.reduce((sum, v) => sum + (parseInt(v.stock, 10) || 0), 0);
                    }
                    return this.mainStockRaw;
                },
                addVariant() {
                    this.variants.push({ name: '', price: '', stock: '' });
                    this.syncMinPrice();
                },
                removeVariant(index) {
                    this.variants.splice(index, 1);
                    this.syncMinPrice();
                },
                formatVariantPrice(index, val) {
                    let clean = String(val).replace(/\D/g, '');
                    this.variants[index].price = clean ? parseInt(clean, 10).toLocaleString('id-ID') : '';
                    this.syncMinPrice();
                },
                syncMinPrice() {
                    if (this.hasSizes && this.variants.length > 0) {
                        let validPrices = this.variants
                            .map(v => parseInt(String(v.price).replace(/\D/g, ''), 10))
                            .filter(p => !isNaN(p) && p > 0);
                        if (validPrices.length > 0) {
                            this.basePriceRaw = String(Math.min(...validPrices));
                        }
                    }
                },
                toggleSizes(enable) {
                    this.hasSizes = enable;
                    if (enable && this.variants.length === 0) {
                        this.addVariant();
                    } else if (!enable) {
                        this.variants = [];
                    }
                }
            }"
        >
            @csrf

            {{-- 1. Informasi Produk --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs sm:p-7 dark:border-slate-800 dark:bg-slate-900">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-100 pb-4 dark:border-slate-800">
                    <i class="fa-solid fa-boxes-stacked text-emerald-600"></i> 1. Informasi Produk
                </h2>

                <div class="mt-6 space-y-5">
                    {{-- Nama Produk --}}
                    <div>
                        <label for="name" class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white">
                            <i class="fa-solid fa-heading text-slate-400 mr-1"></i> Nama Produk <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            required
                            placeholder="Contoh: Es Teh Manis / Rotbak Cokelat Keju / Seragam Olahraga"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >
                        @error('name')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label for="category_id" class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white">
                            <i class="fa-solid fa-layer-group text-slate-400 mr-1"></i> Kategori Produk <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="category_id"
                            name="category_id"
                            required
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="description" class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white">
                            <i class="fa-solid fa-align-left text-slate-400 mr-1"></i> Deskripsi Produk <span class="text-xs font-normal text-slate-400">(Opsional)</span>
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            placeholder="Jelaskan detail bahan, porsi, atau rasa produk secara jelas..."
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- 2. Pengaturan Harga & Stok --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs sm:p-7 dark:border-slate-800 dark:bg-slate-900">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-100 pb-4 dark:border-slate-800">
                    <i class="fa-solid fa-coins text-emerald-600"></i> 2. Harga, Size & Stok
                </h2>

                <div class="mt-6 space-y-6">

                    {{-- Mode Penentuan Harga --}}
                    <div>
                        <label class="mb-3 block text-sm font-semibold text-slate-900 dark:text-white">
                            <i class="fa-solid fa-tags text-emerald-600 mr-1"></i> Tipe Harga Produk
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            {{-- Option 1: Single Price --}}
                            <button
                                type="button"
                                @click="toggleSizes(false)"
                                :class="!hasSizes
                                    ? 'border-emerald-600 bg-emerald-50/70 text-emerald-950 ring-2 ring-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:ring-emerald-900 font-bold shadow-xs'
                                    : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 font-semibold'"
                                class="flex items-center gap-3 rounded-2xl border p-4 text-left transition cursor-pointer"
                            >
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-400">
                                    <i class="fa-solid fa-money-bill-wave"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-extrabold">Harga Tunggal</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-normal">Satu harga untuk semua item (tanpa pilihan size)</p>
                                </div>
                            </button>

                            {{-- Option 2: Size Variants --}}
                            <button
                                type="button"
                                @click="toggleSizes(true)"
                                :class="hasSizes
                                    ? 'border-emerald-600 bg-emerald-50/70 text-emerald-950 ring-2 ring-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:ring-emerald-900 font-bold shadow-xs'
                                    : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 font-semibold'"
                                class="flex items-center gap-3 rounded-2xl border p-4 text-left transition cursor-pointer"
                            >
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-400">
                                    <i class="fa-solid fa-layer-group"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-extrabold">Berdasarkan Rasa / Ukuran</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-normal">Setiap rasa/ukuran memiliki harga tersendiri (misal: Pedas, Manis, Size S, M, L)</p>
                                </div>
                            </button>
                        </div>
                    </div>

                    {{-- Mode 1: Single Price Input --}}
                    <div x-show="!hasSizes" class="rounded-2xl border border-slate-200 bg-slate-50/50 p-4 dark:border-slate-800 dark:bg-slate-800/40">
                        <label for="price_display" class="mb-2 block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Harga Jual Produk (Rp) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">Rp</span>
                            <input
                                id="price_display"
                                type="text"
                                x-model="basePriceDisplay"
                                placeholder="15.000"
                                :required="!hasSizes"
                                class="w-full rounded-2xl border border-slate-200 pl-11 pr-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >
                        </div>
                        <p class="mt-1.5 text-[11px] text-slate-400">Masukkan harga pasti jualanmu.</p>
                    </div>

                    {{-- Mode 2: Multi-Size Inputs --}}
                    <div x-show="hasSizes" class="rounded-2xl border border-emerald-200/80 bg-emerald-50/30 p-4 dark:border-emerald-900/50 dark:bg-emerald-950/20">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <label class="block text-xs font-bold text-emerald-900 dark:text-emerald-300 flex items-center gap-1.5">
                                    <i class="fa-solid fa-layer-group"></i> Daftar Varian Rasa / Ukuran & Harganya
                                </label>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                    Kartu produk di katalog akan otomatis menampilkan rentang harga (misal: Rp 10.000 - Rp 20.000).
                                </p>
                            </div>
                            <button type="button" @click="addVariant()" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-700 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-800 transition shadow-xs">
                                <i class="fa-solid fa-plus"></i> Tambah Varian
                            </button>
                        </div>

                        <div class="space-y-3">
                            <template x-for="(v, i) in variants" :key="i">
                                <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-2xs dark:border-slate-700 dark:bg-slate-800">
                                    <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-2">
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-500 mb-1">Nama Rasa / Ukuran</label>
                                            <input type="text" :name="`variants[${i}][name]`" x-model="v.name" placeholder="Misal: Pedas Sedang, Size S" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-900 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-900 dark:text-white" :required="hasSizes">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-500 mb-1">Harga (Rp)</label>
                                            <div class="relative">
                                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                                <input type="text" :value="v.price" @input="formatVariantPrice(i, $event.target.value)" placeholder="15.000" class="w-full rounded-xl border border-slate-200 pl-9 pr-3 py-2 text-sm font-semibold text-slate-900 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-900 dark:text-white" :required="hasSizes">
                                                <input type="hidden" :name="`variants[${i}][price]`" :value="String(v.price).replace(/\D/g, '')">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-500 mb-1">Stok Varian</label>
                                            <input type="number" min="0" :name="`variants[${i}][stock]`" x-model="v.stock" placeholder="Misal: 10" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-900 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-900 dark:text-white" :required="hasSizes">
                                        </div>
                                    </div>
                                    <button type="button" @click="removeVariant(i)" x-show="variants.length > 1" class="mt-4 sm:mt-5 h-9 w-9 shrink-0 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-950/40 dark:text-red-400 flex items-center justify-center transition" title="Hapus Size">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Hidden input for main price to satisfy backend validation --}}
                    <input type="hidden" name="price" :value="basePriceRaw">
                    @error('price')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    {{-- Diskon & Stok & Status Grid --}}
                    <div class="grid gap-5 sm:grid-cols-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                        {{-- Potongan Diskon --}}
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
                            <label for="discount_display" class="mb-2 block text-xs font-bold text-slate-700 dark:text-slate-300">
                                Diskon (Rp) <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                <input
                                    id="discount_display"
                                    type="text"
                                    x-model="display"
                                    placeholder="2.000"
                                    class="w-full rounded-2xl border border-slate-200 pl-9 pr-3 py-2.5 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                >
                                <input type="hidden" name="discount" :value="raw || 0">
                            </div>
                        </div>

                        {{-- Stok --}}
                        <div>
                            <label for="stock" class="mb-2 block text-xs font-bold text-slate-700 dark:text-slate-300">
                                <span x-text="hasSizes ? 'Total Stok Varian' : 'Sisa Stok'">Sisa Stok</span> <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="stock"
                                name="stock"
                                type="number"
                                min="0"
                                :value="totalStock"
                                @input="if(!hasSizes) mainStockRaw = $event.target.value"
                                :readonly="hasSizes"
                                required
                                placeholder="Misal: 20"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                :class="hasSizes ? 'bg-slate-100 dark:bg-slate-900 text-slate-500 cursor-not-allowed' : ''"
                            >
                            @error('stock')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div>
                            <label for="status" class="mb-2 block text-xs font-bold text-slate-700 dark:text-slate-300">
                                Status Produk
                            </label>
                            <select
                                id="status"
                                name="status"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >
                                <option value="active" @selected(old('status', 'active') === 'active')>Aktif (Siap Dibeli)</option>
                                <option value="inactive" @selected(old('status') === 'inactive')>Nonaktif (Disembunyikan)</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>

            {{-- 3. Foto Produk --}}
            <div
                class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs sm:p-7 dark:border-slate-800 dark:bg-slate-900"
                x-data="{
                    previews: [],
                    errorMsg: '',
                    maxFiles: 5,
                    handleFiles(e) {
                        const files = Array.from(e.target.files);
                        this.errorMsg = '';

                        if (files.length > this.maxFiles) {
                            this.errorMsg = 'Maksimal foto produk yang dapat diunggah adalah ' + this.maxFiles + ' foto.';
                        }

                        // Hapus memori preview lama
                        this.previews.forEach(p => URL.revokeObjectURL(p.url));
                        this.previews = [];

                        for (let file of files.slice(0, this.maxFiles)) {
                            if (file.type.startsWith('image/')) {
                                this.previews.push({
                                    name: file.name,
                                    url: URL.createObjectURL(file)
                                });
                            }
                        }
                    }
                }"
            >
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-image text-emerald-600"></i> 3. Foto Produk
                    </h2>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                        <span x-text="previews.length"></span> / 5 Foto
                    </span>
                </div>

                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                    Unggah maksimal 5 foto produk jualanmu (Format: PNG, JPG, WEBP - Maks 5MB per foto).
                </p>

                <div x-show="errorMsg" class="mt-3 rounded-2xl bg-red-50 p-3 text-xs font-semibold text-red-600 dark:bg-red-950/40 dark:text-red-400">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i> <span x-text="errorMsg"></span>
                </div>

                {{-- Live Image Previews --}}
                <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-5" x-show="previews.length > 0">
                    <template x-for="(prev, i) in previews" :key="i">
                        <div class="group relative aspect-square overflow-hidden rounded-2xl border-2 border-emerald-500/60 bg-slate-100 dark:bg-slate-800 shadow-xs">
                            <img :src="prev.url" :alt="prev.name" class="h-full w-full object-cover">
                            <div class="absolute bottom-0 inset-x-0 bg-slate-950/70 py-1 text-center">
                                <span class="text-[10px] font-bold text-white truncate block px-1" x-text="prev.name"></span>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- File Input --}}
                <div class="mt-5">
                    <label for="images" class="mb-2 block text-xs font-bold text-slate-700 dark:text-slate-300">
                        Pilih File Gambar Produk:
                    </label>
                    <input
                        id="images"
                        type="file"
                        name="images[]"
                        accept="image/jpeg,image/png,image/jpg,image/webp"
                        multiple
                        @change="handleFiles($event)"
                        class="w-full text-xs text-slate-500 file:mr-4 file:rounded-xl file:border-0 file:bg-emerald-600 file:px-4 file:py-2.5 file:text-xs file:font-bold file:text-white hover:file:bg-emerald-700 dark:file:bg-emerald-600 dark:file:text-white border border-slate-200 rounded-2xl p-2 dark:border-slate-700 dark:bg-slate-800 cursor-pointer shadow-xs"
                    >
                </div>
            </div>

            {{-- Action Buttons --}}
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