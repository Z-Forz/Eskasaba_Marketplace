<x-layouts.buyer title="Detail Pesanan">

    <div class="mx-auto w-full max-w-5xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">

            <a
                href="{{ route('buyer.orders.index') }}"
                class="text-xs font-bold text-emerald-700 transition hover:text-emerald-800"
            >
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Riwayat Pesanan
            </a>

            <div class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Nomor Invoice Tagihan
                    </p>

                    <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-900 sm:text-3xl">
                        {{ $order->invoice_number ?? '#' . $order->id }}
                    </h1>
                    <p class="mt-0.5 text-xs text-slate-400">
                        <i class="fa-regular fa-calendar mr-1"></i> Dibuat pada: {{ $order->created_at?->format('d M Y, H:i') }}
                    </p>
                </div>

                <x-badge :type="$order->status">
                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                </x-badge>

            </div>

        </div>

        {{-- Order Progress Stepper Bar --}}
        @php
            $statusStep = match($order->status) {
                'pending'          => 1,
                'confirmed'        => 2,
                'processing'       => 3,
                'ready_for_pickup' => 4,
                'completed'        => 5,
                'cancelled'        => 0,
                default            => 1,
            };
        @endphp

        @if($order->status !== 'cancelled')
            <div class="mb-8 rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-4">
                    Status Progres Pesanan
                </p>

                <div class="relative flex items-center justify-between">
                    {{-- Progress Line --}}
                    <div class="absolute left-0 top-1/2 -z-0 h-1 w-full -translate-y-1/2 bg-slate-100"></div>
                    <div
                        class="absolute left-0 top-1/2 -z-0 h-1 -translate-y-1/2 bg-emerald-600 transition-all duration-500"
                        style="width: {{ (($statusStep - 1) / 4) * 100 }}%"
                    ></div>

                    {{-- Step 1 --}}
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-bold shadow-xs {{ $statusStep >= 1 ? 'bg-emerald-700 text-white ring-4 ring-emerald-100' : 'bg-slate-100 text-slate-400' }}">
                            1
                        </div>
                        <span class="mt-2 text-[11px] font-bold {{ $statusStep >= 1 ? 'text-emerald-700' : 'text-slate-400' }}">Dibuat</span>
                    </div>

                    {{-- Step 2 --}}
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-bold shadow-xs {{ $statusStep >= 2 ? 'bg-emerald-700 text-white ring-4 ring-emerald-100' : 'bg-slate-100 text-slate-400' }}">
                            2
                        </div>
                        <span class="mt-2 text-[11px] font-bold {{ $statusStep >= 2 ? 'text-emerald-700' : 'text-slate-400' }}">Dikonfirmasi</span>
                    </div>

                    {{-- Step 3 --}}
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-bold shadow-xs {{ $statusStep >= 3 ? 'bg-emerald-700 text-white ring-4 ring-emerald-100' : 'bg-slate-100 text-slate-400' }}">
                            3
                        </div>
                        <span class="mt-2 text-[11px] font-bold {{ $statusStep >= 3 ? 'text-emerald-700' : 'text-slate-400' }}">Diproses</span>
                    </div>

                    {{-- Step 4 --}}
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-bold shadow-xs {{ $statusStep >= 4 ? 'bg-emerald-700 text-white ring-4 ring-emerald-100' : 'bg-slate-100 text-slate-400' }}">
                            4
                        </div>
                        <span class="mt-2 text-[11px] font-bold {{ $statusStep >= 4 ? 'text-emerald-700' : 'text-slate-400' }}">Siap Diambil</span>
                    </div>

                    {{-- Step 5 --}}
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-bold shadow-xs {{ $statusStep >= 5 ? 'bg-emerald-700 text-white ring-4 ring-emerald-100' : 'bg-slate-100 text-slate-400' }}">
                            5
                        </div>
                        <span class="mt-2 text-[11px] font-bold {{ $statusStep >= 5 ? 'text-emerald-700' : 'text-slate-400' }}">Selesai</span>
                    </div>
                </div>
            </div>
        @else
            <div class="mb-8 rounded-3xl border border-red-200 bg-red-50 p-5 text-center">
                <p class="text-sm font-bold text-red-700"><i class="fa-solid fa-circle-xmark mr-1"></i> Pesanan Ini Telah Dibatalkan / Ditolak oleh Penjual</p>
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Detail Content --}}
            <div class="space-y-6 lg:col-span-2">

                {{-- Seller Info --}}
                <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs">

                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Penjual Toko
                    </p>

                    <div class="mt-3 flex items-center justify-between gap-4">

                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-900 text-lg font-bold text-white shadow-xs">
                                {{ strtoupper(substr($order->seller?->user?->username ?? 'S', 0, 1)) }}
                            </div>

                            <div>
                                <h2 class="font-bold text-slate-900 text-base">
                                    {{ $order->seller?->user?->username ?? 'Penjual' }}
                                </h2>
                                <p class="text-xs font-semibold text-emerald-700">
                                    <i class="fa-solid fa-circle-check mr-1"></i> Penjual Terverifikasi Sekolah
                                </p>
                            </div>
                        </div>

                        @if($order->seller?->whatsapp_number)
                            <a
                                href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->seller->whatsapp_number) }}?text=Halo%20{{ urlencode($order->seller->user?->username ?? 'Seller') }},%20saya%20tanya%20mengenai%20pesanan%20{{ $order->invoice_number }}"
                                target="_blank"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-800 transition hover:bg-emerald-100"
                            >
                                <i class="fa-brands fa-whatsapp text-sm"></i> Chat WA
                            </a>
                        @endif

                    </div>

                </div>

                {{-- Pickup Location Info --}}
                <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs">

                    <div class="flex items-start gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-800 text-base font-bold">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>

                        <div>

                            <h2 class="font-bold text-slate-900">
                                Lokasi Pengambilan (COD Sekolah)
                            </h2>

                            <p class="mt-1 text-sm font-extrabold text-emerald-700">
                                <i class="fa-solid fa-map-pin mr-1"></i> {{ $order->pickup_location ?? 'Belum ditentukan oleh penjual' }}
                            </p>

                            @if ($order->note)
                                <div class="mt-3 rounded-2xl bg-slate-50 p-3">
                                    <p class="text-[11px] font-semibold text-slate-400">Catatan Pesanan Anda:</p>
                                    <p class="mt-0.5 text-xs text-slate-700 italic">"{{ $order->note }}"</p>
                                </div>
                            @endif

                        </div>

                    </div>

                </div>

                {{-- QRIS Payment Card --}}
                @if (strtolower($order->payment?->method ?? '') === 'qris')
                    <div class="rounded-3xl border border-emerald-200 bg-emerald-50/60 p-6 shadow-xs">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-700 text-base font-bold text-white shadow-xs">
                                <i class="fa-solid fa-qrcode"></i>
                            </div>
                            <div>
                                <h2 class="font-bold text-slate-900">
                                    Pembayaran QRIS Toko
                                </h2>
                                <p class="text-xs text-slate-500">
                                    Scan Barcode QRIS di bawah ini melalui GoPay / OVO / Dana / m-Banking.
                                </p>
                            </div>
                        </div>

                        @if ($order->seller?->qris_image)
                            <div class="mt-4 flex flex-col items-center justify-center rounded-2xl bg-white p-5 border border-emerald-100 shadow-xs">
                                <img
                                    src="{{ Storage::url($order->seller->qris_image) }}"
                                    alt="QRIS Toko {{ $order->seller->user?->username }}"
                                    class="max-h-72 w-auto rounded-xl object-contain border border-slate-100"
                                >
                                <p class="mt-3 text-xs font-bold text-slate-800">
                                    Barcode QRIS Toko: {{ $order->seller->user?->username ?? 'Seller' }}
                                </p>
                                <p class="mt-0.5 text-xs text-emerald-700 font-extrabold">
                                    Total Tagihan: Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}
                                </p>
                            </div>
                        @else
                            <div class="mt-4 rounded-2xl bg-white p-4 text-center border border-amber-200">
                                <p class="text-xs font-bold text-amber-800"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Barcode QRIS Belum Diunggah oleh Seller</p>
                                <p class="mt-1 text-xs text-slate-500">Silakan hubungi penjual via WhatsApp untuk meminta barcode QRIS.</p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Items Table --}}
                <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-xs">

                    <div class="border-b border-slate-100 px-6 py-4">
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

                                    <h3 class="font-bold text-slate-900 text-sm sm:text-base">
                                        {{ $item->product_name ?? $item->product?->name }}
                                    </h3>

                                    @if($item->note)
                                        <p class="mt-1 inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                            <i class="fa-solid fa-tag text-[10px]"></i> Varian / Rasa: {{ $item->note }}
                                        </p>
                                    @endif

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $item->quantity }} × Rp {{ number_format($item->price ?? $item->unit_price ?? 0, 0, ',', '.') }}
                                    </p>

                                    <p class="mt-2 font-black text-slate-900 text-sm sm:text-base">
                                        Rp {{ number_format($item->quantity * ($item->price ?? $item->unit_price ?? 0), 0, ',', '.') }}
                                    </p>

                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>

            </div>

            {{-- Summary Sidebar --}}
            <div>

                <div class="sticky top-24 rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs space-y-4">

                    <h2 class="font-bold text-slate-900 border-b border-slate-100 pb-3">
                        Ringkasan Pembayaran
                    </h2>

                    <div class="space-y-3 text-xs sm:text-sm">

                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">
                                Total Barang
                            </span>
                            <span class="font-bold text-slate-900">
                                {{ $order->items->sum('quantity') }} pcs
                            </span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">
                                Metode Pembayaran
                            </span>
                            <span class="font-bold text-slate-900 uppercase">
                                {{ $order->payment?->method ?? 'COD' }}
                            </span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">
                                Status Pembayaran
                            </span>
                            <span class="font-bold text-emerald-700">
                                {{ ucfirst($order->payment?->status ?? 'Pending') }}
                            </span>
                        </div>

                    </div>

                    <div class="border-t border-slate-100 pt-3">
                        <div class="flex justify-between gap-4">
                            <span class="font-bold text-slate-600">
                                Total Bayar
                            </span>

                            <span class="text-xl font-black text-emerald-700">
                                Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    @if ($order->status === 'completed')

                        <a
                            href="{{ route('buyer.reviews.create', ['order' => $order->id]) }}"
                            class="mt-4 flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-5 py-3 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-800"
                        >
                            <i class="fa-solid fa-star text-amber-300"></i> Beri Ulasan Produk
                        </a>

                    @endif

                </div>

            </div>

        </div>

    </div>

</x-layouts.buyer>