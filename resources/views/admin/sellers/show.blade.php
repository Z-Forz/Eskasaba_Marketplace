<x-layouts.admin title="Detail Seller">

    <div class="space-y-6">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <a
                    href="{{ route('admin.sellers.index') }}"
                    class="text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                >
                    ← Kembali ke seller
                </a>

                <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">
                    Detail Seller
                </h1>

            </div>

            <a
                href="{{ route('admin.sellers.edit', $seller) }}"
                class="rounded-xl bg-gray-900 px-5 py-3 text-center text-sm font-semibold text-white dark:bg-white dark:text-gray-900"
            >
                Kelola Seller
            </a>

        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- Profile --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 lg:col-span-1 dark:border-gray-700 dark:bg-gray-900">

                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-emerald-800 text-2xl font-bold text-white shadow-xs">
                    {{ strtoupper(substr($seller->user?->username ?? 'S', 0, 1)) }}
                </div>

                <h2 class="mt-5 text-xl font-bold text-gray-900 dark:text-white">
                    {{ $seller->user?->username ?? '-' }}
                </h2>

                <p class="mt-1 break-all text-sm text-gray-500 dark:text-gray-400">
                    {{ $seller->user?->email ?? '-' }}
                </p>

                <div class="mt-4">

                    <span class="rounded-full px-3 py-1 text-xs font-semibold
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

            </section>

            {{-- Information --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 lg:col-span-2 dark:border-gray-700 dark:bg-gray-900">

                <h2 class="font-semibold text-gray-900 dark:text-white">
                    Informasi Seller
                </h2>

                <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">

                    <div>
                        <p class="text-xs text-gray-400">
                            Nomor WhatsApp
                        </p>

                        <p class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                            {{ $seller->whatsapp_number ?: '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400">
                            Tanggal Pendaftaran
                        </p>

                        <p class="mt-1 text-sm text-gray-800 dark:text-gray-200">
                            {{ $seller->created_at?->format('d F Y H:i') ?? '-' }}
                        </p>
                    </div>

                    <div class="sm:col-span-2">

                        <p class="text-xs text-gray-400">
                            Deskripsi Toko
                        </p>

                        <p class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300">
                            {{ $seller->description ?: 'Belum ada deskripsi toko.' }}
                        </p>

                    </div>

                    @if ($seller->approved_at)

                        <div>

                            <p class="text-xs text-gray-400">
                                Waktu Disetujui
                            </p>

                            <p class="mt-1 text-sm text-gray-800 dark:text-gray-200">
                                {{ $seller->approved_at->format('d F Y H:i') }}
                            </p>

                        </div>

                    @endif

                </div>

            </section>

        </div>

        {{-- School identity --}}
        @if ($seller->user?->nis_nip)

            <section class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-700 dark:bg-gray-900">

                <h2 class="font-semibold text-gray-900 dark:text-white">
                    Identitas Sekolah
                </h2>

                <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">

                    <div>
                        <p class="text-xs text-gray-400">Username</p>
                        <p class="mt-1 text-sm font-medium dark:text-gray-200">
                            {{ $seller->user->username }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400">NIS / NIP</p>
                        <p class="mt-1 text-sm font-medium dark:text-gray-200">
                            {{ $seller->user->nis_nip }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400">Kelas</p>
                        <p class="mt-1 text-sm font-medium dark:text-gray-200">
                            {{ $seller->user->class ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400">Jurusan</p>
                        <p class="mt-1 text-sm font-medium dark:text-gray-200">
                            {{ $seller->user->major ?? '-' }}
                        </p>
                    </div>

                </div>

            </section>

        @endif

        {{-- Application Info --}}
        @if ($seller->reason || $seller->products_plan)

            <section class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-700 dark:bg-gray-900">

                <h2 class="font-semibold text-gray-900 dark:text-white">
                    Detail Pengajuan
                </h2>

                <div class="mt-5 space-y-5">

                    @if ($seller->reason)
                        <div>
                            <p class="text-xs text-gray-400">Alasan Ingin Menjadi Seller</p>
                            <p class="mt-2 text-sm leading-6 text-gray-700 dark:text-gray-300">
                                {{ $seller->reason }}
                            </p>
                        </div>
                    @endif

                    @if ($seller->products_plan)
                        <div>
                            <p class="text-xs text-gray-400">Rencana Produk yang Akan Dijual</p>
                            <p class="mt-2 text-sm leading-6 text-gray-700 dark:text-gray-300">
                                {{ $seller->products_plan }}
                            </p>
                        </div>
                    @endif

                    @if ($seller->rejection_note)
                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/50 dark:bg-amber-950/30">
                            <p class="text-xs font-semibold text-amber-800 dark:text-amber-400">📋 Catatan Revisi / Penolakan</p>
                            <p class="mt-1 text-sm text-amber-700 dark:text-amber-300">{{ $seller->rejection_note }}</p>
                        </div>
                    @endif

                </div>

            </section>

        @endif

        {{-- Interactive Verification Card Panel --}}
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">

            <div class="flex items-center justify-between gap-4 border-b border-gray-100 pb-4 dark:border-gray-800">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                        Kartu Verifikasi Seller
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Kelola status verifikasi pendaftaran seller di bawah ini.
                    </p>
                </div>

                <span class="rounded-full px-3 py-1 text-xs font-semibold
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
                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700 shadow-xs"
                    >
                        <span>✓</span> Setujui Pengajuan
                    </button>

                    {{-- Minta Revisi Button --}}
                    <button
                        type="button"
                        onclick="document.getElementById('modal-revision').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-600 shadow-xs"
                    >
                        <span>✎</span> Minta Revisi
                    </button>

                    {{-- Tolak Button --}}
                    <button
                        type="button"
                        onclick="document.getElementById('modal-reject').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700 shadow-xs"
                    >
                        <span>✕</span> Tolak Pengajuan
                    </button>

                @else

                    {{-- Already approved: Revoke status button --}}
                    <button
                        type="button"
                        onclick="document.getElementById('modal-revoke').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-5 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-100 dark:border-red-900 dark:bg-red-950/30 dark:text-red-400"
                    >
                        <span>🚫</span> Cabut Status Seller
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
        <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl dark:bg-gray-900">
            <div class="flex items-center gap-3 text-emerald-600">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-100 dark:bg-emerald-950/50">
                    ✓
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Setujui Pengajuan Seller</h3>
            </div>

            <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                Apakah Anda yakin ingin menyetujui pengajuan seller untuk <strong>{{ $seller->user->username }}</strong>?
                User akan langsung mendapatkan akses panel seller dan mulai menjual produk.
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
                    class="rounded-xl px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 shadow-xs"
                >
                    Ya, Setujui
                </button>
            </form>
        </div>
    </div>

    {{-- Custom Modal: Minta Revisi --}}
    <div
        id="modal-revision"
        class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs"
    >
        <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl dark:bg-gray-900">
            <div class="flex items-center gap-3 text-amber-600">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-100 dark:bg-amber-950/50">
                    ✎
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Minta Revisi Pengajuan</h3>
            </div>

            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Tuliskan catatan detail mengenai apa yang perlu diperbaiki oleh <strong>{{ $seller->user->username }}</strong>.
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
                    placeholder="Contoh: Alasan yang diberikan kurang detail. Mohon jelaskan lebih lanjut jenis produk yang akan dijual..."
                    class="block w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                ></textarea>

                <div class="flex justify-end gap-3">
                    <button
                        type="button"
                        onclick="document.getElementById('modal-revision').classList.add('hidden')"
                        class="rounded-xl px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="rounded-xl bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-600 shadow-xs"
                    >
                        Kirim Permintaan Revisi
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
        <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl dark:bg-gray-900">
            <div class="flex items-center gap-3 text-red-600">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-red-100 dark:bg-red-950/50">
                    ✕
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Tolak Pengajuan Seller</h3>
            </div>

            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Tuliskan alasan penolakan pengajuan untuk <strong>{{ $seller->user->username }}</strong>.
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
                    placeholder="Contoh: Pengajuan tidak memenuhi syarat karena produk yang direncanakan tidak sesuai ketentuan marketplace..."
                    class="block w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                ></textarea>

                <div class="flex justify-end gap-3">
                    <button
                        type="button"
                        onclick="document.getElementById('modal-reject').classList.add('hidden')"
                        class="rounded-xl px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700 shadow-xs"
                    >
                        Tolak Pengajuan
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
        <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl dark:bg-gray-900">
            <div class="flex items-center gap-3 text-red-600">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-red-100 dark:bg-red-950/50">
                    🚫
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Cabut Status Seller</h3>
            </div>

            <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                Apakah Anda yakin ingin mencabut status seller dari <strong>{{ $seller->user->username }}</strong>?
                Seller ini tidak akan lagi dapat mengelola toko atau menambah produk baru.
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
                        class="rounded-xl px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700 shadow-xs"
                    >
                        Ya, Cabut Status
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-layouts.admin>