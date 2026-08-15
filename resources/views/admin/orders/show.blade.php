<x-layouts.admin>
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a
                    href="{{ route('admin.orders.index') }}"
                    class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
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

            <x-badge :status="$order->status" />
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- Order Items --}}
            <div class="space-y-6 lg:col-span-2">

                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                        <h2 class="font-bold text-gray-900 dark:text-white">
                            Produk
                        </h2>
                    </div>

                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($order->items as $item)
                            <div class="flex gap-4 p-5">

                                <div class="h-20 w-20 shrink-0 overflow-hidden rounded-xl bg-gray-100">
                                    @if ($item->product?->images?->first())
                                        <img
                                            src="{{ asset('storage/' . $item->product->images->first()->image) }}"
                                            class="h-full w-full object-cover"
                                            alt="{{ $item->product_name }}"
                                        >
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">
                                        {{ $item->product_name }}
                                    </h3>

                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $item->quantity }} ×
                                        Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                    </p>
                                </div>

                                <div class="font-bold text-gray-900 dark:text-white">
                                    Rp {{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Pickup --}}
                @if ($order->pickupSchedule)
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <h2 class="font-bold text-gray-900 dark:text-white">
                            Jadwal Pengambilan
                        </h2>

                        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <p class="text-xs text-gray-500">Tanggal</p>
                                <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                                    {{ $order->pickupSchedule->pickup_date }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500">Waktu</p>
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

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="font-bold text-gray-900 dark:text-white">
                        Ringkasan
                    </h2>

                    <div class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-medium">
                                Rp {{ number_format($order->subtotal ?? 0, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="border-t border-gray-200 pt-3 dark:border-gray-700">
                            <div class="flex justify-between">
                                <span class="font-semibold">Total</span>
                                <span class="text-lg font-bold">
                                    Rp {{ number_format($order->total ?? 0, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Buyer --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="font-bold text-gray-900 dark:text-white">
                        Pembeli
                    </h2>

                    <div class="mt-4">
                        <p class="font-semibold text-gray-900 dark:text-white">
                            {{ $order->user?->name ?? '-' }}
                        </p>

                        <p class="mt-1 text-sm text-gray-500">
                            {{ $order->user?->email ?? '-' }}
                        </p>
                    </div>
                </div>

                {{-- Seller --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="font-bold text-gray-900 dark:text-white">
                        Seller
                    </h2>

                    <div class="mt-4">
                        <p class="font-semibold text-gray-900 dark:text-white">
                            {{ $order->seller?->user?->name ?? '-' }}
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-layouts.admin>