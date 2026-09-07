<x-layouts.seller title="Pengaturan Toko & QRIS">

    <div class="mx-auto max-w-4xl space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                Pengaturan Toko & QRIS
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Kelola informasi toko, nomor WhatsApp, dan gambar barcode QRIS pembayaran Anda.
            </p>
        </div>

        @if (session('success'))
            <x-alert type="success" :message="session('success')" class="mb-4" />
        @endif

        @if ($errors->any())
            <x-alert type="error" :message="$errors->first()" class="mb-4" />
        @endif

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900 sm:p-8">

            <form
                action="{{ route('seller.profile.update') }}"
                method="POST"
                enctype="multipart/form-data"
                class="space-y-6"
            >
                @csrf
                @method('PUT')

                {{-- WhatsApp Number --}}
                <div>
                    <label for="whatsapp_number" class="block text-sm font-semibold text-slate-900 dark:text-white">
                        Nomor WhatsApp <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="whatsapp_number"
                        name="whatsapp_number"
                        value="{{ old('whatsapp_number', $seller->whatsapp_number) }}"
                        required
                        placeholder="Contoh: 081234567890"
                        class="mt-2 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    >
                    @error('whatsapp_number')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- QRIS Barcode Image Upload --}}
                <div>
                    <label for="qris_image" class="block text-sm font-semibold text-slate-900 dark:text-white">
                        Upload / Ubah Barcode QRIS Toko
                    </label>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Upload foto/gambar QRIS (GoPay/OVO/Dana/BCA/ShopeePay dll) milik toko Anda. Gambar ini akan tampil saat pembeli memilih pembayaran QRIS.
                    </p>

                    <input
                        type="file"
                        id="qris_image"
                        name="qris_image"
                        accept="image/*"
                        class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 file:mr-4 file:rounded-xl file:border-0 file:bg-emerald-600 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-white hover:file:bg-emerald-700 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    >

                    @if ($seller->qris_image)
                        <div class="mt-4 flex items-center gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-800/50">
                            <img
                                src="{{ Storage::url($seller->qris_image) }}"
                                alt="QRIS {{ $seller->user->username }}"
                                class="h-24 w-24 rounded-xl border border-slate-200 object-cover shadow-xs dark:border-slate-700"
                            >
                            <div>
                                <p class="text-xs font-bold text-slate-900 dark:text-white">Barcode QRIS Toko Aktif</p>
                                <p class="mt-0.5 text-xs text-slate-500">Pilih file baru diatas jika ingin mengganti barcode QRIS.</p>
                                <a
                                    href="{{ Storage::url($seller->qris_image) }}"
                                    target="_blank"
                                    class="mt-2 inline-block text-xs font-semibold text-emerald-600 hover:underline"
                                >
                                    🔍 Lihat Gambar Asli
                                </a>
                            </div>
                        </div>
                    @endif

                    @error('qris_image')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-semibold text-slate-900 dark:text-white">
                        Deskripsi Toko
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        placeholder="Tuliskan deskripsi singkat mengenai toko atau produk Anda..."
                        class="mt-2 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    >{{ old('description', $seller->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end border-t border-slate-100 pt-4 dark:border-slate-800">
                    <button
                        type="submit"
                        class="rounded-2xl bg-emerald-600 px-6 py-3 text-sm font-bold text-white shadow-xs transition hover:bg-emerald-700"
                    >
                        Simpan Pengaturan
                    </button>
                </div>

            </form>

        </div>

    </div>

</x-layouts.seller>
