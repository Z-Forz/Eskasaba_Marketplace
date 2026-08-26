<x-layouts.app title="Ajukan Menjadi Seller">
    <div class="mx-auto w-full max-w-2xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <a
                href="{{ route('profile.index') }}"
                class="inline-flex items-center text-sm text-slate-500 transition hover:text-slate-900"
            >
                <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Profil
            </a>

            <div class="mt-4">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    Ajukan Menjadi Seller
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Isi formulir berikut untuk mendaftar sebagai penjual di Eskasaba Marketplace.
                    Admin akan memverifikasi pengajuan Anda.
                </p>
            </div>
        </div>

        @if ($errors->any())
            <x-alert type="error" :message="$errors->first()" class="mb-6" />
        @endif

        {{-- Catatan revisi (jika sedang revisi) --}}
        @if ($seller?->needsRevision())
            <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-5">
                <p class="text-sm font-semibold text-amber-800">📋 Catatan dari Admin</p>
                <p class="mt-1 text-sm text-amber-700">{{ $seller->rejection_note }}</p>
                <p class="mt-2 text-xs text-amber-500">Perbaiki pengajuan sesuai catatan di atas, lalu kirim ulang.</p>
            </div>
        @endif

        {{-- Form --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

            <form
                method="POST"
                action="{{ route('buyer.apply-seller.store') }}"
                enctype="multipart/form-data"
                class="space-y-6"
            >
                @csrf

                {{-- WhatsApp Number (Otomatis dari Profil) --}}
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50/60 p-4 dark:border-emerald-900/50 dark:bg-emerald-950/30">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-300">
                                <i class="fa-brands fa-whatsapp text-sm mr-1"></i> Nomor WhatsApp Toko Pendaftar
                            </p>
                            <p class="mt-1 text-sm font-extrabold text-slate-900 dark:text-white">
                                {{ auth()->user()->phone ?? 'Belum diisi' }}
                            </p>
                            <p class="mt-0.5 text-[11px] text-slate-500 dark:text-slate-400">
                                Otomatis disinkronkan dengan Nomor HP pada Profil Anda.
                            </p>
                        </div>

                        <a
                            href="{{ route('profile.edit') }}"
                            class="inline-flex items-center justify-center gap-1 rounded-xl bg-white px-3 py-1.5 text-xs font-bold text-emerald-700 shadow-xs border border-emerald-200 hover:bg-emerald-100 dark:bg-slate-800 dark:text-emerald-300 dark:border-slate-700 shrink-0"
                        >
                            <i class="fa-solid fa-pen-to-square"></i> Ubah di Edit Profil
                        </a>
                    </div>
                </div>

                {{-- QRIS Image --}}
                <div>
                    <label
                        for="qris_image"
                        class="block text-sm font-medium text-slate-700"
                    >
                        Upload Gambar/Barcode QRIS Toko
                        <span class="text-xs text-slate-400 font-normal">(Sangat disarankan untuk metode QRIS)</span>
                    </label>

                    <p class="mt-1 text-xs text-slate-400">
                        Upload foto barcode QRIS (GoPay/OVO/Dana/BCA/ShopeePay dll) milik toko Anda agar pembeli dapat melakukan Scan QRIS saat checkout.
                    </p>

                    <input
                        type="file"
                        id="qris_image"
                        name="qris_image"
                        accept="image/*"
                        class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 file:mr-4 file:rounded-lg file:border-0 file:bg-slate-200 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-700 hover:file:bg-slate-300"
                    />

                    @if ($seller?->qris_image)
                        <div class="mt-3 flex items-center gap-3 rounded-2xl border border-slate-200 p-3 bg-slate-50">
                            <img src="{{ Storage::url($seller->qris_image) }}" alt="QRIS Toko" class="h-16 w-16 rounded-xl object-cover border border-slate-200">
                            <div>
                                <p class="text-xs font-bold text-slate-800">Barcode QRIS Toko Saat Ini</p>
                                <p class="text-[11px] text-slate-500">Pilih file gambar baru jika ingin mengganti QRIS.</p>
                            </div>
                        </div>
                    @endif

                    @error('qris_image')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Alasan --}}
                <div>
                    <label
                        for="reason"
                        class="block text-sm font-medium text-slate-700"
                    >
                        Mengapa Anda ingin menjadi seller?
                        <span class="text-red-500">*</span>
                    </label>

                    <p class="mt-1 text-xs text-slate-400">
                        Ceritakan motivasi dan tujuan Anda berjualan di marketplace ini.
                    </p>

                    <textarea
                        id="reason"
                        name="reason"
                        rows="4"
                        placeholder="Contoh: Saya ingin berjualan karena memiliki keahlian membuat kerajinan tangan dan ingin memanfaatkan platform ini untuk berbagi produk saya kepada teman-teman sekolah..."
                        class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 transition focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-200"
                    >{{ old('reason', $seller?->reason) }}</textarea>

                    @error('reason')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Disclaimer --}}
                <div class="rounded-xl bg-slate-50 p-4 text-xs leading-relaxed text-slate-500">
                    Dengan mengajukan formulir ini, Anda menyetujui bahwa data yang diisi adalah benar
                    dan bersedia mengikuti aturan marketplace yang berlaku di sekolah.
                </div>

                {{-- Submit --}}
                <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4">
                    <a
                        href="{{ route('profile.index') }}"
                        class="rounded-xl px-5 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                    >
                        {{ $seller?->needsRevision() ? 'Kirim Ulang Pengajuan' : 'Ajukan Sekarang' }}
                    </button>
                </div>

            </form>

        </div>

    </div>
</x-layouts.app>
