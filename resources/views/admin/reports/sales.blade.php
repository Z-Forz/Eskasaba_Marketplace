<x-layouts.admin title="Laporan Penjualan">

    <div class="space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-chart-line text-emerald-600"></i>Laporan Penjualan Marketplace
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Ringkasan perputaran transaksi, total pendapatan, dan riwayat pesanan di Eskasaba Marketplace.
            </p>
        </div>

        {{-- Summary Cards --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-gray-700 dark:bg-gray-900">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Pendapatan Selesai</p>
                <p class="mt-2 text-3xl font-black text-emerald-700 dark:text-emerald-400">
                    Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-xs text-slate-500">Omzet transaksi pesanan yang telah selesai</p>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-gray-700 dark:bg-gray-900">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Transaksi Selesai</p>
                <p class="mt-2 text-3xl font-extrabold text-blue-600 dark:text-blue-400">
                    {{ number_format($completedOrdersCount ?? 0) }} Pesanan
                </p>
                <p class="mt-1 text-xs text-slate-500">Pesanan telah berhasil diambil & dibayar</p>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-gray-700 dark:bg-gray-900">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Semua Pesanan</p>
                <p class="mt-2 text-3xl font-extrabold text-slate-900 dark:text-white">
                    {{ number_format($totalOrders ?? 0) }} Pesanan
                </p>
                <p class="mt-1 text-xs text-slate-500">Termasuk pesanan pending, diproses, dan selesai</p>
            </div>
        </div>

        {{-- Sellers Sales Performance --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-gray-700 dark:bg-gray-900">
            <h2 class="text-base font-bold text-slate-900 dark:text-white">
                Performa Penjualan Seller
            </h2>

            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($sellers as $sel)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-800 text-sm font-bold text-white shadow-xs">
                                {{ strtoupper(substr($sel->user?->username ?? 'S', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold text-slate-900 dark:text-white">{{ $sel->user?->username ?? '-' }}</p>
                                <p class="text-xs text-slate-500">{{ $sel->products_count }} Produk • {{ $sel->orders_count }} Pesanan</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Recent Sales Table --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xs dark:border-gray-700 dark:bg-gray-900">
            <div class="p-6 border-b border-slate-100 dark:border-gray-800">
                <h2 class="text-base font-bold text-slate-900 dark:text-white">
                    Riwayat Penjualan Terbaru
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:border-gray-800 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-4">ID Pesanan</th>
                            <th class="px-6 py-4">Pembeli</th>
                            <th class="px-6 py-4">Seller</th>
                            <th class="px-6 py-4">Total Pembayaran</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-gray-800">
                        @forelse ($recentSales as $sale)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-gray-800/50">
                                <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                                    #{{ $sale->id }}
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-800 dark:text-gray-200">
                                    {{ $sale->buyer?->username ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-xs font-medium text-emerald-800 dark:text-emerald-400">
                                    {{ $sale->seller?->user?->username ?? '-' }}
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                                    Rp {{ number_format($sale->total_price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    <span class="rounded-full px-3 py-1 font-bold
                                        {{ $sale->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}"
                                    >
                                        {{ ucfirst($sale->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500">
                                    {{ $sale->created_at?->format('d M Y H:i') ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-slate-500">
                                    Belum ada transaksi penjualan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if (method_exists($recentSales, 'links'))
            <div>
                {{ $recentSales->links() }}
            </div>
        @endif

    </div>

</x-layouts.admin>
