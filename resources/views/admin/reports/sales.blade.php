<x-layouts.admin title="Laporan Penjualan">

    <div class="space-y-6">

        {{-- Header & Filter --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-chart-line text-emerald-600"></i>Laporan Penjualan Marketplace
                </h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Ringkasan perputaran transaksi, grafik perbandingan omzet bulanan, dan riwayat pesanan di Eskasaba Marketplace.
                </p>
            </div>

            {{-- Filter Form --}}
            <form method="GET" action="{{ route('admin.reports.sales') }}" class="flex items-center gap-2 bg-white p-2 rounded-2xl border border-slate-200 dark:bg-slate-900 dark:border-slate-800 shadow-xs">
                <div class="flex items-center gap-1.5 px-2 text-xs font-bold text-slate-500">
                    <i class="fa-solid fa-filter text-emerald-600"></i>
                    <span>Filter:</span>
                </div>
                <select name="month" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-800 focus:border-emerald-500 focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800 dark:text-white dark:focus:border-emerald-500">
                    @foreach (range(1, 12) as $m)
                        <option value="{{ $m }}" {{ (int)$selectedMonth === $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>

                <select name="year" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-800 focus:border-emerald-500 focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800 dark:text-white dark:focus:border-emerald-500">
                    @foreach (range(date('Y') - 2, date('Y')) as $y)
                        <option value="{{ $y }}" {{ (int)$selectedYear === $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="rounded-xl bg-emerald-700 px-4 py-1.5 text-xs font-bold text-white hover:bg-emerald-800 transition cursor-pointer">
                    Terapkan
                </button>
            </form>
        </div>

        {{-- Monthly Summary Cards --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Omzet Bulan Ini</p>
                    <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                        {{ \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->translatedFormat('M Y') }}
                    </span>
                </div>
                <p class="mt-2 text-3xl font-black text-emerald-700 dark:text-emerald-400">
                    Rp {{ number_format($monthlyRevenue ?? 0, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-xs text-slate-500">Total transaksi selesai di bulan ini</p>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Transaksi Selesai Bulan Ini</p>
                    <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                        {{ $monthlyTotalOrders > 0 ? round(($monthlyCompletedOrders / $monthlyTotalOrders) * 100) : 0 }}% Sukses
                    </span>
                </div>
                <p class="mt-2 text-3xl font-extrabold text-emerald-700 dark:text-emerald-400">
                    {{ number_format($monthlyCompletedOrders ?? 0) }} Pesanan
                </p>
                <p class="mt-1 text-xs text-slate-500">Dari total {{ number_format($monthlyTotalOrders ?? 0) }} pesanan bulan ini</p>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Omzet (All Time)</p>
                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[10px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                        Semua Periode
                    </span>
                </div>
                <p class="mt-2 text-3xl font-extrabold text-slate-900 dark:text-white">
                    Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-xs text-slate-500">Akumulasi {{ number_format($completedOrdersCount ?? 0) }} pesanan selesai</p>
            </div>
        </div>

        {{-- Bar Chart: Perbandingan Sales Beberapa Bulan Sebelumnya --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-chart-column text-emerald-600"></i> Grafik Batang Penjualan (6 Bulan Terakhir)
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Perbandingan pendapatan (Rp) dan jumlah transaksi selesai per bulan.
                    </p>
                </div>
            </div>

            <div class="relative w-full h-72">
                <canvas id="salesBarChart"
                    data-labels='@json($chartLabels)'
                    data-revenues='@json($chartRevenues)'
                    data-orders='@json($chartOrderCounts)'></canvas>
            </div>
        </div>

        {{-- Sellers Sales Performance --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-base font-bold text-slate-900 dark:text-white">
                Performa Penjualan Seller
            </h2>

            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($sellers as $sel)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-800/60">
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
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-slate-200 p-8 text-center dark:border-slate-800">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500">
                            <i class="fa-solid fa-store text-xl"></i>
                        </div>
                        <p class="mt-3 text-sm font-bold text-slate-700 dark:text-slate-300">Belum Ada Data Seller</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Belum ada seller aktif yang terdaftar untuk menampilkan performa penjualan.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Sales Table --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-900 dark:text-white">
                    Riwayat Penjualan ({{ \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->translatedFormat('F Y') }})
                </h2>
                <span class="text-xs text-slate-500">Total: {{ $recentSales->total() }} Pesanan</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:bg-slate-800/80">
                        <tr>
                            <th class="px-6 py-4">ID Pesanan</th>
                            <th class="px-6 py-4">Pembeli</th>
                            <th class="px-6 py-4">Seller</th>
                            <th class="px-6 py-4">Total Pembayaran</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($recentSales as $sale)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/50">
                                <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                                    {{ $sale->id }}
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-800 dark:text-slate-200">
                                    {{ $sale->buyer?->username ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-xs font-medium text-emerald-800 dark:text-emerald-400">
                                    {{ $sale->seller?->user?->username ?? '-' }}
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                                    Rp {{ number_format($sale->total_price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-xs whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full px-3 py-1 font-bold whitespace-nowrap
                                        {{ $sale->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-950/40 dark:text-green-400' : 'bg-blue-100 text-blue-800 dark:bg-blue-950/40 dark:text-blue-400' }}"
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
                                    Belum ada transaksi penjualan pada bulan ini.
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

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const canvas = document.getElementById('salesBarChart');
                if (!canvas) return;

                const ctx = canvas.getContext('2d');
                const labels = JSON.parse(canvas.dataset.labels || '[]');
                const revenues = JSON.parse(canvas.dataset.revenues || '[]');
                const orders = JSON.parse(canvas.dataset.orders || '[]');

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Pendapatan (Rp)',
                                data: revenues,
                                backgroundColor: 'rgba(16, 185, 129, 0.85)',
                                borderColor: 'rgba(16, 185, 129, 1)',
                                borderWidth: 1,
                                borderRadius: 8,
                                yAxisID: 'y'
                            },
                            {
                                label: 'Jumlah Transaksi Selesai',
                                data: orders,
                                backgroundColor: 'rgba(59, 130, 246, 0.75)',
                                borderColor: 'rgba(59, 130, 246, 1)',
                                borderWidth: 1,
                                borderRadius: 8,
                                yAxisID: 'y1'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Pendapatan (Rp)',
                                    font: { size: 11, weight: 'bold' }
                                },
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    }
                                },
                                grid: {
                                    color: 'rgba(226, 232, 240, 0.5)'
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'Jumlah Transaksi',
                                    font: { size: 11, weight: 'bold' }
                                },
                                grid: {
                                    drawOnChartArea: false
                                },
                                ticks: {
                                    precision: 0
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    font: { family: 'Plus Jakarta Sans', weight: 'bold', size: 12 }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) label += ': ';
                                        if (context.datasetIndex === 0) {
                                            label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                        } else {
                                            label += context.parsed.y + ' Transaksi';
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush

</x-layouts.admin>
