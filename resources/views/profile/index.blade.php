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
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white shadow-xs transition hover:bg-emerald-800"
                >
                    <i class="fa-solid fa-pen-to-square"></i> Edit Profil
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl border border-red-200 bg-red-50 px-5 py-2.5 text-sm font-bold text-red-600 transition hover:bg-red-100 hover:text-red-700 dark:border-red-900/50 dark:bg-red-950/40 dark:text-red-400"
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
                    <div class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-3xl bg-emerald-950 text-2xl font-black text-white shadow-md border border-emerald-500/30">
                        {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                    </div>

                    <h2 class="mt-4 text-xl font-extrabold text-slate-900 dark:text-white">
                        {{ auth()->user()->username }}
                    </h2>

                    <p class="mt-0.5 text-xs font-semibold text-slate-500 dark:text-slate-400">
                        NIS/NIP: {{ auth()->user()->nis_nip ?? '-' }}
                    </p>

                    <div class="mt-3 flex flex-wrap items-center justify-center gap-2">
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                            <i class="{{ auth()->user()->role === 'teacher' ? 'fa-solid fa-chalkboard-user' : 'fa-solid fa-graduation-cap' }} mr-1"></i>
                            {{ auth()->user()->role === 'teacher' ? 'Guru' : 'Siswa' }}
                        </span>
                    </div>
                </div>

                {{-- Detail Identitas Ringkas --}}
                <div class="mt-6 border-t border-slate-100 pt-5 space-y-3 text-sm dark:border-slate-800">
                    @if(auth()->user()->api_id)
                        <div class="flex justify-between">
                            <span class="text-xs font-medium text-slate-400">ID API Sekolah</span>
                            <span class="font-bold text-emerald-700 dark:text-emerald-400">#{{ auth()->user()->api_id }}</span>
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
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-xl text-slate-700">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </div>
                    <div class="mt-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Pesanan</p>
                        <p class="mt-1 text-3xl font-black text-slate-900 dark:text-white">{{ $totalOrders ?? 0 }}</p>
                    </div>
                </div>

                {{-- Pending Orders --}}
                <div class="flex flex-col justify-between rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-xl text-amber-600 dark:bg-amber-950/40">
                        <i class="fa-solid fa-clock font-bold"></i>
                    </div>
                    <div class="mt-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Menunggu Verifikasi</p>
                        <p class="mt-1 text-3xl font-black text-slate-900 dark:text-white">{{ $pendingOrders ?? 0 }}</p>
                    </div>
                </div>

                {{-- Completed Orders --}}
                <div class="flex flex-col justify-between rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-xl text-emerald-600 dark:bg-emerald-950/40">
                        <i class="fa-solid fa-circle-check font-bold"></i>
                    </div>
                    <div class="mt-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Pesanan Selesai</p>
                        <p class="mt-1 text-3xl font-black text-slate-900 dark:text-white">{{ $completedOrders ?? 0 }}</p>
                    </div>
                </div>

                {{-- Cart Count --}}
                <div class="flex flex-col justify-between rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-xl text-blue-600 dark:bg-blue-950/40">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                    <div class="mt-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Item Keranjang</p>
                        <p class="mt-1 text-3xl font-black text-slate-900 dark:text-white">{{ $cartCount ?? 0 }}</p>
                    </div>
                </div>
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

        {{-- Quick Access Menu --}}
        <div class="mt-8 rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <h3 class="font-bold text-slate-900 dark:text-white">Akses Cepat</h3>

            <div class="mt-4 space-y-2 text-sm font-medium">
                <a href="{{ route('products.index') }}" class="flex items-center justify-between rounded-2xl p-3 text-slate-700 transition hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800">
                    <span class="flex items-center gap-3 font-semibold"><i class="fa-solid fa-bag-shopping text-emerald-600 w-5"></i> Katalog Produk</span>
                    <i class="fa-solid fa-arrow-right text-slate-400 text-xs"></i>
                </a>
                <a href="{{ route('buyer.cart.index') }}" class="flex items-center justify-between rounded-2xl p-3 text-slate-700 transition hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800">
                    <span class="flex items-center gap-3 font-semibold"><i class="fa-solid fa-cart-shopping text-emerald-600 w-5"></i> Keranjang Belanja</span>
                    <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">{{ $cartCount ?? 0 }}</span>
                </a>
                <a href="{{ route('buyer.orders.index') }}" class="flex items-center justify-between rounded-2xl p-3 text-slate-700 transition hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800">
                    <span class="flex items-center gap-3 font-semibold"><i class="fa-solid fa-box text-emerald-600 w-5"></i> Riwayat Pesanan</span>
                    <i class="fa-solid fa-arrow-right text-slate-400 text-xs"></i>
                </a>
            </div>
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