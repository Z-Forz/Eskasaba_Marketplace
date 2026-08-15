<x-layouts.admin title="Dashboard Admin">

    <div class="grid gap-4 md:grid-cols-3">

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-500">
                Total Pengguna
            </p>
            <p class="mt-4 text-4xl font-bold text-slate-900">
                {{ number_format($totalUsers ?? 0) }}
            </p>
            <p class="mt-2 text-sm text-slate-500">
                Jumlah pengguna terdaftar di marketplace.
            </p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-500">
                Total Seller
            </p>
            <p class="mt-4 text-4xl font-bold text-slate-900">
                {{ number_format($totalSellers ?? 0) }}
            </p>
            <p class="mt-2 text-sm text-slate-500">
                Seller yang sudah terdaftar dalam sistem.
            </p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-500">
                Total Produk
            </p>
            <p class="mt-4 text-4xl font-bold text-slate-900">
                {{ number_format($totalProducts ?? 0) }}
            </p>
            <p class="mt-2 text-sm text-slate-500">
                Produk yang tersedia di marketplace.
            </p>
        </div>

    </div>

    <div class="grid gap-4 xl:grid-cols-[1.4fr_1fr]">

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Pesanan Terbaru
                    </p>
                    <h2 class="mt-2 text-2xl font-bold text-slate-900">
                        Aktivitas pesanan terakhir
                    </h2>
                </div>
                <a
                    href="{{ route('admin.orders.index') }}"
                    class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                >
                    Lihat semua
                </a>
            </div>

            <div class="mt-6 space-y-4">

                @forelse ($recentOrders ?? [] as $order)
                    <a
                        href="{{ route('admin.orders.show', $order) }}"
                        class="block rounded-3xl border border-slate-200 p-4 transition hover:border-slate-300 hover:bg-slate-50"
                    >
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">
                                    #{{ $order->id }}
                                </p>
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $order->buyer->name ?? 'Pembeli' }} • {{ ucfirst($order->status ?? '-') }}
                                </p>
                            </div>
                            <p class="text-lg font-bold text-slate-900">
                                Rp {{ number_format($order->total_amount ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                    </a>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-200 p-10 text-center text-sm text-slate-500">
                        Tidak ada pesanan baru.
                    </div>
                @endforelse

            </div>

        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Tindakan Cepat
                    </p>
                    <h2 class="mt-2 text-2xl font-bold text-slate-900">
                        Akses admin
                    </h2>
                </div>
                <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-sm font-semibold text-amber-700">
                    {{ number_format($pendingOrders ?? 0) }} Pending
                </span>
            </div>

            <div class="mt-6 space-y-4">
                <a
                    href="{{ route('admin.users.index') }}"
                    class="block rounded-3xl border border-slate-200 p-4 transition hover:border-slate-300 hover:bg-slate-50"
                >
                    <p class="text-sm font-semibold text-slate-900">Kelola Pengguna</p>
                    <p class="mt-1 text-sm text-slate-500">Tambahkan, edit, atau hapus user.</p>
                </a>
                <a
                    href="{{ route('admin.categories.index') }}"
                    class="block rounded-3xl border border-slate-200 p-4 transition hover:border-slate-300 hover:bg-slate-50"
                >
                    <p class="text-sm font-semibold text-slate-900">Kelola Kategori</p>
                    <p class="mt-1 text-sm text-slate-500">Atur kategori produk marketplace.</p>
                </a>
                <a
                    href="{{ route('admin.sellers.index') }}"
                    class="block rounded-3xl border border-slate-200 p-4 transition hover:border-slate-300 hover:bg-slate-50"
                >
                    <p class="text-sm font-semibold text-slate-900">Verifikasi Seller</p>
                    <p class="mt-1 text-sm text-slate-500">Tinjau dan setujui seller baru.</p>
                </a>
                <a
                    href="{{ route('admin.website-settings.index') }}"
                    class="block rounded-3xl border border-slate-200 p-4 transition hover:border-slate-300 hover:bg-slate-50"
                >
                    <p class="text-sm font-semibold text-slate-900">Pengaturan Website</p>
                    <p class="mt-1 text-sm text-slate-500">Ubah nama, logo, hero, dan footer.</p>
                </a>
            </div>

        </section>

    </div>

</x-layouts.admin>
