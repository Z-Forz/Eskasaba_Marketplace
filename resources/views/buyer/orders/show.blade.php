<x-layouts.buyer title="Detail Pesanan">

    <div class="mx-auto w-full max-w-5xl px-4 py-8 sm:px-6 lg:px-8">

        <div class="mb-8">

            <a
                href="{{ route('buyer.orders.index') }}"
                class="text-sm font-medium text-slate-500 transition hover:text-slate-900"
            >
                ← Kembali ke pesanan
            </a>

            <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

                <div>
                    <p class="text-sm text-slate-500">
                        Nomor Pesanan
                    </p>

                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">
                        #{{ $order->order_number ?? $order->id }}
                    </h1>
                </div>

                <x-badge :type="$order->status">
                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                </x-badge>

            </div>

        </div>

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Detail Order --}}
            <div class="space-y-6 lg:col-span-2">

                {{-- Seller --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Penjual
                    </p>

                    <div class="mt-3 flex items-center gap-4">

                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 font-bold text-slate-600">
                            {{ strtoupper(substr($order->seller->user->name ?? 'S', 0, 1)) }}
                        </div>

                        <div>
                            <h2 class="font-bold text-slate-900">
                                {{ $order->seller->user->name ?? 'Seller' }}
                            </h2>

                            @if ($order->seller->user->username ?? null)
                                <p class="text-sm text-slate-500">
                                    @{{ $order->seller->user->username }}
                                </p>
                            @endif
                        </div>

                    </div>

                </div>

                {{-- Items --}}
                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                    <div class="border-b border-slate-100 px-6 py-5">
                        <h2 class="font-bold text-slate-900">
                            Produk Pesanan
                        </h2>
                    </div>

                    <div class="divide-y divide-slate-100">

                        @foreach ($order->items as $item)

                            <div class="flex gap-4 p-5 sm:p-6">

                                <div class="h-20 w-20 shrink-0 overflow-hidden rounded-2xl bg-slate-100 sm:h-24 sm:w-24">

                                    @if ($item->product?->images?->first())
                                        <img
                                            src="{{ Storage::url($item->product->images->first()->image) }}"
                                            alt="{{ $item->product_name }}"
                                            class="h-full w-full object-cover"
                                        >
                                    @endif

                                </div>

                                <div class="min-w-0 flex-1">

                                    <h3 class="font-semibold text-slate-900">
                                        {{ $item->product_name }}
                                    </h3>

                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ $item->quantity }}
                                        ×
                                        Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                    </p>

                                    <p class="mt-2 font-bold text-slate-900">
                                        Rp {{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}
                                    </p>

                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>

                {{-- Pickup --}}
                @if ($order->pickupSchedule)

                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

                        <div class="flex gap-4">

                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-slate-100">
                                📍
                            </div>

                            <div class="min-w-0">

                                <h2 class="font-bold text-slate-900">
                                    Jadwal Pengambilan
                                </h2>

                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $order->pickupSchedule->pickup_date
                                        ? \Carbon\Carbon::parse($order->pickupSchedule->pickup_date)->translatedFormat('d F Y')
                                        : 'Belum ditentukan'
                                    }}
                                </p>

                                @if ($order->pickupSchedule->pickup_time)
                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ $order->pickupSchedule->pickup_time }}
                                    </p>
                                @endif

                                @if ($order->pickupSchedule->location)
                                    <p class="mt-2 text-sm font-medium text-slate-700">
                                        {{ $order->pickupSchedule->location }}
                                    </p>
                                @endif

                            </div>

                        </div>

                    </div>

                @endif

            </div>

            {{-- Summary --}}
            <div>

                <div class="sticky top-24 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

                    <h2 class="font-bold text-slate-900">
                        Ringkasan
                    </h2>

                    <div class="mt-6 space-y-4 text-sm">

                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">
                                Total produk
                            </span>

                            <span class="font-medium text-slate-900">
                                {{ $order->items->sum('quantity') }}
                            </span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">
                                Subtotal
                            </span>

                            <span class="font-medium text-slate-900">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </span>
                        </div>

                    </div>

                    <div class="my-6 border-t border-slate-100"></div>

                    <div class="flex justify-between gap-4">
                        <span class="font-medium text-slate-500">
                            Total
                        </span>

                        <span class="text-lg font-bold text-slate-900">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </span>
                    </div>

                    @if ($order->status === 'completed')

                        <a
                            href="{{ route('buyer.reviews.create', ['order' => $order->id]) }}"
                            class="mt-6 flex w-full items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
                        >
                            Beri Ulasan
                        </a>

                    @endif

                </div>

            </div>

        </div>

    </div>

</x-layouts.buyer>