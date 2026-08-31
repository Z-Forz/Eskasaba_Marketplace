<x-layouts.seller title="Edit Produk">

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
                <i class="fa-solid fa-pen-to-square text-emerald-600"></i> Edit Produk: {{ $product->name }}
            </h1>

            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Perbarui informasi, harga, stok, varian rasa, atau foto produk jualanmu.
            </p>
        </div>

        <form
            method="POST"
            action="{{ route('seller.products.update', $product) }}"
            enctype="multipart/form-data"
            class="space-y-6"
            x-data="{
                hasSizes: @js(!empty(old('variants', $product->variants ?? []))),
                variants: @js(old('variants', $product->variants ?? [])).map(v => ({
                    name: v.name || '',
                    price: v.price ? Number(v.price).toLocaleString('id-ID') : ''
                })),
                basePriceRaw: '{{ old('price', $product->price ?? '') }}',
                
                get basePriceDisplay() {
                    if (!this.basePriceRaw && this.basePriceRaw !== 0) return '';
                    let clean = String(this.basePriceRaw).replace(/\D/g, '');
                    return clean ? Number(clean).toLocaleString('id-ID') : '';
                },
                set basePriceDisplay(val) {
                    this.basePriceRaw = val.replace(/\D/g, '');
                },
                addVariant() {
                    this.variants.push({ name: '', price: '' });
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
            @method('PUT')

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
                            value="{{ old('name', $product->name) }}"
                            required
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
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
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
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >{{ old('description', $product->description) }}</textarea>
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
                                    <i class="fa-solid fa-ruler-combined"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-extrabold">Berdasarkan Size / Ukuran</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-normal">Setiap size memiliki harga tersendiri (misal: S, M, L)</p>
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
                                    <i class="fa-solid fa-ruler-combined"></i> Daftar Ukuran / Size & Harganya
                                </label>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                    Kartu produk di katalog akan otomatis menampilkan rentang harga (misal: Rp 10.000 - Rp 20.000).
                                </p>
                            </div>
                            <button type="button" @click="addVariant()" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-700 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-800 transition shadow-xs">
                                <i class="fa-solid fa-plus"></i> Tambah Size
                            </button>
                        </div>

                        <div class="space-y-3">
                            <template x-for="(v, i) in variants" :key="i">
                                <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-2xs dark:border-slate-700 dark:bg-slate-800">
                                    <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-500 mb-1">Nama Size / Ukuran</label>
                                            <input type="text" :name="`variants[${i}][name]`" x-model="v.name" placeholder="Misal: Size S, Size M, Size L, XL" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-900 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-900 dark:text-white" :required="hasSizes">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-500 mb-1">Harga Size (Rp)</label>
                                            <div class="relative">
                                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                                <input type="text" :name="`variants[${i}][price]`" :value="v.price" @input="formatVariantPrice(i, $event.target.value)" placeholder="15.000" class="w-full rounded-xl border border-slate-200 pl-9 pr-3 py-2 text-sm font-semibold text-slate-900 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-900 dark:text-white" :required="hasSizes">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" @click="removeVariant(i)" x-show="variants.length > 1" class="mt-4 sm:mt-5 h-9 w-9 shrink-0 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-950/40 dark:text-red-400 flex items-center justify-center transition" title="Hapus Size">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Hidden input for main price --}}
                    <input type="hidden" name="price" :value="basePriceRaw">
                    @error('price')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    {{-- Diskon & Stok & Status Grid --}}
                    <div class="grid gap-5 sm:grid-cols-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                        {{-- Potongan Diskon --}}
                        <div x-data="{
                            raw: '{{ old('discount', $product->discount) }}',
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
                                Sisa Stok <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="stock"
                                name="stock"
                                type="number"
                                min="0"
                                value="{{ old('stock', $product->stock) }}"
                                required
                                class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
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
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >
                                <option value="active" @selected(old('status', $product->status) === 'active')>Aktif (Siap Dibeli)</option>
                                <option value="inactive" @selected(old('status', $product->status) === 'inactive')>Nonaktif (Disembunyikan)</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>

            {{-- 3. Foto Produk --}}
            <div
                class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs sm:p-7 dark:border-slate-800 dark:bg-slate-900"
                x-data="{
                    existingCount: {{ $product->images->count() }},
                    deletedIds: [],
                    previews: [],
                    dt: new DataTransfer(),
                    errorMsg: '',
                    maxFiles: 5,

                    toggleDeleteExisting(id) {
                        if (this.deletedIds.includes(id)) {
                            this.deletedIds = this.deletedIds.filter(i => i !== id);
                        } else {
                            this.deletedIds.push(id);
                        }
                        this.validateCount();
                    },

                    isDeleted(id) {
                        return this.deletedIds.includes(id);
                    },

                    get remainingSlots() {
                        const activeExisting = this.existingCount - this.deletedIds.length;
                        return this.maxFiles - activeExisting;
                    },

                    get totalCount() {
                        const activeExisting = this.existingCount - this.deletedIds.length;
                        return activeExisting + this.previews.length;
                    },

                    validateCount() {
                        if (this.totalCount > this.maxFiles) {
                            this.errorMsg = 'Total foto (lama + baru) tidak boleh lebih dari ' + this.maxFiles + ' foto.';
                        } else {
                            this.errorMsg = '';
                        }
                    },

                    handleFiles(e) {
                        const newFiles = Array.from(e.target.files);
                        this.errorMsg = '';

                        for (let file of newFiles) {
                            if (this.totalCount >= this.maxFiles) {
                                this.errorMsg = 'Total foto produk maksimal adalah ' + this.maxFiles + ' foto.';
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

                    removeNewFile(index) {
                        this.dt.items.remove(index);
                        if (this.previews[index]) {
                            URL.revokeObjectURL(this.previews[index].url);
                        }
                        this.previews.splice(index, 1);
                        this.$refs.fileInput.files = this.dt.files;
                        this.validateCount();
                    }
                }"
            >
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-100 pb-4 dark:border-slate-800 w-full">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-image text-emerald-600"></i> 3. Foto Produk
                        </span>
                        <span class="ml-auto rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                            <span x-text="totalCount"></span> / 5 Foto Total
                        </span>
                    </h2>
                </div>

                <p class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                    Kamu bisa menghapus foto lama dan menambahkan foto baru sekaligus (Maksimal total 5 foto).
                </p>

                <div x-show="errorMsg" class="mt-3 rounded-2xl bg-red-50 p-3 text-xs font-semibold text-red-600 dark:bg-red-950/40 dark:text-red-400">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i> <span x-text="errorMsg"></span>
                </div>

                {{-- Existing Images --}}
                @if ($product->images->count())
                    <div class="mt-5">
                        <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Foto Saat Ini:</h3>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-5">
                            @foreach ($product->images as $image)
                                <div class="group relative aspect-square overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 dark:border-slate-700 dark:bg-slate-800">
                                    <img
                                        src="{{ Storage::url($image->image) }}"
                                        alt="{{ $product->name }}"
                                        class="h-full w-full object-cover transition duration-200"
                                        :class="{ 'opacity-30 blur-xs': isDeleted({{ $image->id }}) }"
                                    >

                                    <div
                                        x-show="isDeleted({{ $image->id }})"
                                        class="absolute inset-0 flex flex-col items-center justify-center bg-red-950/70 text-white p-2 text-center"
                                    >
                                        <i class="fa-solid fa-trash-can text-sm text-red-300 mb-1"></i>
                                        <span class="text-[10px] font-bold text-red-200">Akan Dihapus</span>
                                        <button
                                            type="button"
                                            @click="toggleDeleteExisting({{ $image->id }})"
                                            class="mt-1.5 rounded-lg bg-white/20 px-2 py-0.5 text-[10px] font-bold text-white hover:bg-white/40 transition"
                                        >
                                            Urungkan
                                        </button>
                                    </div>

                                    <button
                                        type="button"
                                        x-show="!isDeleted({{ $image->id }})"
                                        @click="toggleDeleteExisting({{ $image->id }})"
                                        class="absolute right-2 top-2 flex h-8 w-8 items-center justify-center rounded-full bg-slate-900/75 text-white shadow-lg backdrop-blur-md border border-white/40 transition hover:bg-red-600"
                                        title="Hapus foto"
                                    >
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </button>

                                    <input
                                        type="checkbox"
                                        name="delete_images[]"
                                        value="{{ $image->id }}"
                                        :checked="isDeleted({{ $image->id }})"
                                        class="hidden"
                                    >
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- New Upload Previews --}}
                <div class="mt-5" x-show="previews.length > 0">
                    <h3 class="text-xs font-bold text-emerald-700 dark:text-emerald-400 mb-2">Foto Baru:</h3>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-5">
                        <template x-for="(prev, i) in previews" :key="i">
                            <div class="group relative aspect-square overflow-hidden rounded-2xl border-2 border-emerald-500/60 bg-slate-100 dark:bg-slate-800">
                                <img :src="prev.url" :alt="prev.name" class="h-full w-full object-cover">
                                <button
                                    type="button"
                                    @click="removeNewFile(i)"
                                    class="absolute right-2 top-2 flex h-8 w-8 items-center justify-center rounded-full bg-slate-900/75 text-white shadow-lg backdrop-blur-md border border-white/40 transition hover:bg-red-600"
                                    title="Batal foto baru"
                                >
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="mt-5">
                    <label
                        for="images"
                        x-show="remainingSlots > 0"
                        class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 p-6 text-center transition hover:border-emerald-500 hover:bg-emerald-50/30 dark:border-slate-700 dark:hover:border-emerald-500 dark:hover:bg-slate-800/50"
                    >
                        <i class="fa-solid fa-cloud-arrow-up text-2xl text-slate-400 dark:text-slate-500"></i>
                        <span class="mt-2 text-xs font-bold text-slate-700 dark:text-slate-300">
                            Tambah Foto Baru (<span x-text="remainingSlots"></span> sisa slot)
                        </span>
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
                    <i class="fa-solid fa-check"></i> Simpan Perubahan
                </button>
            </div>

        </form>

    </div>

</x-layouts.seller>