<x-layouts.seller title="Kirim Request Kategori / Fitur Baru">

    <div class="mx-auto max-w-3xl space-y-6">

        {{-- Navigation Header --}}
        <div>
            <a
                href="{{ route('seller.seller-requests.index') }}"
                class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
            >
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Request
            </a>

            <div class="mt-3">
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-folder-plus text-emerald-600"></i> Kirim Request Baru ke Admin
                </h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Sampaikan nama kategori produk baru atau usulan fitur yang Anda butuhkan untuk kelancaran toko Anda.
                </p>
            </div>
        </div>

        @if ($errors->any())
            <x-alert type="error" :message="$errors->first()" />
        @endif

        {{-- Form Container --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900 sm:p-8">

            <form
                method="POST"
                action="{{ route('seller.seller-requests.store') }}"
                x-data="{ isSubmitting: false }"
                @submit="if (isSubmitting) { $event.preventDefault(); return false; } isSubmitting = true;"
                :class="{ 'pointer-events-none opacity-80': isSubmitting }"
                class="space-y-6"
            >
                @csrf

                {{-- Tipe Request --}}
                <div>
                    <label for="type" class="block text-sm font-bold text-slate-900 dark:text-white">
                        Tipe Request <span class="text-rose-500">*</span>
                    </label>
                    <p class="mt-0.5 text-xs text-slate-400">
                        Pilih kategori produk jika Anda ingin menambah kategori baru untuk jualan.
                    </p>

                    <select
                        id="type"
                        name="type"
                        required
                        class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    >
                        <option value="category" @selected(old('type') === 'category' || !old('type'))>Kategori Produk Baru</option>
                        <option value="other" @selected(old('type') === 'other')>Saran / Lainnya</option>
                    </select>
                    @error('type')
                        <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Kategori yang Dibutuhkan / Judul --}}
                <div>
                    <label for="title" class="block text-sm font-bold text-slate-900 dark:text-white">
                        Nama Kategori yang Dibutuhkan <span class="text-rose-500">*</span>
                    </label>
                    <p class="mt-0.5 text-xs text-slate-400">
                        Isikan nama kategori produk yang Anda inginkan agar Admin dapat membuatnya di sistem.
                    </p>

                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title') }}"
                        required
                        placeholder="Contoh: Kerajinan Tangan, Perlengkapan Otomotif, Buku & Alat Tulis, dll."
                        class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-900 placeholder-slate-400 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    />
                    @error('title')
                        <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Keterangan / Detail Tambahan --}}
                <div>
                    <label for="description" class="block text-sm font-bold text-slate-900 dark:text-white">
                        Keterangan / Detail Tambahan <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <p class="mt-0.5 text-xs text-slate-400">
                        Tambahkan penjelasan singkat produk seperti apa yang akan Anda jual pada kategori ini.
                    </p>

                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        placeholder="Contoh: Saya berencana menjual gantungan kunci ukir kayu dan gelang handmade..."
                        class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Info Note --}}
                <div class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-4 text-xs text-emerald-800 dark:border-emerald-900/40 dark:bg-emerald-950/30 dark:text-emerald-300">
                    <p class="font-bold flex items-center gap-1.5">
                        <i class="fa-solid fa-paper-plane text-emerald-600 dark:text-emerald-400"></i> Notifikasi Otomatis
                    </p>
                    <p class="mt-1 leading-relaxed opacity-90">
                        Setelah dikirim, pesan Anda akan langsung diteruskan ke Admin via WhatsApp & Sistem. Anda dapat mengecek status dibaca dan balasan Admin kapan saja di Panel Seller.
                    </p>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4 dark:border-slate-800">
                    <a
                        href="{{ route('seller.seller-requests.index') }}"
                        class="rounded-xl px-5 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        :disabled="isSubmitting"
                        :class="{ 'opacity-70 cursor-not-allowed pointer-events-none': isSubmitting }"
                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-6 py-2.5 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-800 cursor-pointer"
                    >
                        <span x-show="!isSubmitting" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-paper-plane"></i> Kirim Request
                        </span>
                        <span x-show="isSubmitting" style="display: none;" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin"></i> Mengirim Request...
                        </span>
                    </button>
                </div>

            </form>

        </div>

    </div>

</x-layouts.seller>
