<x-layouts.admin>
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Pesanan
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Kelola seluruh pesanan marketplace.
                </p>
            </div>
        </div>

        {{-- Filter --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <form method="GET" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Cari
                    </label>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Nomor pesanan..."
                        class="w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Status
                    </label>

                    <select
                        name="status"
                        class="w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >
                        <option value="">Semua Status</option>
                        <option value="pending" @selected(request('status') === 'pending')>
                            Pending
                        </option>
                        <option value="paid" @selected(request('status') === 'paid')>
                            Dibayar
                        </option>
                        <option value="processing" @selected(request('status') === 'processing')>
                            Diproses
                        </option>
                        <option value="ready" @selected(request('status') === 'ready')>
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
                        class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
                    >
                        Filter
                    </button>
                </div>

                <div class="flex items-end">
                    <a
                        href="{{ route('admin.orders.index') }}"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-center text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                    >
                        Reset
                    </a>
                </div>

            </form>
        </div>
        
        {{-- Desktop Table --}}
        <div class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm md:block dark:border-gray-700 dark:bg-gray-800">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">
                                Pesanan
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">
                                Pembeli
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">
                                Seller
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">
                                Total
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">
                                Status
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase text-gray-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($orders as $order)
                            <tr class="transition hover:bg-gray-50 dark:hover:bg-gray-700/30">

                                <td class="whitespace-nowrap px-6 py-4">
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        #{{ $order->id }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $order->created_at?->format('d M Y H:i') }}
                                    </p>
                                </td>

                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        {{ $order->user?->name ?? '-' }}
                                    </p>
                                </td>

                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        {{ $order->seller?->user?->name ?? '-' }}
                                    </p>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 font-semibold text-gray-900 dark:text-white">
                                    Rp {{ number_format($order->total ?? 0, 0, ',', '.') }}
                                </td>

                                <td class="px-6 py-4">
                                    <x-badge :status="$order->status" />
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <a
                                        href="{{ route('admin.orders.show', $order) }}"
                                        class="text-sm font-semibold text-indigo-600 hover:text-indigo-800"
                                    >
                                        Detail
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <x-empty-state
                                        title="Belum ada pesanan"
                                        description="Pesanan yang masuk akan tampil di sini."
                                    />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Mobile --}}
        <div class="space-y-4 md:hidden">
            @forelse ($orders as $order)
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">

                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-bold text-gray-900 dark:text-white">
                                #{{ $order->id }}
                            </p>

                            <p class="text-xs text-gray-500">
                                {{ $order->created_at?->format('d M Y H:i') }}
                            </p>
                        </div>

                        <x-badge :status="$order->status" />
                    </div>

                    <div class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between gap-4">
                            <span class="text-gray-500">Pembeli</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ $order->user?->name ?? '-' }}
                            </span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-gray-500">Seller</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ $order->seller?->user?->name ?? '-' }}
                            </span>
                        </div>

                        <div class="flex justify-between gap-4 border-t border-gray-100 pt-2 dark:border-gray-700">
                            <span class="text-gray-500">Total</span>
                            <span class="font-bold text-gray-900 dark:text-white">
                                Rp {{ number_format($order->total ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <a
                        href="{{ route('admin.orders.show', $order) }}"
                        class="mt-4 block rounded-xl bg-indigo-600 px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-indigo-700"
                    >
                        Lihat Detail
                    </a>

                </div>
            @empty
                <x-empty-state
                    title="Belum ada pesanan"
                    description="Pesanan yang masuk akan tampil di sini."
                />
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($orders->hasPages())
            <div>
                {{ $orders->withQueryString()->links() }}
            </div>
        @endif

    </div>
</x-layouts.admin>