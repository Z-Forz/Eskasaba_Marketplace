<x-layouts.admin title="Detail Pesanan">
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a
                    href="{{ route('admin.orders.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
                >
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke List Pesanan
                </a>

                <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-emerald-600"></i> Detail Invoice: {{ $order->invoice_number ?? $order->id }}
                </h1>

                <p class="mt-1 text-xs text-slate-400 flex items-center gap-1">
                    <i class="fa-solid fa-clock text-[10px]"></i> {{ $order->created_at?->format('d M Y H:i') }}
                </p>
            </div>

            <span class="rounded-full px-4 py-1.5 text-xs font-bold
                {{ $order->status === 'completed'
                    ? 'bg-green-100 text-green-700 dark:bg-green-950/40 dark:text-green-400'
                    : ($order->status === 'cancelled'
                        ? 'bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-400'
                        : 'bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400') }}"
            >
                <i class="fa-solid fa-circle-info mr-1"></i> {{ ucfirst($order->status) }}
            </span>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- Order Items --}}
            <div class="space-y-6 lg:col-span-2">

                <div class="rounded-3xl border border-slate-200/80 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900">
                    <div class="border-b border-slate-100 px-6 py-4 dark:border-slate-800">
                        <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-boxes-stacked text-emerald-600"></i> Daftar Produk Pesanan
                        </h2>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($order->items ?? [] as $item)
                            <div class="flex gap-4 p-5">

                                <div class="h-20 w-20 shrink-0 overflow-hidden rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700">
                                    @if ($item->product?->images?->first())
                                        <img
                                            src="{{ asset('storage/' . $item->product->images->first()->image) }}"
                                            class="h-full w-full object-cover"
                                            alt="{{ $item->product_name ?? 'Produk' }}"
                                        >
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-slate-400">
                                            <i class="fa-solid fa-image text-xl"></i>
                                        </div>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <h3 class="font-bold text-slate-900 dark:text-white text-base">
                                        {{ $item->product_name ?? $item->product?->name ?? 'Produk' }}
                                    </h3>

                                    @if(!empty($item->note))
                                        <div class="mt-1">
                                            <span class="inline-flex items-center gap-1 rounded-xl bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                                <i class="fa-solid fa-tag text-[9px]"></i> Varian / Rasa: {{ $item->note }}
                                            </span>
                                        </div>
                                    @endif

                                    <p class="mt-2 text-xs font-semibold text-slate-500">
                                        {{ $item->quantity }} pcs × Rp {{ number_format($item->unit_price ?? $item->price ?? 0, 0, ',', '.') }}
                                    </p>
                                </div>

                                <div class="font-black text-slate-900 dark:text-white text-base">
                                    Rp {{ number_format(($item->quantity ?? 1) * ($item->unit_price ?? $item->price ?? 0), 0, ',', '.') }}
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Pickup Schedule --}}
                @if ($order->pickupSchedule)
                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                        <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-calendar-check text-emerald-600"></i> Jadwal Pengambilan Sekolah
                        </h2>

                        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800">
                                <p class="text-xs font-bold text-slate-400">Tanggal Pengambilan</p>
                                <p class="mt-1 font-bold text-slate-900 dark:text-white flex items-center gap-1.5 text-sm">
                                    <i class="fa-solid fa-calendar-day text-emerald-600"></i> {{ $order->pickupSchedule->pickup_date }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800">
                                <p class="text-xs font-bold text-slate-400">Jam / Waktu Pengambilan</p>
                                <p class="mt-1 font-bold text-slate-900 dark:text-white flex items-center gap-1.5 text-sm">
                                    <i class="fa-solid fa-clock text-emerald-600"></i> {{ $order->pickupSchedule->pickup_time }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            {{-- Summary Sidebar --}}
            <div class="space-y-6">

                <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                    <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-calculator text-emerald-600"></i> Ringkasan Transaksi
                    </h2>

                    <div class="mt-4 space-y-3 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-semibold">Total Nilai Pesanan</span>
                            <span class="font-black text-emerald-700 dark:text-emerald-400 text-base">
                                Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Buyer Card --}}
                <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                    <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-user-tag text-blue-600"></i> Pembeli (Buyer)
                    </h2>

                    <div class="mt-4 flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-slate-900 font-bold text-white shadow-xs">
                            {{ strtoupper(substr($order->buyer?->username ?? $order->user?->username ?? 'B', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 dark:text-white text-sm">
                                {{ $order->buyer?->username ?? $order->user?->username ?? '-' }}
                            </p>
                            <p class="text-xs text-slate-400 truncate">
                                {{ $order->buyer?->email ?? $order->user?->email ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Seller Card --}}
                <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                    <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-store text-emerald-600"></i> Penjual (Seller)
                    </h2>

                    <div class="mt-4 flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-800 font-bold text-white shadow-xs">
                            {{ strtoupper(substr($order->seller?->user?->username ?? 'S', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 dark:text-white text-sm">
                                {{ $order->seller?->user?->username ?? '-' }}
                            </p>
                            @if($order->seller?->whatsapp_number)
                                <a
                                    href="https://wa.me/{{ preg_replace('/\D/', '', $order->seller->whatsapp_number) }}"
                                    target="_blank"
                                    class="text-xs font-bold text-emerald-700 hover:underline dark:text-emerald-400 flex items-center gap-1 mt-0.5"
                                >
                                    <i class="fa-brands fa-whatsapp text-sm"></i> {{ $order->seller->whatsapp_number }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-layouts.admin>