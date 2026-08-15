<x-layouts.admin title="Detail Pesanan">
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a
                    href="{{ route('admin.orders.index') }}"
                    class="text-sm font-semibold text-slate-600 hover:text-slate-900 dark:text-gray-400 dark:hover:text-white"
                >
                    ← Kembali ke Pesanan
                </a>

                <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                    Pesanan #{{ $order->id }}
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    {{ $order->created_at?->format('d M Y H:i') }}
                </p>
            </div>

            <span class="rounded-full px-3.5 py-1 text-xs font-bold bg-slate-100 text-slate-800 dark:bg-gray-800 dark:text-gray-200">
                {{ ucfirst($order->status) }}
            </span>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- Order Items --}}
            <div class="space-y-6 lg:col-span-2">

                <div class="rounded-3xl border border-slate-200 bg-white shadow-xs dark:border-gray-700 dark:bg-gray-900">
                    <div class="border-b border-slate-100 px-6 py-4 dark:border-gray-800">
                        <h2 class="font-bold text-gray-900 dark:text-white">
                            Item Produk Pesanan
                        </h2>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-gray-800">
                        @foreach ($order->items ?? [] as $item)
                            <div class="flex gap-4 p-5">

                                <div class="h-20 w-20 shrink-0 overflow-hidden rounded-xl bg-slate-100 dark:bg-gray-800">
                                    @if ($item->product?->images?->first())
                                        <img
                                            src="{{ asset('storage/' . $item->product->images->first()->image) }}"
                                            class="h-full w-full object-cover"
                                            alt="{{ $item->product_name ?? 'Produk' }}"
                                        >
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">
                                        {{ $item->product_name ?? $item->product?->name ?? 'Produk' }}
                                    </h3>

                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ $item->quantity }} ×
                                        Rp {{ number_format($item->unit_price ?? $item->price ?? 0, 0, ',', '.') }}
                                    </p>
                                </div>

                                <div class="font-bold text-gray-900 dark:text-white">
                                    Rp {{ number_format(($item->quantity ?? 1) * ($item->unit_price ?? $item->price ?? 0), 0, ',', '.') }}
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Pickup --}}
                @if ($order->pickupSchedule)
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-gray-700 dark:bg-gray-900">
                        <h2 class="font-bold text-gray-900 dark:text-white">
                            Jadwal Pengambilan
                        </h2>

                        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <p class="text-xs text-slate-500">Tanggal Pengambilan</p>
                                <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                                    {{ $order->pickupSchedule->pickup_date }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-slate-500">Waktu / Jam</p>
                                <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                                    {{ $order->pickupSchedule->pickup_time }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            {{-- Summary --}}
            <div class="space-y-6">

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-gray-700 dark:bg-gray-900">
                    <h2 class="font-bold text-gray-900 dark:text-white">
                        Ringkasan Pembayaran
                    </h2>

                    <div class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Nominal Pesanan</span>
                            <span class="font-bold text-slate-900 dark:text-white">
                                Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Buyer --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-gray-700 dark:bg-gray-900">
                    <h2 class="font-bold text-gray-900 dark:text-white">
                        Pembeli (Buyer)
                    </h2>

                    <div class="mt-4">
                        <p class="font-bold text-gray-900 dark:text-white">
                            {{ $order->buyer?->username ?? $order->user?->username ?? '-' }}
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            {{ $order->buyer?->email ?? $order->user?->email ?? '-' }}
                        </p>
                    </div>
                </div>

                {{-- Seller --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-gray-700 dark:bg-gray-900">
                    <h2 class="font-bold text-gray-900 dark:text-white">
                        Penjual (Seller)
                    </h2>

                    <div class="mt-4">
                        <p class="font-bold text-gray-900 dark:text-white">
                            {{ $order->seller?->user?->username ?? '-' }}
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-layouts.admin>