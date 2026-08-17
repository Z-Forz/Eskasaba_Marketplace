<x-layouts.admin title="Kelola Pesanan">
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-emerald-600"></i> Pemantauan Pesanan Marketplace
                </h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Pantau seluruh transaksi, invoice, item varian, dan status pesanan antara pembeli dan seller.
                </p>
            </div>
        </div>

        {{-- Search & Filter Bar --}}
        <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">

                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <i class="fa-solid fa-magnifying-glass mr-1 text-slate-400"></i> Cari Invoice / ID
                    </label>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Contoh: INV/2026/... atau ID..."
                        class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    >
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <i class="fa-solid fa-filter mr-1 text-slate-400"></i> Status Pesanan
                    </label>

                    <select
                        name="status"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    >
                        <option value="">Semua Status</option>
                        <option value="pending" @selected(request('status') === 'pending')>⏳ Menunggu Konfirmasi (Pending)</option>
                        <option value="confirmed" @selected(request('status') === 'confirmed')>✅ Dikonfirmasi</option>
                        <option value="processing" @selected(request('status') === 'processing')>🍳 Diproses Seller</option>
                        <option value="ready_for_pickup" @selected(request('status') === 'ready_for_pickup')>📦 Siap Diambil</option>
                        <option value="completed" @selected(request('status') === 'completed')>🎉 Selesai</option>
                        <option value="cancelled" @selected(request('status') === 'cancelled')>❌ Dibatalkan</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button
                        type="submit"
                        class="flex-1 rounded-2xl bg-emerald-700 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-800 flex items-center justify-center gap-1.5 shadow-xs"
                    >
                        <i class="fa-solid fa-magnifying-glass"></i> Filter
                    </button>
                    
                    <a
                        href="{{ route('admin.orders.index') }}"
                        class="rounded-2xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 flex items-center justify-center gap-1"
                    >
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </a>
                </div>

            </form>
        </div>
        
        {{-- Table Container --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50/80 text-xs font-bold uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:bg-slate-800/80 dark:text-slate-400">
                        <tr>
                            <th class="px-6 py-4">Invoice / ID</th>
                            <th class="px-6 py-4">Pembeli (Buyer)</th>
                            <th class="px-6 py-4">Penjual (Seller)</th>
                            <th class="px-6 py-4">Total Transaksi</th>
                            <th class="px-6 py-4">Status Pesanan</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($orders as $order)
                            <tr class="transition hover:bg-slate-50/60 dark:hover:bg-slate-800/50">

                                <td class="whitespace-nowrap px-6 py-4">
                                    <p class="font-bold text-slate-900 dark:text-white flex items-center gap-1">
                                        <i class="fa-solid fa-receipt text-emerald-600 text-xs"></i> #{{ $order->invoice_number ?? $order->id }}
                                    </p>
                                    <p class="text-xs text-slate-400 flex items-center gap-1 mt-0.5">
                                        <i class="fa-solid fa-clock text-[10px]"></i> {{ $order->created_at?->format('d M Y H:i') }}
                                    </p>
                                </td>

                                <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-user-tag text-blue-600 text-xs"></i>
                                        <span>{{ $order->buyer?->username ?? $order->user?->username ?? '-' }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-bold text-emerald-800 dark:text-emerald-400">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-store text-emerald-600 text-xs"></i>
                                        <span>{{ $order->seller?->user?->username ?? '-' }}</span>
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 font-black text-emerald-700 dark:text-emerald-400 text-base">
                                    Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-bold
                                        {{ $order->status === 'completed'
                                            ? 'bg-green-100 text-green-700 dark:bg-green-950/40 dark:text-green-400'
                                            : ($order->status === 'cancelled'
                                                ? 'bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-400'
                                                : ($order->status === 'ready_for_pickup'
                                                    ? 'bg-blue-100 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400'
                                                    : 'bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400')) }}"
                                    >
                                        @if($order->status === 'completed')
                                            <i class="fa-solid fa-circle-check mr-1"></i> Selesai
                                        @elseif($order->status === 'cancelled')
                                            <i class="fa-solid fa-circle-xmark mr-1"></i> Dibatalkan
                                        @elseif($order->status === 'ready_for_pickup')
                                            <i class="fa-solid fa-box-open mr-1"></i> Siap Diambil
                                        @elseif($order->status === 'processing')
                                            <i class="fa-solid fa-fire-burner mr-1"></i> Diproses
                                        @else
                                            <i class="fa-solid fa-clock mr-1"></i> {{ ucfirst($order->status) }}
                                        @endif
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <a
                                        href="{{ route('admin.orders.show', $order) }}"
                                        class="inline-flex items-center gap-1.5 rounded-2xl bg-slate-900 px-4 py-2 text-xs font-bold text-white transition hover:bg-slate-800 dark:bg-emerald-700 dark:hover:bg-emerald-800"
                                    >
                                        <i class="fa-solid fa-eye"></i> Detail Pesanan
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">
                                    Belum ada data pesanan ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile List --}}
            <div class="divide-y divide-slate-100 md:hidden dark:divide-slate-800">
                @forelse ($orders as $order)
                    <div class="space-y-3 p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-bold text-slate-900 dark:text-white">
                                    #{{ $order->invoice_number ?? $order->id }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{ $order->created_at?->format('d M Y H:i') }}
                                </p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-bold bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-200">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>

                        <div class="space-y-1 text-xs text-slate-600 dark:text-slate-300">
                            <p><span class="text-slate-400">Pembeli:</span> {{ $order->buyer?->username ?? $order->user?->username ?? '-' }}</p>
                            <p><span class="text-slate-400">Seller:</span> {{ $order->seller?->user?->username ?? '-' }}</p>
                            <p class="font-black text-emerald-700 dark:text-emerald-400 text-sm">Total: Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}</p>
                        </div>

                        <a
                            href="{{ route('admin.orders.show', $order) }}"
                            class="flex items-center justify-center gap-2 rounded-2xl bg-slate-900 py-2.5 text-xs font-bold text-white dark:bg-emerald-700"
                        >
                            <i class="fa-solid fa-eye"></i> Detail Pesanan
                        </a>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-slate-500">
                        Belum ada pesanan ditemukan.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Pagination --}}
        @if ($orders->hasPages())
            <div>
                {{ $orders->withQueryString()->links() }}
            </div>
        @endif

    </div>
</x-layouts.admin>