<x-layouts.admin title="Kelola Pesanan">
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                    🛒 Pemantauan Pesanan Marketplace
                </h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Pantau seluruh riwayat dan status pesanan antara pembeli dan seller.
                </p>
            </div>
        </div>

        {{-- Filter --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-xs dark:border-gray-700 dark:bg-gray-900">
            <form method="GET" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">

                <div>
                    <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-gray-400">
                        Cari Nomor Pesanan
                    </label>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Nomor ID pesanan..."
                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-slate-400 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                    >
                </div>

                <div>
                    <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-gray-400">
                        Status Pesanan
                    </label>

                    <select
                        name="status"
                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-slate-400 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                    >
                        <option value="">Semua Status</option>
                        <option value="pending" @selected(request('status') === 'pending')>
                            Pending
                        </option>
                        <option value="confirmed" @selected(request('status') === 'confirmed')>
                            Dikonfirmasi
                        </option>
                        <option value="processing" @selected(request('status') === 'processing')>
                            Diproses
                        </option>
                        <option value="ready_for_pickup" @selected(request('status') === 'ready_for_pickup')>
                            Siap Diambil
                        </option>
                        <option value="completed" @selected(request('status') === 'completed')>
                            Selesai
                        </option>
                        <option value="cancelled" @selected(request('status') === 'cancelled')>
                            Dibatalkan
                        </option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button
                        type="submit"
                        class="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-900"
                    >
                        Filter
                    </button>
                </div>

                <div class="flex items-end">
                    <a
                        href="{{ route('admin.orders.index') }}"
                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-center text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                    >
                        Reset
                    </a>
                </div>

            </form>
        </div>
        
        {{-- Desktop Table --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xs dark:border-gray-700 dark:bg-gray-900">
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:border-gray-800 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-4">ID Pesanan</th>
                            <th class="px-6 py-4">Pembeli (Buyer)</th>
                            <th class="px-6 py-4">Penjual (Seller)</th>
                            <th class="px-6 py-4">Total Harga</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 dark:divide-gray-800">
                        @forelse ($orders as $order)
                            <tr class="transition hover:bg-slate-50/60 dark:hover:bg-gray-800/50">

                                <td class="whitespace-nowrap px-6 py-4">
                                    <p class="font-bold text-slate-900 dark:text-white">
                                        #{{ $order->id }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        {{ $order->created_at?->format('d M Y H:i') }}
                                    </p>
                                </td>

                                <td class="px-6 py-4 font-semibold text-slate-800 dark:text-gray-200">
                                    {{ $order->buyer?->username ?? $order->user?->username ?? '-' }}
                                </td>

                                <td class="px-6 py-4 font-medium text-emerald-800 dark:text-emerald-400">
                                    {{ $order->seller?->user?->username ?? '-' }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 font-bold text-slate-900 dark:text-white">
                                    Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-bold bg-slate-100 text-slate-800 dark:bg-gray-800 dark:text-gray-200">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <a
                                        href="{{ route('admin.orders.show', $order) }}"
                                        class="rounded-xl border border-slate-200 px-3.5 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-gray-700 dark:text-gray-300"
                                    >
                                        Detail →
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    Belum ada pesanan ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile List --}}
            <div class="divide-y divide-slate-100 md:hidden dark:divide-gray-800">
                @forelse ($orders as $order)
                    <div class="space-y-3 p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-bold text-slate-900 dark:text-white">
                                    #{{ $order->id }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{ $order->created_at?->format('d M Y H:i') }}
                                </p>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-xs font-bold bg-slate-100 text-slate-800">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>

                        <div class="space-y-1 text-xs text-slate-600 dark:text-gray-300">
                            <p><span class="text-slate-400">Pembeli:</span> {{ $order->buyer?->username ?? $order->user?->username ?? '-' }}</p>
                            <p><span class="text-slate-400">Seller:</span> {{ $order->seller?->user?->username ?? '-' }}</p>
                            <p class="font-bold text-slate-900 dark:text-white">Total: Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}</p>
                        </div>

                        <a
                            href="{{ route('admin.orders.show', $order) }}"
                            class="block w-full rounded-xl border border-slate-200 py-2 text-center text-xs font-semibold text-slate-700 dark:border-gray-700 dark:text-gray-300"
                        >
                            Detail →
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