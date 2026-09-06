<x-layouts.app title="Profil & Dashboard">
    <div class="mx-auto w-full max-w-6xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Header & Action Buttons --}}
        <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">
                    Akun Saya
                </p>

                <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-900 dark:text-white sm:text-3xl">
                    Halo, {{ auth()->user()->username }}
                </h1>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Kelola profil, lihat statistik belanja, dan pantau pesanan Anda.
                </p>
            </div>

            {{-- Action Buttons: Edit Profil & Logout --}}
            <div class="flex flex-wrap items-center gap-3">
                <a
                    href="{{ route('profile.edit') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-emerald-600/20 transition hover:from-emerald-700 hover:to-teal-700 hover:shadow-lg"
                >
                    <i class="fa-solid fa-pen-to-square"></i> Edit Profil
                </a>

                <form id="logout-user-form" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="button"
                        onclick="confirmAction({ title: 'Konfirmasi Keluar', message: 'Apakah Anda yakin ingin keluar dari akun Anda?', form: 'logout-user-form', variant: 'warning', confirmText: 'Ya, Keluar' })"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl border border-red-200 bg-red-50 px-5 py-2.5 text-sm font-bold text-red-600 transition hover:bg-red-100 hover:text-red-700 dark:border-red-900/50 dark:bg-red-950/40 dark:text-red-400 cursor-pointer"
                    >
                        <i class="fa-solid fa-right-from-bracket"></i> Keluar
                    </button>
                </form>
            </div>
        </div>

        {{-- Top Section: Identity + Stats --}}
        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Profile Card --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <div class="flex flex-col items-center text-center">
                    <div class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 to-teal-700 text-2xl font-black text-white shadow-lg shadow-emerald-600/25 border-2 border-emerald-400/40">
                        {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                    </div>

                    <h2 class="mt-4 text-xl font-extrabold text-slate-900 dark:text-white">
                        {{ auth()->user()->username }}
                    </h2>

                    <p class="mt-0.5 text-xs font-semibold text-slate-500 dark:text-slate-400">
                        NIS/NIP: {{ auth()->user()->nis_nip ?? '-' }}
                    </p>

                    <div class="mt-3 flex flex-wrap items-center justify-center gap-2">
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-extrabold text-emerald-800 border border-emerald-200/80 dark:bg-emerald-950/60 dark:text-emerald-300">
                            <i class="{{ auth()->user()->role === 'teacher' ? 'fa-solid fa-chalkboard-user' : 'fa-solid fa-graduation-cap' }} mr-1 text-emerald-600"></i>
                            {{ auth()->user()->role === 'teacher' ? 'Guru' : 'Siswa' }}
                        </span>
                    </div>
                </div>

                {{-- Detail Identitas Ringkas --}}
                <div class="mt-6 border-t border-slate-100 pt-5 space-y-3 text-sm dark:border-slate-800">
                    @if(auth()->user()->class_room || auth()->user()->role === 'student')
                        <div class="flex justify-between">
                            <span class="text-xs font-medium text-slate-400">Kelas / Rombel</span>
                            <span class="font-bold text-emerald-700 dark:text-emerald-400">{{ auth()->user()->class_room ?? '-' }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between">
                        <span class="text-xs font-medium text-slate-400">Email</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ auth()->user()->email ?? '-' }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-xs font-medium text-slate-400">No. Telp</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ auth()->user()->phone ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- 4 Stat Cards --}}
            <div class="lg:col-span-2 grid grid-cols-2 gap-4">
                {{-- Total Orders --}}
                <div class="flex flex-col justify-between rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-xl text-emerald-700 border border-emerald-100">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </div>
                    <div class="mt-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Pesanan</p>
                        <p class="mt-1 text-3xl font-black text-slate-900 dark:text-white">{{ $totalOrders ?? 0 }}</p>
                    </div>
                </div>

                {{-- Pending Orders --}}
                <div class="flex flex-col justify-between rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-xl text-amber-600 border border-amber-100 dark:bg-amber-950/40">
                        <i class="fa-solid fa-clock font-bold"></i>
                    </div>
                    <div class="mt-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Menunggu Verifikasi</p>
                        <p class="mt-1 text-3xl font-black text-slate-900 dark:text-white">{{ $pendingOrders ?? 0 }}</p>
                    </div>
                </div>

                {{-- Completed Orders --}}
                <div class="flex flex-col justify-between rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-teal-50 text-xl text-teal-600 border border-teal-100 dark:bg-teal-950/40">
                        <i class="fa-solid fa-circle-check font-bold"></i>
                    </div>
                    <div class="mt-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Pesanan Selesai</p>
                        <p class="mt-1 text-3xl font-black text-slate-900 dark:text-white">{{ $completedOrders ?? 0 }}</p>
                    </div>
                </div>

                {{-- Cart Count --}}
                <div class="flex flex-col justify-between rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-xl text-blue-600 border border-blue-100 dark:bg-blue-950/40">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                    <div class="mt-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Item Keranjang</p>
                        <p class="mt-1 text-3xl font-black text-slate-900 dark:text-white">{{ $cartCount ?? 0 }}</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Highly Highlighted Quick Access Grid --}}
        <div class="mt-8">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-bolt text-emerald-600"></i> Akses Cepat & Menu Utama
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Pintas ke fitur belanja, keranjang, dan riwayat transaksi pesanan Anda.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                {{-- Quick Access 1: Katalog Produk --}}
                <a
                    href="{{ route('products.index') }}"
                    class="group relative flex flex-col justify-between overflow-hidden rounded-3xl border border-emerald-200/80 bg-gradient-to-br from-emerald-50/80 via-white to-emerald-50/30 p-6 shadow-xs transition hover:-translate-y-1 hover:border-emerald-400 hover:shadow-md dark:border-emerald-950 dark:from-emerald-950/40 dark:via-slate-900 dark:to-emerald-950/20"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-600/20 transition group-hover:scale-110">
                            <i class="fa-solid fa-store text-xl"></i>
                        </div>
                        <span class="text-xs font-bold text-emerald-700 opacity-80 group-hover:translate-x-1 transition dark:text-emerald-400">
                            Buka <i class="fa-solid fa-arrow-right ml-1"></i>
                        </span>
                    </div>

                    <div class="mt-5">
                        <h3 class="font-extrabold text-slate-900 group-hover:text-emerald-700 dark:text-white dark:group-hover:text-emerald-400 text-base">
                            Katalog Produk
                        </h3>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                            Jelajahi berbagai makanan & produk karya warga sekolah.
                        </p>
                    </div>
                </a>

                {{-- Quick Access 2: Keranjang Belanja --}}
                <a
                    href="{{ route('buyer.cart.index') }}"
                    class="group relative flex flex-col justify-between overflow-hidden rounded-3xl border border-blue-200/80 bg-gradient-to-br from-blue-50/80 via-white to-blue-50/30 p-6 shadow-xs transition hover:-translate-y-1 hover:border-blue-400 hover:shadow-md dark:border-blue-950 dark:from-blue-950/40 dark:via-slate-900 dark:to-blue-950/20"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-md shadow-blue-500/20 transition group-hover:scale-110">
                            <i class="fa-solid fa-cart-shopping text-xl"></i>
                        </div>
                        <span class="rounded-full bg-blue-600 px-2.5 py-0.5 text-xs font-black text-white shadow-xs">
                            {{ $cartCount ?? 0 }} Item
                        </span>
                    </div>

                    <div class="mt-5">
                        <h3 class="font-extrabold text-slate-900 group-hover:text-blue-600 dark:text-white dark:group-hover:text-blue-400 text-base">
                            Keranjang Belanja
                        </h3>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                            Cek item pilihanmu sebelum checkout pesanan.
                        </p>
                    </div>
                </a>

                {{-- Quick Access 3: Riwayat Pesanan --}}
                <a
                    href="{{ route('buyer.orders.index') }}"
                    class="group relative flex flex-col justify-between overflow-hidden rounded-3xl border border-purple-200/80 bg-gradient-to-br from-purple-50/80 via-white to-purple-50/30 p-6 shadow-xs transition hover:-translate-y-1 hover:border-purple-400 hover:shadow-md dark:border-purple-950 dark:from-purple-950/40 dark:via-slate-900 dark:to-purple-950/20"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-purple-500 to-violet-600 text-white shadow-md shadow-purple-500/20 transition group-hover:scale-110">
                            <i class="fa-solid fa-box-open text-xl"></i>
                        </div>
                        <span class="text-xs font-bold text-purple-700 opacity-80 group-hover:translate-x-1 transition dark:text-purple-400">
                            Lihat <i class="fa-solid fa-arrow-right ml-1"></i>
                        </span>
                    </div>

                    <div class="mt-5">
                        <h3 class="font-extrabold text-slate-900 group-hover:text-purple-600 dark:text-white dark:group-hover:text-purple-400 text-base">
                            Riwayat Pesanan
                        </h3>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                            Pantau status & konfirmasi lokasi pengambilan barang.
                        </p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Seller Card or Application Banner --}}
        <div class="mt-8">
            @if (auth()->user()->seller?->status === 'approved')
                {{-- Approved Seller Panel Access --}}
                <div class="flex flex-col justify-between gap-6 rounded-3xl border border-emerald-200 bg-emerald-50/60 p-6 shadow-xs dark:border-emerald-950 dark:bg-emerald-950/20 sm:flex-row sm:items-center sm:p-8">
                    <div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                            <i class="fa-solid fa-store"></i> Toko Anda Aktif
                        </span>
                        <h2 class="mt-3 text-xl font-black text-slate-900 dark:text-white sm:text-2xl">
                            Kelola Produk & Jualan Anda
                        </h2>
                        <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                            Akses dashboard seller untuk menambah produk baru, mengonfirmasi pesanan, dan mengatur QRIS.
                        </p>
                    </div>

                    <a
                        href="{{ route('seller.dashboard') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-6 py-3.5 text-sm font-bold text-white shadow-xs transition hover:bg-emerald-800 shrink-0"
                    >
                        Masuk Panel Seller <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            @elseif (auth()->user()->seller?->status === 'pending')
                {{-- Pending Verification Banner --}}
                <div class="rounded-3xl border border-amber-200 bg-amber-50/60 p-6 shadow-xs dark:border-amber-950 dark:bg-amber-950/20 sm:p-8">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-800 font-bold dark:bg-amber-900 dark:text-amber-200">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                                Pengajuan Seller Sedang Diverifikasi Admin
                            </h2>
                            <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-300">
                                Permohonan Anda untuk menjadi seller sedang ditinjau oleh pihak admin sekolah. Mohon tunggu konfirmasi.
                            </p>
                        </div>
                    </div>
                </div>
            @else
                {{-- Apply to be a seller banner --}}
                <div class="flex flex-col justify-between gap-6 rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900 sm:flex-row sm:items-center sm:p-8">
                    <div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            <i class="fa-solid fa-store"></i> Peluang Usaha Sekolah
                        </span>
                        <h2 class="mt-3 text-xl font-black text-slate-900 dark:text-white sm:text-2xl">
                            Tertarik Berjualan di Eskasaba Market?
                        </h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Daftarkan toko Anda untuk mulai mempromosikan dan menjual barang secara online kepada sesama warga sekolah.
                        </p>
                    </div>

                    <a
                        href="{{ route('buyer.apply-seller') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-900 px-6 py-3.5 text-sm font-bold text-white shadow-xs transition hover:bg-slate-800 dark:bg-emerald-700 dark:hover:bg-emerald-800 shrink-0"
                    >
                        Daftar Jadi Seller Toko <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            @endif
        </div>

        {{-- Activity Logs & Login History Card --}}
        <div class="mt-8 rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved text-emerald-600"></i> Riwayat Login & Keamanan Akun
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Daftar perangkat & IP yang baru-baru ini mengakses atau mengubah akun Anda.
                    </p>
                </div>

                <a href="{{ route('profile.activity-logs') }}" class="text-xs font-bold text-emerald-700 hover:underline dark:text-emerald-400 shrink-0 inline-flex items-center gap-1">
                    Lihat Semua <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="mt-4 divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                @forelse ($recentActivityLogs ?? [] as $log)
                    <div class="py-3 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-sm dark:bg-slate-800">
                                <i class="{{ $log->icon }}"></i>
                            </div>

                            <div class="min-w-0">
                                <p class="font-bold text-slate-900 dark:text-white text-xs truncate">
                                    {{ $log->description }}
                                </p>
                                <p class="text-[11px] text-slate-400 truncate">
                                    <i class="fa-solid fa-laptop text-[10px]"></i> {{ $log->device }} • IP: {{ $log->ip_address ?? '127.0.0.1' }}
                                </p>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">
                                {{ $log->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="py-4 text-center text-xs text-slate-400">
                        Belum ada riwayat aktivitas yang tercatat.
                    </p>
                @endforelse
            </div>
        </div>

    </div>
</x-layouts.app>