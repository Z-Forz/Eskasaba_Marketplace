<x-layouts.admin title="Detail Verifikasi Seller">

    <div class="space-y-6">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <a
                    href="{{ route('admin.sellers.index') }}"
                    class="text-xs font-bold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white flex items-center gap-1.5"
                >
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Seller
                </a>

                <h1 class="mt-3 text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-user-check text-emerald-600"></i> Detail & Verifikasi Seller: {{ $seller->user?->username }}
                </h1>

            </div>

        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- Profile --}}
            <section class="rounded-3xl border border-slate-200/80 bg-white p-5 sm:p-6 lg:col-span-1 dark:border-slate-800 dark:bg-slate-900 shadow-xs">

                <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-emerald-800 text-2xl font-black text-white shadow-xs">
                    {{ strtoupper(substr($seller->user?->username ?? 'S', 0, 1)) }}
                </div>

                <h2 class="mt-5 text-xl font-black text-slate-900 dark:text-white">
                    {{ $seller->user?->username ?? '-' }}
                </h2>

                <p class="mt-1 break-all text-xs font-medium text-slate-500 dark:text-slate-400">
                    <i class="fa-solid fa-envelope mr-1 text-slate-400"></i> {{ $seller->user?->email ?? '-' }}
                </p>

                <div class="mt-4">

                    <span class="rounded-full px-3 py-1 text-xs font-bold
                        {{ $seller->status === 'approved'
                            ? 'bg-green-100 text-green-700 dark:bg-green-950/40 dark:text-green-400'
                            : ($seller->status === 'rejected'
                                ? 'bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-400'
                                : ($seller->status === 'revision'
                                    ? 'bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400'
                                    : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-950/40 dark:text-yellow-400')) }}"
                    >
                        <i class="fa-solid fa-circle-info mr-1"></i> Status: {{ $seller->statusLabel() }}
                    </span>

                </div>

            </section>

            {{-- Information --}}
            <section class="rounded-3xl border border-slate-200/80 bg-white p-5 sm:p-6 lg:col-span-2 dark:border-slate-800 dark:bg-slate-900 shadow-xs">

                <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-store text-emerald-600"></i> Informasi Toko & WhatsApp
                </h2>

                <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">

                    <div>
                        <p class="text-xs font-bold text-slate-400">
                            Nomor WhatsApp Toko
                        </p>

                        <p class="mt-1 text-sm font-bold text-slate-800 dark:text-slate-200">
                            @if ($seller->whatsapp_number)
                                <a
                                    href="https://wa.me/{{ preg_replace('/\D/', '', $seller->whatsapp_number) }}"
                                    target="_blank"
                                    class="inline-flex items-center gap-1.5 text-emerald-700 hover:underline dark:text-emerald-400"
                                >
                                    <i class="fa-brands fa-whatsapp text-base"></i> {{ $seller->whatsapp_number }}
                                </a>
                            @else
                                -
                            @endif
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold text-slate-400">
                            Tanggal Pendaftaran
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-800 dark:text-slate-200">
                            <i class="fa-solid fa-calendar-days text-slate-400 mr-1"></i> {{ $seller->created_at?->format('d F Y H:i') ?? '-' }}
                        </p>
                    </div>

                    <div class="sm:col-span-2">

                        <p class="text-xs font-bold text-slate-400">
                            Deskripsi Toko
                        </p>

                        <p class="mt-1 text-sm leading-relaxed text-slate-700 dark:text-slate-300">
                            {{ $seller->description ?: 'Belum ada deskripsi toko.' }}
                        </p>

                    </div>

                    @if ($seller->approved_at)

                        <div>

                            <p class="text-xs font-bold text-slate-400">
                                Waktu Disetujui Admin
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-800 dark:text-slate-200">
                                <i class="fa-solid fa-clock-check text-emerald-600 mr-1"></i> {{ $seller->approved_at->format('d F Y H:i') }}
                            </p>

                        </div>

                    @endif

                </div>

            </section>

        </div>

        {{-- School identity --}}
        @if ($seller->user?->nis_nip)

            <section class="rounded-3xl border border-slate-200/80 bg-white p-5 sm:p-6 dark:border-slate-800 dark:bg-slate-900 shadow-xs">

                <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-graduation-cap text-emerald-600"></i> Identitas Sekolah (Warga Sekolah)
                </h2>

                <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">

                    <div>
                        <p class="text-xs font-bold text-slate-400">Username</p>
                        <p class="mt-1 text-sm font-bold dark:text-slate-200">
                            {{ $seller->user->username }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold text-slate-400">NIS / NIP</p>
                        <p class="mt-1 text-sm font-bold dark:text-slate-200">
                            {{ $seller->user->nis_nip }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold text-slate-400">Kelas</p>
                        <p class="mt-1 text-sm font-bold dark:text-slate-200">
                            {{ $seller->user->class ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold text-slate-400">Jurusan / Mapel</p>
                        <p class="mt-1 text-sm font-bold dark:text-slate-200">
                            {{ $seller->user->major ?? '-' }}
                        </p>
                    </div>

                </div>

            </section>

        @endif

        {{-- Application Info --}}
        @if ($seller->reason || $seller->products_plan || $seller->qris_image)

            <section class="rounded-3xl border border-slate-200/80 bg-white p-5 sm:p-6 dark:border-slate-800 dark:bg-slate-900 shadow-xs">

                <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-qrcode text-emerald-600"></i> Detail Pengajuan & Barcode QRIS Toko
                </h2>

                <div class="mt-5 space-y-5">

                    @if ($seller->qris_image)
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Gambar Barcode QRIS Toko</p>
                            <div class="mt-3 flex items-center gap-4">
                                <img src="{{ Storage::url($seller->qris_image) }}" alt="QRIS {{ $seller->user?->username }}" class="h-40 w-40 rounded-2xl border border-slate-200 object-cover shadow-xs dark:border-slate-700 bg-white p-2">
                                <a href="{{ Storage::url($seller->qris_image) }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                    <i class="fa-solid fa-magnifying-glass-plus"></i> Lihat Ukuran Penuh
                                </a>
                            </div>
                        </div>
                    @endif

                    @if ($seller->reason)
                        <div>
                            <p class="text-xs font-bold text-slate-400">Alasan Ingin Menjadi Seller</p>
                            <p class="mt-1 text-sm leading-relaxed text-slate-700 dark:text-slate-300">
                                {{ $seller->reason }}
                            </p>
                        </div>
                    @endif

                    @if ($seller->products_plan)
                        <div>
                            <p class="text-xs font-bold text-slate-400">Rencana Produk yang Akan Dijual</p>
                            <p class="mt-1 text-sm leading-relaxed text-slate-700 dark:text-slate-300">
                                {{ $seller->products_plan }}
                            </p>
                        </div>
                    @endif

                    @if ($seller->rejection_note)
                        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/50 dark:bg-amber-950/30">
                            <p class="text-xs font-bold text-amber-800 dark:text-amber-400 flex items-center gap-1">
                                <i class="fa-solid fa-note-sticky"></i> Catatan Revisi / Penolakan Admin:
                            </p>
                            <p class="mt-1 text-sm font-semibold text-amber-700 dark:text-amber-300">{{ $seller->rejection_note }}</p>
                        </div>
                    @endif

                </div>

            </section>

        @endif

        {{-- Verification Panel --}}
        <section class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">

            <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-4 dark:border-slate-800">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-gavel text-emerald-600"></i> Panel Aksi Verifikasi Seller
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Kelola status verifikasi pendaftaran seller di bawah ini.
                    </p>
                </div>

                <span class="rounded-full px-3 py-1 text-xs font-bold
                    {{ $seller->status === 'approved'
                        ? 'bg-green-100 text-green-700 dark:bg-green-950/40 dark:text-green-400'
                        : ($seller->status === 'rejected'
                            ? 'bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-400'
                            : ($seller->status === 'revision'
                                ? 'bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400'
                                : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-950/40 dark:text-yellow-400')) }}"
                >
                    {{ $seller->statusLabel() }}
                </span>
            </div>

            <div class="mt-6 flex flex-wrap items-center gap-3">

                @if (! $seller->isApproved())

                    {{-- Setujui Button --}}
                    <button
                        type="button"
                        onclick="document.getElementById('modal-approve').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 rounded-2xl bg-emerald-700 px-5 py-3 text-sm font-bold text-white transition hover:bg-emerald-800 shadow-xs"
                    >
                        <i class="fa-solid fa-check"></i> Setujui Pengajuan Seller
                    </button>

                    {{-- Minta Revisi Button --}}
                    <button
                        type="button"
                        onclick="document.getElementById('modal-revision').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 rounded-2xl bg-amber-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-amber-700 shadow-xs"
                    >
                        <i class="fa-solid fa-pen-to-square"></i> Minta Revisi
                    </button>

                    {{-- Tolak Button --}}
                    <button
                        type="button"
                        onclick="document.getElementById('modal-reject').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 rounded-2xl bg-red-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-red-700 shadow-xs"
                    >
                        <i class="fa-solid fa-xmark"></i> Tolak Pengajuan
                    </button>

                @else

                    {{-- Already approved: Revoke status button --}}
                    <button
                        type="button"
                        onclick="document.getElementById('modal-revoke').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 rounded-2xl border border-red-200 bg-red-50 px-5 py-3 text-sm font-bold text-red-600 transition hover:bg-red-100 dark:border-red-900 dark:bg-red-950/30 dark:text-red-400"
                    >
                        <i class="fa-solid fa-ban"></i> Cabut Status Seller
                    </button>

                @endif

            </div>

        </section>

    </div>

    {{-- Custom Modal: Setujui --}}
    <div
        id="modal-approve"
        class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs"
    >
        <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-3 text-emerald-600">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-100 dark:bg-emerald-950/50">
                    <i class="fa-solid fa-check text-lg"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Setujui Pengajuan Seller</h3>
            </div>

            <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">
                Apakah Anda yakin ingin menyetujui pengajuan seller untuk <strong>{{ $seller->user?->username }}</strong>?
                User akan langsung mendapatkan akses ke dashboard seller.
            </p>

            <form
                method="POST"
                action="{{ route('admin.sellers.approve', $seller) }}"
                class="mt-6 flex justify-end gap-3"
            >
                @csrf
                <button
                    type="button"
                    onclick="document.getElementById('modal-approve').classList.add('hidden')"
                    class="rounded-2xl px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="rounded-2xl bg-emerald-700 px-5 py-2.5 text-xs font-bold text-white hover:bg-emerald-800 shadow-xs flex items-center gap-1.5"
                >
                    <i class="fa-solid fa-check"></i> Ya, Setujui
                </button>
            </form>
        </div>
    </div>

    {{-- Custom Modal: Minta Revisi --}}
    <div
        id="modal-revision"
        class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs"
    >
        <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-3 text-amber-600">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-100 dark:bg-amber-950/50">
                    <i class="fa-solid fa-pen-to-square text-lg"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Minta Revisi Pengajuan</h3>
            </div>

            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                Tuliskan catatan revisi mengenai hal yang perlu diperbaiki oleh <strong>{{ $seller->user?->username }}</strong>.
            </p>

            <form
                method="POST"
                action="{{ route('admin.sellers.revision', $seller) }}"
                class="mt-5 space-y-4"
            >
                @csrf

                <textarea
                    name="rejection_note"
                    rows="4"
                    required
                    placeholder="Contoh: Alasan pengajuan kurang jelas. Mohon lengkapi gambar QRIS toko..."
                    class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium focus:border-amber-500 focus:outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                ></textarea>

                <div class="flex justify-end gap-3">
                    <button
                        type="button"
                        onclick="document.getElementById('modal-revision').classList.add('hidden')"
                        class="rounded-2xl px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="rounded-2xl bg-amber-600 px-5 py-2.5 text-xs font-bold text-white hover:bg-amber-700 shadow-xs flex items-center gap-1.5"
                    >
                        <i class="fa-solid fa-paper-plane"></i> Kirim Permintaan Revisi
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- Custom Modal: Tolak --}}
    <div
        id="modal-reject"
        class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs"
    >
        <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-3 text-red-600">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-red-100 dark:bg-red-950/50">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tolak Pengajuan Seller</h3>
            </div>

            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                Tuliskan alasan penolakan pengajuan untuk <strong>{{ $seller->user?->username }}</strong>.
            </p>

            <form
                method="POST"
                action="{{ route('admin.sellers.reject', $seller) }}"
                class="mt-5 space-y-4"
            >
                @csrf

                <textarea
                    name="rejection_note"
                    rows="4"
                    required
                    placeholder="Contoh: Pengajuan tidak memenuhi syarat karena produk tidak sesuai ketentuan..."
                    class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium focus:border-red-500 focus:outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                ></textarea>

                <div class="flex justify-end gap-3">
                    <button
                        type="button"
                        onclick="document.getElementById('modal-reject').classList.add('hidden')"
                        class="rounded-2xl px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="rounded-2xl bg-red-600 px-5 py-2.5 text-xs font-bold text-white hover:bg-red-700 shadow-xs flex items-center gap-1.5"
                    >
                        <i class="fa-solid fa-ban"></i> Tolak Pengajuan
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- Custom Modal: Cabut Status --}}
    <div
        id="modal-revoke"
        class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs"
    >
        <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-3 text-red-600">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-red-100 dark:bg-red-950/50">
                    <i class="fa-solid fa-user-slash text-lg"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Cabut Status Seller</h3>
            </div>

            <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">
                Apakah Anda yakin ingin mencabut status seller dari <strong>{{ $seller->user?->username }}</strong>?
            </p>

            <form
                method="POST"
                action="{{ route('admin.sellers.reject', $seller) }}"
                class="mt-6 space-y-4"
            >
                @csrf
                <input type="hidden" name="rejection_note" value="Status seller dicabut oleh admin.">

                <div class="flex justify-end gap-3">
                    <button
                        type="button"
                        onclick="document.getElementById('modal-revoke').classList.add('hidden')"
                        class="rounded-2xl px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="rounded-2xl bg-red-600 px-5 py-2.5 text-xs font-bold text-white hover:bg-red-700 shadow-xs flex items-center gap-1.5"
                    >
                        <i class="fa-solid fa-user-slash"></i> Ya, Cabut Status
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-layouts.admin>