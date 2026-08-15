<x-layouts.seller>
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Pesanan
                </h1>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Kelola pesanan dari produk yang kamu jual.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                    {{ $orders->total() }} Pesanan
                </span>
            </div>
        </div>

        {{-- Filter --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <form method="GET"
                  action="{{ route('seller.orders.index') }}"
                  class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">

                <div>
                    <label for="status"
                           class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Status
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">

                        <option value="">Semua Status</option>

                        <option value="pending"
                            @selected(request('status') === 'pending')}>
                            Menunggu
                        </option>

                        <option value="paid"
                            @selected(request('status') === 'paid')}>
                            Dibayar
                        </option>

                        <option value="processing"
                            @selected(request('status') === 'processing')}>
                            Diproses
                        </option>

                        <option value="ready"
                            @selected(request('status') === 'ready')}>
                            Siap Diambil
                        </option>

                        <option value="completed"
                            @selected(request('status') === 'completed')}>
                            Selesai
                        </option>

                        <option value="cancelled"
                            @selected(request('status') === 'cancelled')}>
                            Dibatalkan
                        </option>
                    </select>
                </div>

                <div>
                    <label for="search"
                           class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Cari
                    </label>

                    <input
                        id="search"
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Nomor pesanan..."
                        class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>

                <div class="flex items-end">
                    <button
                        type="submit"
                        class="w-full rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                        Filter
                    </button>
                </div>

                @if(request()->hasAny(['status', 'search']))
                    <div class="flex items-end">
                        <a
                            href="{{ route('seller.orders.index') }}"
                            class="w-full rounded-xl border border-gray-200 px-5 py-2.5 text-center text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                            Reset
                        </a>
                    </div>
                @endif

            </form>
        </div>

        {{-- Orders --}}
        @if($orders->count())

            <div class="space-y-4">

                @foreach($orders as $order)

                    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-gray-800 dark:bg-gray-900">

                        {{-- Order Header --}}
                        <div class="flex flex-col gap-3 border-b border-gray-100 pb-4 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">

                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                                    Pesanan
                                </p>

                                <h2 class="mt-1 font-bold text-gray-900 dark:text-white">
                                    #{{ $order->id }}
                                </h2>

                                <p class="mt-1 text-xs text-gray-500">
                                    {{ $order->created_at?->format('d M Y, H:i') }}
                                </p>
                            </div>

                            <x-badge
                                :type="$order->status"
                                :label="ucfirst(str_replace('_', ' ', $order->status))"
                            />

                        </div>

                        {{-- Buyer + Items --}}
                        <div class="grid gap-5 py-5 md:grid-cols-2">

                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                                    Pembeli
                                </p>

                                <p class="mt-2 font-semibold text-gray-900 dark:text-white">
                                    {{ $order->user?->name ?? '-' }}
                                </p>

                                @if($order->user?->email)
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $order->user->email }}
                                    </p>
                                @endif
                            </div>

                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                                    Ringkasan
                                </p>

                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $order->items->sum('quantity') }} item
                                </p>

                                <p class="mt-1 text-lg font-bold text-blue-600">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </p>
                            </div>

                        </div>

                        {{-- Items --}}
                        @if($order->items->count())

                            <div class="space-y-2 border-t border-gray-100 pt-4 dark:border-gray-800">

                                @foreach($order->items->take(3) as $item)

                                    <div class="flex items-center justify-between gap-3 text-sm">

                                        <div class="min-w-0">
                                            <p class="truncate font-medium text-gray-800 dark:text-gray-200">
                                                {{ $item->product_name }}
                                            </p>

                                            <p class="text-xs text-gray-500">
                                                {{ $item->quantity }} ×
                                                Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                            </p>
                                        </div>

                                        <p class="shrink-0 font-semibold text-gray-700 dark:text-gray-300">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </p>

                                    </div>

                                @endforeach

                                @if($order->items->count() > 3)
                                    <p class="pt-1 text-xs text-gray-400">
                                        + {{ $order->items->count() - 3 }} item lainnya
                                    </p>
                                @endif

                            </div>

                        @endif

                        {{-- Action --}}
                        <div class="mt-5 flex justify-end border-t border-gray-100 pt-4 dark:border-gray-800">

                            <a
                                href="{{ route('seller.orders.show', $order) }}"
                                class="rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                                Lihat Detail
                            </a>

                        </div>

                    </div>

                @endforeach

            </div>

            {{-- Pagination --}}
            <div>
                {{ $orders->withQueryString()->links() }}
            </div>

        @else

            <x-empty-state
                title="Belum ada pesanan"
                description="Pesanan yang masuk untuk produk kamu akan muncul di sini."
            />

        @endif

    </div>
</x-layouts.seller>