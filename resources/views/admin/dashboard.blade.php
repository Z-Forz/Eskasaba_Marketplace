<x-layouts.admin title="Dashboard Admin">

    <div class="space-y-8">

        {{-- Header --}}
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">
                <i class="fa-solid fa-gauge-high mr-1"></i> Marketplace Administration
            </p>

            <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-900 dark:text-white sm:text-3xl flex items-center gap-2">
                Halo, Admin <i class="fa-solid fa-user-shield text-emerald-600"></i>
            </h1>

            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Pantau statistik, kelola pengguna, verifikasi seller, dan awasi transaksi toko sekolah.
            </p>
        </div>

        {{-- Statistics Grid --}}
        <div class="grid gap-4 md:grid-cols-3">

            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Total Pengguna
                    </p>
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">
                        <i class="fa-solid fa-users text-lg"></i>
                    </div>
                </div>
                <p class="mt-3 text-3xl font-black text-slate-900 dark:text-white">
                    {{ number_format($totalUsers ?? 0) }}
                </p>
                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                    Siswa, Guru, & Pengguna terdaftar.
                </p>
            </div>

            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Total Seller Aktif
                    </p>
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">
                        <i class="fa-solid fa-store text-lg"></i>
                    </div>
                </div>
                <p class="mt-3 text-3xl font-black text-emerald-700 dark:text-emerald-400">
                    {{ number_format($totalSellers ?? 0) }}
                </p>
                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                    Toko terverifikasi yang sedang aktif.
                </p>
            </div>

            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Total Produk
                    </p>
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-purple-50 text-purple-600 dark:bg-purple-950/50 dark:text-purple-300">
                        <i class="fa-solid fa-boxes-stacked text-lg"></i>
                    </div>
                </div>
                <p class="mt-3 text-3xl font-black text-slate-900 dark:text-white">
                    {{ number_format($totalProducts ?? 0) }}
                </p>
                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                    Produk aktif di katalog marketplace.
                </p>
            </div>

        </div>

        {{-- Main Sections --}}
        <div class="grid gap-6 xl:grid-cols-[1.4fr_1fr]">

            {{-- Recent Orders --}}
            <section class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">

                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-receipt text-emerald-600"></i> Pesanan Terbaru
                        </h2>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                            Aktivitas transaksi terakhir antar siswa & seller.
                        </p>
                    </div>
                    <a
                        href="{{ route('admin.orders.index') }}"
                        class="rounded-2xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 inline-flex items-center gap-1"
                    >
                        Lihat Semua <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

                <div class="mt-6 space-y-3">

                    @forelse ($recentOrders ?? [] as $order)
                        <a
                            href="{{ route('admin.orders.show', $order) }}"
                            class="block rounded-2xl border border-slate-100 p-4 transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/60"
                        >
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">
                                        {{ $order->invoice_number ?? '#' . $order->id }}
                                    </p>
                                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                                        <i class="fa-solid fa-user mr-1 text-slate-400"></i> {{ $order->buyer?->username ?? $order->user?->username ?? 'Pembeli' }} • {{ ucfirst($order->status ?? '-') }}
                                    </p>
                                </div>
                                <p class="text-base font-black text-emerald-700 dark:text-emerald-400">
                                    Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}
                                </p>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-200 p-8 text-center text-xs text-slate-500 dark:border-slate-800">
                            Belum ada pesanan terbaru.
                        </div>
                    @endforelse

                </div>

            </section>

            {{-- Quick Admin Access --}}
            <section class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">

                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-warning text-amber-500"></i> Akses Penting
                        </h2>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                            Pintasan fitur pengelolaan admin.
                        </p>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700 dark:bg-amber-950/50 dark:text-amber-300">
                        {{ number_format($pendingOrders ?? 0) }} Pending
                    </span>
                </div>

                <div class="mt-6 space-y-3">
                    <a
                        href="{{ route('admin.users.index') }}"
                        class="group flex items-center gap-4 rounded-2xl border border-slate-100 p-4 transition hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/60"
                    >
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">
                            <i class="fa-solid fa-users text-base"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-slate-900 dark:text-white">Kelola Pengguna</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Tambahkan, edit, atau kelola user.</p>
                        </div>
                        <i class="fa-solid fa-arrow-right text-slate-400 group-hover:text-slate-600 transition"></i>
                    </a>

                    <a
                        href="{{ route('admin.categories.index') }}"
                        class="group flex items-center gap-4 rounded-2xl border border-slate-100 p-4 transition hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/60"
                    >
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">
                            <i class="fa-solid fa-layer-group text-base"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-slate-900 dark:text-white">Kelola Kategori</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Atur kategori produk marketplace.</p>
                        </div>
                        <i class="fa-solid fa-arrow-right text-slate-400 group-hover:text-slate-600 transition"></i>
                    </a>

                    <a
                        href="{{ route('admin.sellers.index') }}"
                        class="group flex items-center gap-4 rounded-2xl border border-slate-100 p-4 transition hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/60"
                    >
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-300">
                            <i class="fa-solid fa-user-check text-base"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-slate-900 dark:text-white">Verifikasi Seller</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Tinjau dan persetujuan seller baru.</p>
                        </div>
                        <i class="fa-solid fa-arrow-right text-slate-400 group-hover:text-slate-600 transition"></i>
                    </a>

                    <a
                        href="{{ route('admin.website-settings.index') }}"
                        class="group flex items-center gap-4 rounded-2xl border border-slate-100 p-4 transition hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/60"
                    >
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            <i class="fa-solid fa-gear text-base"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-slate-900 dark:text-white">Pengaturan Website</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Atur profil marketplace & landing page.</p>
                        </div>
                        <i class="fa-solid fa-arrow-right text-slate-400 group-hover:text-slate-600 transition"></i>
                    </a>
                </div>

            </section>

        </div>

    </div>

</x-layouts.admin>
