<x-layouts.seller title="Dashboard Seller">

    <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8 space-y-8">

        {{-- Header --}}
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">
                <i class="fa-solid fa-chart-line mr-1"></i> Seller Dashboard & Laporan Penjualan
            </p>

            <div class="mt-1 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white sm:text-3xl flex items-center gap-2">
                        Halo, {{ auth()->user()->username }} <i class="fa-solid fa-store text-emerald-600 text-2xl"></i>
                    </h1>

                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Pantau laporan omset penjualan, produk terlaris, pesanan masuk, dan kelola tokomu.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2.5">
                    <a
                        href="{{ route('seller.products.create') }}"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-5 py-3 text-sm font-bold text-white shadow-xs transition hover:bg-emerald-800 sm:w-auto"
                    >
                        <i class="fa-solid fa-plus"></i> Tambah Produk Baru
                    </a>
                </div>
            </div>
        </div>


        {{-- =========================================================
            SALES REPORT SUMMARY CARDS (LAPORAN PENJUALAN SELLER)
        ========================================================== --}}
        <div>
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-1.5">
                <i class="fa-solid fa-coins text-emerald-600"></i> Ringkasan Laporan Pendapatan Toko
            </h2>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                {{-- Total Omset Penjualan --}}
                <div class="rounded-3xl border border-emerald-200/80 bg-emerald-50/60 p-5 shadow-xs dark:border-emerald-950 dark:bg-emerald-950/30">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-400">
                            Total Omset Penjualan
                        </p>
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-700 text-white shadow-xs">
                            <i class="fa-solid fa-sack-dollar text-base"></i>
                        </div>
                    </div>

                    <p class="mt-3 text-2xl font-black text-emerald-900 dark:text-emerald-300">
                        Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}
                    </p>

                    <p class="mt-1 text-[11px] font-semibold text-emerald-700 dark:text-emerald-400">
                        <i class="fa-solid fa-circle-check mr-1"></i> Dari transaksi valid & selesai
                    </p>
                </div>

                {{-- Omset Bulan Ini --}}
                <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Penjualan Bulan Ini
                        </p>
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-300">
                            <i class="fa-solid fa-calendar-days text-base"></i>
                        </div>
                    </div>

                    <p class="mt-3 text-2xl font-black text-slate-900 dark:text-white">
                        Rp {{ number_format($revenueThisMonth ?? 0, 0, ',', '.') }}
                    </p>

                    <p class="mt-1 text-[11px] font-semibold text-slate-400">
                        Bulan {{ date('F Y') }}
                    </p>
                </div>

                {{-- Omset Hari Ini --}}
                <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Penjualan Hari Ini
                        </p>
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-300">
                            <i class="fa-solid fa-bolt text-base"></i>
                        </div>
                    </div>

                    <p class="mt-3 text-2xl font-black text-slate-900 dark:text-white">
                        Rp {{ number_format($revenueToday ?? 0, 0, ',', '.') }}
                    </p>

                    <p class="mt-1 text-[11px] font-semibold text-slate-400">
                        Tanggal {{ date('d M Y') }}
                    </p>
                </div>

                {{-- Total Produk Terjual --}}
                <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Produk Terjual
                        </p>
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-purple-50 text-purple-600 dark:bg-purple-950/40 dark:text-purple-300">
                            <i class="fa-solid fa-cart-flatbed text-base"></i>
                        </div>
                    </div>

                    <p class="mt-3 text-2xl font-black text-slate-900 dark:text-white">
                        {{ number_format($totalItemsSold ?? 0) }} <span class="text-xs font-bold text-slate-400">Pcs</span>
                    </p>

                    <p class="mt-1 text-[11px] font-semibold text-slate-400">
                        Akumulasi item laku
                    </p>
                </div>

            </div>
        </div>


        {{-- Operations Quick Stats --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

            <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200">
                    <i class="fa-solid fa-boxes-stacked text-base"></i>
                </div>
                <p class="mt-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Total Katalolog Produk
                </p>
                <p class="mt-1 text-xl font-black text-slate-900 dark:text-white">
                    {{ $totalProducts ?? 0 }} Produk
                </p>
            </div>

            <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-950/40">
                    <i class="fa-solid fa-clock-rotate-left text-base"></i>
                </div>
                <p class="mt-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Pesanan Pending
                </p>
                <p class="mt-1 text-xl font-black text-amber-600 dark:text-amber-400">
                    {{ $pendingOrders ?? 0 }} Pesanan
                </p>
            </div>

            <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-950/40">
                    <i class="fa-solid fa-receipt text-base"></i>
                </div>
                <p class="mt-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Total Pesanan Masuk
                </p>
                <p class="mt-1 text-xl font-black text-slate-900 dark:text-white">
                    {{ $totalOrders ?? 0 }} Pesanan
                </p>
            </div>

            <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40">
                    <i class="fa-solid fa-circle-check text-base"></i>
                </div>
                <p class="mt-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Pesanan Selesai
                </p>
                <p class="mt-1 text-xl font-black text-emerald-700 dark:text-emerald-400">
                    {{ $completedOrders ?? 0 }} Selesai
                </p>
            </div>

        </div>


        {{-- =========================================================
            MAIN SECTION: TOP SELLING PRODUCTS & RECENT SALES REPORT
        ========================================================== --}}
        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Top Selling Products Report --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900 sm:p-6 lg:col-span-1">
                <div class="border-b border-slate-100 pb-4 dark:border-slate-800">
                    <h2 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2">
                        <i class="fa-solid fa-fire text-amber-500"></i> Laporan Produk Terlaris
                    </h2>
                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                        Top 5 produk jualanmu dengan jumlah terbeli tertinggi.
                    </p>
                </div>

                <div class="mt-4 divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($topProducts ?? [] as $index => $top)
                        <div class="flex items-center justify-between py-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-black {{ $index === 0 ? 'bg-amber-400 text-slate-950' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">
                                    {{ $index + 1 }}
                                </span>

                                <div class="min-w-0">
                                    <p class="truncate text-xs font-bold text-slate-900 dark:text-white">
                                        {{ $top->product_name ?? $top->product?->name ?? 'Produk' }}
                                    </p>
                                    <p class="text-[11px] text-slate-400 font-medium">
                                        Terjual: <strong class="text-emerald-700 dark:text-emerald-400 font-extrabold">{{ $top->total_sold }} Pcs</strong>
                                    </p>
                                </div>
                            </div>

                            <p class="text-xs font-black text-slate-900 dark:text-white shrink-0 ml-2">
                                Rp {{ number_format($top->total_revenue ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-slate-400">
                            Belum ada data penjualan produk terlaris.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Recent Sales Transactions Report --}}
            <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900 lg:col-span-2">

                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5 dark:border-slate-800 sm:px-6">

                    <div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2">
                            <i class="fa-solid fa-list-check text-emerald-600"></i> Laporan Transaksi Pesanan Masuk
                        </h2>

                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                            Daftar pesanan terbaru pembeli beserta varian rasa & total pembayaran.
                        </p>
                    </div>

                    <a
                        href="{{ route('seller.orders.index') }}"
                        class="text-xs font-bold text-emerald-700 hover:text-emerald-800 dark:text-emerald-400 inline-flex items-center gap-1"
                    >
                        Lihat semua <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

                @if (isset($recentOrders) && $recentOrders->count())

                    <div class="divide-y divide-slate-100 dark:divide-slate-800">

                        @foreach ($recentOrders as $order)

                            <a
                                href="{{ route('seller.orders.show', $order) }}"
                                class="block p-5 transition hover:bg-slate-50 dark:hover:bg-slate-800/60 sm:p-6"
                            >

                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                                    <div>
                                        <p class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5 text-sm">
                                            <i class="fa-solid fa-receipt text-emerald-600 text-xs"></i> {{ $order->invoice_number ?? '#' . $order->id }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                            <i class="fa-solid fa-user mr-1 text-slate-400"></i> Pembeli: <strong>{{ $order->user?->username ?? 'Buyer' }}</strong>
                                            • <i class="fa-solid fa-clock mr-1 text-slate-400"></i> {{ $order->created_at?->format('d M Y, H:i') }}
                                        </p>

                                        {{-- Order Items Preview with Flavor Note --}}
                                        @if($order->items->count())
                                            <div class="mt-2 flex flex-wrap gap-1.5">
                                                @foreach($order->items as $item)
                                                    <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-0.5 text-[11px] font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                        <span>{{ $item->quantity }}x {{ $item->product_name ?? $item->product?->name }}</span>
                                                        @if(!empty($item->note))
                                                            <span class="text-emerald-700 dark:text-emerald-400 font-bold">({{ $item->note }})</span>
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex items-center justify-between gap-4 sm:flex-col sm:items-end sm:justify-center">

                                        <span class="text-base font-black text-emerald-700 dark:text-emerald-400">
                                            Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}
                                        </span>

                                        <x-badge :type="$order->status">
                                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                        </x-badge>

                                    </div>

                                </div>

                            </a>

                        @endforeach

                    </div>

                @else

                    <div class="px-5 py-10 sm:px-6 text-center">
                        <x-empty-state
                            title="Belum ada transaksi"
                            description="Transaksi penjualan toko akan muncul di sini."
                        />
                    </div>

                @endif

            </div>

        </div>

    </div>

</x-layouts.seller>