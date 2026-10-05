<x-layouts.seller title="Laporan Penjualan Toko">

    <div class="space-y-6">

        {{-- Header & Filter --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-chart-line text-emerald-600"></i>Laporan Penjualan Toko
                </h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Ringkasan perputaran transaksi, grafik perbandingan omzet bulanan, produk terlaris, dan riwayat pesanan toko Anda.
                </p>
            </div>

            {{-- Filter Form --}}
            <form method="GET" action="{{ route('seller.reports.sales') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 bg-white p-2 rounded-2xl border border-slate-200 dark:bg-slate-900 dark:border-slate-800 shadow-xs">
                <div class="flex items-center gap-1.5 px-2 text-xs font-bold text-slate-500">
                    <i class="fa-solid fa-filter text-emerald-600"></i>
                    <span>Filter:</span>
                </div>

                <div class="w-36">
                    <x-custom-select
                        name="month"
                        :options="collect(range(1, 12))->mapWithKeys(fn($m) => [(string)$m => \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F')])->all()"
                        :selected="(string)$selectedMonth"
                        placeholder=""
                    />
                </div>

                <div class="w-28">
                    <x-custom-select
                        name="year"
                        :options="collect(range(date('Y') - 2, date('Y')))->mapWithKeys(fn($y) => [(string)$y => (string)$y])->all()"
                        :selected="(string)$selectedYear"
                        placeholder=""
                    />
                </div>

                <button type="submit" class="rounded-2xl bg-emerald-700 px-4 py-2.5 text-xs font-bold text-white hover:bg-emerald-800 transition cursor-pointer">
                    Terapkan
                </button>
            </form>
        </div>

        {{-- Monthly Summary Cards --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

            {{-- Omzet Bulan Ini --}}
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
                <p class="mt-1 text-xs text-slate-500">Dari transaksi valid &amp; selesai di bulan ini</p>
            </div>

            {{-- Transaksi Selesai Bulan Ini --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Transaksi Selesai</p>
                    <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                        {{ $monthlyTotalOrders > 0 ? round(($monthlyCompletedOrders / $monthlyTotalOrders) * 100) : 0 }}% Sukses
                    </span>
                </div>
                <p class="mt-2 text-3xl font-extrabold text-emerald-700 dark:text-emerald-400">
                    {{ number_format($monthlyCompletedOrders ?? 0) }} Pesanan
                </p>
                <p class="mt-1 text-xs text-slate-500">Dari total {{ number_format($monthlyTotalOrders ?? 0) }} pesanan bulan ini</p>
            </div>

            {{-- Total Omzet (All Time) --}}
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

            {{-- Produk Terjual --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Produk Terjual</p>
                    <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                        Bulan Ini
                    </span>
                </div>
                <p class="mt-2 text-3xl font-extrabold text-slate-900 dark:text-white">
                    {{ number_format($monthlyItemsSold ?? 0) }} <span class="text-sm font-bold text-slate-400">Pcs</span>
                </p>
                <p class="mt-1 text-xs text-slate-500">Total akumulasi: {{ number_format($totalItemsSoldAllTime ?? 0) }} Pcs terjual</p>
            </div>

        </div>

        {{-- Bar Chart: Perbandingan Sales Beberapa Bulan Sebelumnya --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-chart-column text-emerald-600"></i> Grafik Batang Penjualan Toko (6 Bulan Terakhir)
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Perbandingan pendapatan (Rp) dan jumlah transaksi selesai per bulan untuk toko Anda.
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

        {{-- Top Selling Products Report --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 dark:border-slate-800">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-fire text-amber-500"></i> Produk Terlaris Toko ({{ \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->translatedFormat('F Y') }})
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Top 5 produk jualanmu dengan jumlah item terjual terbanyak pada periode ini.
                    </p>
                </div>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($topProducts as $index => $top)
                    @php
                        $productObj = $top->product;
                        $firstImage = $productObj?->images?->first()?->image_path ?? null;
                    @endphp
                    <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-800/60">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-black {{ $index === 0 ? 'bg-amber-400 text-slate-950' : 'bg-emerald-800 text-white' }}">
                                {{ $index + 1 }}
                            </span>

                            @if($firstImage)
                                <img src="{{ asset('storage/' . $firstImage) }}" alt="{{ $top->product_name }}" class="h-10 w-10 shrink-0 rounded-xl object-cover border border-slate-200 dark:border-slate-700">
                            @else
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-200 text-slate-400 dark:bg-slate-700">
                                    <i class="fa-solid fa-box text-base"></i>
                                </div>
                            @endif

                            <div class="min-w-0">
                                <p class="truncate text-xs font-bold text-slate-900 dark:text-white">
                                    {{ $top->product_name ?? $productObj?->name ?? 'Produk' }}
                                </p>
                                <p class="text-[11px] text-slate-500">
                                    Terjual: <strong class="text-emerald-700 dark:text-emerald-400 font-extrabold">{{ number_format($top->total_sold ?? 0) }} Pcs</strong>
                                </p>
                            </div>
                        </div>

                        <p class="text-xs font-black text-slate-900 dark:text-white shrink-0 ml-2">
                            Rp {{ number_format((float) ($top->total_revenue ?? 0), 0, ',', '.') }}
                        </p>
                    </div>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-slate-200 p-8 text-center dark:border-slate-800">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-500 dark:bg-amber-950/40">
                            <i class="fa-solid fa-fire text-xl"></i>
                        </div>
                        <p class="mt-3 text-sm font-bold text-slate-700 dark:text-slate-300">Belum Ada Transaksi Produk</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Belum ada penjualan produk pada bulan yang dipilih.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Sales Table --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-emerald-600"></i> Riwayat Penjualan Toko ({{ \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->translatedFormat('F Y') }})
                </h2>
                <span class="text-xs text-slate-500 font-semibold">Total: {{ $recentSales->total() }} Pesanan</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:bg-slate-800/80">
                        <tr>
                            <th class="px-6 py-4">No. Invoice / ID</th>
                            <th class="px-6 py-4">Pembeli</th>
                            <th class="px-6 py-4">Detail Item</th>
                            <th class="px-6 py-4">Total Pembayaran</th>
                            <th class="px-6 py-4">Status Pesanan</th>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($recentSales as $sale)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/50">
                                <td class="px-6 py-4 font-bold text-slate-900 dark:text-white whitespace-nowrap">
                                    {{ $sale->invoice_number ?? '#' . $sale->id }}
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-800 dark:text-slate-200">
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                            {{ strtoupper(substr($sale->user?->username ?? 'B', 0, 1)) }}
                                        </div>
                                        <span>{{ $sale->user?->username ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    @if($sale->items->count())
                                        <div class="flex flex-wrap gap-1 max-w-xs">
                                            @foreach($sale->items as $item)
                                                <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                    <span>{{ $item->quantity }}x {{ $item->product_name ?? $item->product?->name }}</span>
                                                    @if(!empty($item->note))
                                                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">({{ $item->note }})</span>
                                                    @endif
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-900 dark:text-white whitespace-nowrap">
                                    Rp {{ number_format($sale->total_price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-xs whitespace-nowrap">
                                    <x-badge :type="$sale->status">
                                        {{ ucfirst(str_replace('_', ' ', $sale->status)) }}
                                    </x-badge>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500 whitespace-nowrap">
                                    {{ $sale->created_at?->format('d M Y H:i') ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <a href="{{ route('seller.orders.show', $sale) }}" class="inline-flex items-center gap-1 rounded-xl bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-emerald-950/60 dark:hover:text-emerald-300 transition">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                        <span>Detail</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-slate-500">
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
                                label: 'Pendapatan Toko (Rp)',
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

</x-layouts.seller>
