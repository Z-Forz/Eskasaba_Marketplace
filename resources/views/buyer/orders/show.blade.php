<x-layouts.buyer title="Detail Pesanan">

    <div class="mx-auto w-full max-w-5xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">

            <a
                href="{{ route('buyer.orders.index') }}"
                class="text-xs font-bold text-emerald-700 transition hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-300"
            >
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Riwayat Pesanan
            </a>

            <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">

                <div>
                    @php
                        $firstItem = $order->items?->first();
                        $firstProductName = $firstItem?->product?->name ?? 'Produk Pesanan';
                        $itemCount = $order->items?->count() ?? 1;

                        $orderTitle = $itemCount > 1
                            ? $firstProductName . ' + ' . ($itemCount - 1) . ' produk lainnya'
                            : $firstProductName;
                    @endphp
                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                        <span class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-2.5 py-0.5 text-xs font-extrabold text-emerald-800 border border-emerald-200/60 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-900/60">
                            <i class="fa-solid fa-receipt text-[10px]"></i> {{ $order->invoice_number ?? '#' . $order->id }}
                        </span>
                        @if($order->created_at)
                            <span class="text-xs text-slate-400">
                                • {{ $order->created_at->format('d M Y, H:i') }}
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl font-black tracking-tight text-slate-900 sm:text-3xl dark:text-white">
                        {{ $orderTitle }}
                    </h1>
                </div>

                <div class="self-start sm:self-auto">
                    <x-badge :type="$order->status">
                        {{ match($order->status) {
                            'cancel_requested' => 'Pengajuan Pembatalan',
                            'cancelled'        => match($order->cancelled_by) {
                                'buyer'  => 'Dibatalkan Pembeli (Anda)',
                                'seller' => 'Dibatalkan Penjual Toko',
                                'admin'  => 'Dibatalkan Admin Sekolah',
                                default  => 'Dibatalkan',
                            },
                            default => ucfirst(str_replace('_', ' ', $order->status))
                        } }}
                    </x-badge>
                </div>

            </div>

        </div>

        {{-- Order Progress Stepper Bar & Cancellation Banners --}}
        @php
            $statusStep = match($order->status) {
                'pending'          => 1,
                'confirmed'        => 2,
                'processing'       => 3,
                'ready_for_pickup' => 4,
                'completed'        => 5,
                'cancelled', 'cancel_requested' => 0,
                default            => 1,
            };

            $progressWidthClass = match($statusStep) {
                1 => 'w-0',
                2 => 'w-1/4',
                3 => 'w-1/2',
                4 => 'w-3/4',
                5 => 'w-full',
                default => 'w-0',
            };

            // Format WhatsApp pre-filled text with order details
            $sellerName = $order->seller?->user?->username ?? 'Penjual';
            $buyerName  = auth()->user()->username ?? 'Pembeli';

            $itemsSummary = "";
            foreach($order->items as $idx => $item) {
                $opt = $item->variant_name ?: $item->note;
                $optText = !empty($opt) ? " [Pilihan: {$opt}]" : "";
                $price  = number_format($item->price ?? 0, 0, ',', '.');
                $itemsSummary .= ($idx + 1) . ". {$item->product_name}{$optText} - {$item->quantity}x @ Rp {$price}\n";
            }

            $buyerPhoneNum = $order->user?->phone ?: '-';
            $waText = "Halo Kak {$sellerName}, saya {$buyerName} (No. HP: {$buyerPhoneNum}) dari Eskasaba Marketplace.\n\n"
                . "📦 *DETAIL PESANAN SAYA*\n"
                . "• Invoice: {$order->invoice_number}\n"
                . "• Waktu Pesanan: " . ($order->created_at?->format('d M Y, H:i') ?? '-') . "\n"
                . "• Status: " . ucfirst(str_replace('_', ' ', $order->status)) . "\n\n"
                . "🛍️ *DAFTAR ITEM PRODUK:*\n" . $itemsSummary . "\n"
                . "💰 *TOTAL BAYAR:* Rp " . number_format($order->total_price ?? 0, 0, ',', '.') . "\n"
                . "💳 *METODE PEMBAYARAN:* " . strtoupper($order->payment?->method ?? 'COD') . "\n"
                . "📍 *TITIK PENGAMBILAN:* " . ($order->pickup_location ?? 'COD Sekolah') . "\n\n"
                . "Mohon bantuan untuk diproses ya kak. Terima kasih!";

            $waPhone = preg_replace('/[^0-9]/', '', $order->seller?->whatsapp_number ?? '');
            if (!empty($waPhone) && str_starts_with($waPhone, '0')) {
                $waPhone = '62' . substr($waPhone, 1);
            }
            $waUrl   = !empty($waPhone) ? "https://wa.me/{$waPhone}?text=" . urlencode($waText) : null;
        @endphp

        @if($order->status === 'cancel_requested')
            <div class="mb-8 rounded-3xl border border-amber-200 bg-amber-50/90 p-6 shadow-xs dark:border-amber-900/60 dark:bg-amber-950/40">
                <div class="flex items-center gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-800 text-xl font-bold dark:bg-amber-900/60 dark:text-amber-300">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-amber-900 dark:text-amber-300">Pengajuan Pembatalan Menunggu Konfirmasi Penjual</h2>
                        <p class="mt-0.5 text-xs text-amber-700 dark:text-amber-400">
                            Alasan Pengajuan Anda: <em>"{{ $order->cancellation_reason }}"</em>. Penjual sedang meninjau dan akan mengunggah foto bukti pengembalian dana (refund).
                        </p>
                    </div>
                </div>
            </div>
        @elseif($order->status === 'cancelled')
            <div class="mb-8 rounded-3xl border border-red-200 bg-red-50/90 p-6 shadow-xs dark:border-red-900/60 dark:bg-red-950/40">
                <div class="flex items-start gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-red-100 text-red-700 text-xl font-bold dark:bg-red-900/60 dark:text-red-300">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-base font-bold text-red-900 dark:text-red-300">
                            Pesanan Ini Telah Dibatalkan
                        </h2>
                        <p class="mt-1 text-xs font-semibold text-red-700 dark:text-red-400">
                            Dibatalkan Oleh: <strong>{{ match($order->cancelled_by) { 'buyer' => 'Pembeli (Anda)', 'seller' => 'Penjual Toko', 'admin' => 'Admin Sekolah', default => 'Sistem' } }}</strong>
                        </p>
                        @if($order->cancellation_reason)
                            <div class="mt-2.5 rounded-2xl bg-white/80 p-3.5 border border-red-100 dark:border-red-900/40 dark:bg-slate-900/60">
                                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wide">Alasan Pembatalan:</p>
                                <p class="mt-0.5 text-xs text-slate-800 dark:text-slate-200 font-medium italic">"{{ $order->cancellation_reason }}"</p>
                            </div>
                        @endif

                        {{-- Bukti Pengembalian Dana / Refund Proof Card --}}
                        @if($order->payment?->refund_proof)
                            <div class="mt-4 rounded-2xl border border-emerald-200 bg-white p-4 shadow-2xs dark:border-slate-800 dark:bg-slate-900" x-data="{ showRefundModal: false }">
                                <p class="text-xs font-bold text-emerald-800 dark:text-emerald-300 flex items-center gap-1.5 mb-3">
                                    <i class="fa-solid fa-file-circle-check text-emerald-600 text-sm"></i> Bukti Pengembalian Dana (Refund QRIS dari Penjual)
                                </p>
                                <div class="flex flex-col sm:flex-row items-center gap-4">
                                    <img
                                        src="{{ Storage::url($order->payment->refund_proof) }}"
                                        alt="Bukti Pengembalian Dana"
                                        class="h-32 w-32 rounded-xl border border-slate-200 object-cover shadow-xs cursor-pointer hover:opacity-95 hover:scale-105 transition dark:border-slate-700"
                                        @click="showRefundModal = true"
                                    >
                                    <div class="flex-1 text-center sm:text-left">
                                        <p class="text-xs font-bold text-slate-900 dark:text-white">
                                            Status Pengembalian Dana: <span class="text-emerald-700 dark:text-emerald-400 font-black">✓ Lunas Dikembalikan</span>
                                        </p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                            Penjual telah mentransfer kembali dana sebesar <strong>Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}</strong>.
                                        </p>
                                        @if($order->payment->refund_notes)
                                            <p class="mt-1 text-xs text-slate-600 dark:text-slate-300 italic">"{{ $order->payment->refund_notes }}"</p>
                                        @endif
                                        <div class="mt-3">
                                            <button
                                                type="button"
                                                @click="showRefundModal = true"
                                                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-700 px-3.5 py-1.5 text-xs font-bold text-white shadow-xs hover:bg-emerald-800 transition cursor-pointer"
                                            >
                                                <i class="fa-solid fa-magnifying-glass-plus"></i> Lihat Bukti Refund Penuh
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Centered Lightbox Modal --}}
                                <div
                                    x-show="showRefundModal"
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    @click="showRefundModal = false"
                                    @keydown.escape.window="showRefundModal = false"
                                    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md cursor-pointer select-none"
                                >
                                    <div class="relative max-w-lg w-full flex flex-col items-center justify-center p-2" @click.stop>
                                        <div class="relative w-full flex justify-center">
                                            <img
                                                src="{{ Storage::url($order->payment->refund_proof) }}"
                                                alt="Bukti Pengembalian Dana"
                                                class="max-h-[80vh] max-w-full rounded-3xl bg-white p-3 shadow-2xl object-contain border-4 border-emerald-500/30 dark:bg-slate-900"
                                            >
                                            <button
                                                type="button"
                                                @click="showRefundModal = false"
                                                class="absolute -top-3 -right-3 flex h-9 w-9 items-center justify-center rounded-full bg-slate-900 text-white shadow-lg border border-white/20 hover:bg-red-600 transition cursor-pointer"
                                            >
                                                <i class="fa-solid fa-xmark text-sm"></i>
                                            </button>
                                        </div>
                                        <p class="mt-4 text-center text-xs font-bold text-white/90 bg-slate-900/90 px-4 py-2 rounded-full border border-white/10 backdrop-blur-xs flex items-center gap-1.5 shadow-lg">
                                            <i class="fa-solid fa-xmark text-emerald-400"></i> Klik di mana saja untuk menutup
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="mb-8 rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-4">
                    Status Progres Pesanan
                </p>

                <div class="relative flex items-center justify-between">
                    {{-- Progress Line --}}
                    <div class="absolute left-0 top-1/2 -z-0 h-1 w-full -translate-y-1/2 bg-slate-100 dark:bg-slate-800"></div>
                    <div
                        class="absolute left-0 top-1/2 -z-0 h-1 -translate-y-1/2 bg-emerald-600 transition-all duration-500 {{ $progressWidthClass }}"
                    ></div>

                    {{-- Step 1 --}}
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-bold shadow-xs {{ $statusStep >= 1 ? 'bg-emerald-700 text-white ring-4 ring-emerald-100 dark:ring-emerald-950' : 'bg-slate-100 text-slate-400 dark:bg-slate-800' }}">
                            1
                        </div>
                        <span class="mt-2 text-[11px] font-bold {{ $statusStep >= 1 ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-400' }}">Dibuat</span>
                    </div>

                    {{-- Step 2 --}}
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-bold shadow-xs {{ $statusStep >= 2 ? 'bg-emerald-700 text-white ring-4 ring-emerald-100 dark:ring-emerald-950' : 'bg-slate-100 text-slate-400 dark:bg-slate-800' }}">
                            2
                        </div>
                        <span class="mt-2 text-[11px] font-bold {{ $statusStep >= 2 ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-400' }}">Dikonfirmasi</span>
                    </div>

                    {{-- Step 3 --}}
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-bold shadow-xs {{ $statusStep >= 3 ? 'bg-emerald-700 text-white ring-4 ring-emerald-100 dark:ring-emerald-950' : 'bg-slate-100 text-slate-400 dark:bg-slate-800' }}">
                            3
                        </div>
                        <span class="mt-2 text-[11px] font-bold {{ $statusStep >= 3 ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-400' }}">Diproses</span>
                    </div>

                    {{-- Step 4 --}}
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-bold shadow-xs {{ $statusStep >= 4 ? 'bg-emerald-700 text-white ring-4 ring-emerald-100 dark:ring-emerald-950' : 'bg-slate-100 text-slate-400 dark:bg-slate-800' }}">
                            4
                        </div>
                        <span class="mt-2 text-[11px] font-bold {{ $statusStep >= 4 ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-400' }}">Siap Diambil</span>
                    </div>

                    {{-- Step 5 --}}
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-bold shadow-xs {{ $statusStep >= 5 ? 'bg-emerald-700 text-white ring-4 ring-emerald-100 dark:ring-emerald-950' : 'bg-slate-100 text-slate-400 dark:bg-slate-800' }}">
                            5
                        </div>
                        <span class="mt-2 text-[11px] font-bold {{ $statusStep >= 5 ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-400' }}">Selesai</span>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Detail Content --}}
            <div class="space-y-6 lg:col-span-2">

                {{-- Seller Info Card with Pre-Formatted WA Redirect --}}
                <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">

                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Penjual Toko
                    </p>

                    <div class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                        <div class="flex items-center gap-3 min-w-0">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-slate-900 text-lg font-bold text-white shadow-xs dark:bg-slate-800">
                                {{ strtoupper(substr($order->seller?->user?->username ?? 'S', 0, 1)) }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <h2 class="font-bold text-slate-900 text-base dark:text-white truncate">
                                    {{ $order->seller?->user?->username ?? 'Penjual' }}
                                </h2>
                                <p class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">
                                    <i class="fa-solid fa-circle-check mr-1"></i> Penjual Terverifikasi Sekolah
                                </p>
                            </div>
                        </div>

                        @if($waUrl)
                            <a
                                href="{{ $waUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-700 cursor-pointer w-full sm:w-auto"
                            >
                                <i class="fa-brands fa-whatsapp text-base"></i>
                                <span>Chat WA Seller</span>
                            </a>
                        @else
                            <button
                                disabled
                                class="inline-flex items-center justify-center gap-1.5 rounded-2xl bg-slate-100 dark:bg-slate-800 px-3.5 py-2 text-xs font-bold text-slate-400 cursor-not-allowed w-full sm:w-auto"
                            >
                                <i class="fa-brands fa-whatsapp text-sm"></i> WA Tidak Tersedia
                            </button>
                        @endif

                    </div>

                </div>

                {{-- Pickup Location Info --}}
                <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">

                    <div class="flex items-start gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-800 text-base font-bold dark:bg-emerald-950/60 dark:text-emerald-300">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>

                        <div>

                            <h2 class="font-bold text-slate-900 dark:text-white">
                                Lokasi Pengambilan (COD Sekolah)
                            </h2>

                            <p class="mt-1 text-sm font-extrabold text-emerald-700 dark:text-emerald-400">
                                <i class="fa-solid fa-map-pin mr-1"></i> {{ $order->pickup_location ?? 'Belum ditentukan oleh penjual' }}
                            </p>

                            @if ($order->note)
                                <div class="mt-3 rounded-2xl bg-slate-50 p-3 dark:bg-slate-800/60">
                                    <p class="text-[11px] font-semibold text-slate-400">Catatan Pesanan Anda:</p>
                                    <p class="mt-0.5 text-xs text-slate-700 dark:text-slate-300 italic">"{{ $order->note }}"</p>
                                </div>
                            @endif

                        </div>

                    </div>

                </div>

                {{-- QRIS Payment Card --}}
                @if (strtolower($order->payment?->method ?? '') === 'qris')
                    <div class="rounded-3xl border border-emerald-200 bg-emerald-50/60 p-6 shadow-xs dark:border-emerald-900/60 dark:bg-emerald-950/30">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-700 text-base font-bold text-white shadow-xs">
                                <i class="fa-solid fa-qrcode"></i>
                            </div>
                            <div>
                                <h2 class="font-bold text-slate-900 dark:text-white">
                                    Pembayaran QRIS Toko
                                </h2>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    Scan Barcode QRIS di bawah ini melalui GoPay / OVO / Dana / m-Banking.
                                </p>
                            </div>
                        </div>

                        @if ($order->seller?->qris_image)
                            <div class="mt-4 flex flex-col items-center justify-center rounded-2xl bg-white p-5 border border-emerald-100 shadow-xs dark:border-slate-800 dark:bg-slate-900" x-data="{ showQrisModal: false }">
                                <img
                                    src="{{ Storage::url($order->seller->qris_image) }}"
                                    alt="QRIS Toko {{ $order->seller->user?->username }}"
                                    class="max-h-72 w-auto rounded-xl object-contain border border-slate-100 dark:border-slate-800 cursor-pointer hover:opacity-90 hover:scale-105 transition"
                                    @click="showQrisModal = true"
                                >
                                <button
                                    type="button"
                                    @click="showQrisModal = true"
                                    class="mt-3 inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 cursor-pointer"
                                >
                                    <i class="fa-solid fa-magnifying-glass-plus"></i> Perbesar QRIS Toko
                                </button>
                                <p class="mt-2 text-xs font-bold text-slate-800 dark:text-slate-200">
                                    Barcode QRIS Toko: {{ $order->seller->user?->username ?? 'Seller' }}
                                </p>
                                <p class="mt-0.5 text-xs text-emerald-700 dark:text-emerald-400 font-extrabold">
                                    Total Tagihan: Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}
                                </p>

                                {{-- Centered Lightbox Modal --}}
                                <div
                                    x-show="showQrisModal"
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    @click="showQrisModal = false"
                                    @keydown.escape.window="showQrisModal = false"
                                    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md cursor-pointer select-none"
                                >
                                    <div class="relative max-w-lg w-full flex flex-col items-center justify-center p-2" @click.stop>
                                        <div class="relative w-full flex justify-center">
                                            <img
                                                src="{{ Storage::url($order->seller->qris_image) }}"
                                                alt="QRIS Toko {{ $order->seller->user?->username }}"
                                                class="max-h-[80vh] max-w-full rounded-3xl bg-white p-4 shadow-2xl object-contain border-4 border-emerald-500/30 dark:bg-slate-900"
                                            >
                                            <button
                                                type="button"
                                                @click="showQrisModal = false"
                                                class="absolute -top-3 -right-3 flex h-9 w-9 items-center justify-center rounded-full bg-slate-900 text-white shadow-lg border border-white/20 hover:bg-red-600 transition cursor-pointer"
                                            >
                                                <i class="fa-solid fa-xmark text-sm"></i>
                                            </button>
                                        </div>
                                        <p class="mt-4 text-center text-xs font-bold text-white/90 bg-slate-900/90 px-4 py-2 rounded-full border border-white/10 backdrop-blur-xs flex items-center gap-1.5 shadow-lg">
                                            <i class="fa-solid fa-xmark text-emerald-400"></i> Klik di mana saja untuk menutup
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mt-4 rounded-2xl bg-white p-4 text-center border border-amber-200 dark:border-amber-900/60 dark:bg-slate-900">
                                <p class="text-xs font-bold text-amber-800 dark:text-amber-400"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Barcode QRIS Belum Diunggah oleh Seller</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Silakan hubungi penjual via WhatsApp untuk meminta barcode QRIS.</p>
                            </div>
                        @endif

                        {{-- Upload / View Proof of Payment --}}
                        <div class="mt-5 border-t border-emerald-200/80 pt-4 dark:border-emerald-900/60">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                <i class="fa-solid fa-receipt text-emerald-600"></i> Bukti Pembayaran QRIS / Transfer
                            </h3>

                            @if($order->payment?->proof)
                                <div class="mt-3 rounded-2xl bg-white p-4 border border-slate-200 flex flex-col sm:flex-row items-center gap-4 dark:border-slate-800 dark:bg-slate-900" x-data="{ showProofModal: false }">
                                    <img
                                        src="{{ Storage::url($order->payment->proof) }}"
                                        alt="Bukti Pembayaran"
                                        class="h-24 w-24 rounded-xl border border-slate-200 object-cover shadow-xs cursor-pointer hover:opacity-90 hover:scale-105 transition dark:border-slate-700"
                                        @click="showProofModal = true"
                                    >
                                    <div class="flex-1 text-center sm:text-left">
                                        <div class="flex items-center justify-center sm:justify-start gap-2">
                                            <span class="text-xs font-bold text-slate-900 dark:text-white">Status Pembayaran:</span>
                                            <span class="rounded-full px-2.5 py-0.5 text-[11px] font-extrabold {{ match($order->payment->status) {
                                                'verified', 'paid' => 'bg-emerald-100 text-emerald-800 border border-emerald-300 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-900',
                                                'rejected'         => 'bg-red-100 text-red-800 border border-red-300 dark:bg-red-950/60 dark:text-red-300 dark:border-red-900',
                                                default            => 'bg-amber-100 text-amber-800 border border-amber-300 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-900'
                                            } }}">
                                                {{ match($order->payment->status) {
                                                    'verified', 'paid' => '✓ Terverifikasi Lunas',
                                                    'rejected'         => '✕ Bukti Ditolak Penjual',
                                                    default            => '⏳ Menunggu Verifikasi Penjual'
                                                } }}
                                            </span>
                                        </div>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Bukti pembayaran telah berhasil dikirim ke penjual.</p>
                                        <button
                                            type="button"
                                            @click="showProofModal = true"
                                            class="mt-2 inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 hover:underline dark:text-emerald-400 cursor-pointer"
                                        >
                                            <i class="fa-solid fa-magnifying-glass-plus"></i> Lihat Bukti Pembayaran Penuh
                                        </button>
                                    </div>

                                    {{-- Centered Lightbox Modal --}}
                                    <div
                                        x-show="showProofModal"
                                        x-cloak
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95"
                                        @click="showProofModal = false"
                                        @keydown.escape.window="showProofModal = false"
                                        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md cursor-pointer select-none"
                                    >
                                        <div class="relative max-w-lg w-full flex flex-col items-center justify-center p-2" @click.stop>
                                            <div class="relative w-full flex justify-center">
                                                <img
                                                    src="{{ Storage::url($order->payment->proof) }}"
                                                    alt="Bukti Pembayaran"
                                                    class="max-h-[80vh] max-w-full rounded-3xl bg-white p-3 shadow-2xl object-contain border-4 border-emerald-500/30 dark:bg-slate-900"
                                                >
                                                <button
                                                    type="button"
                                                    @click="showProofModal = false"
                                                    class="absolute -top-3 -right-3 flex h-9 w-9 items-center justify-center rounded-full bg-slate-900 text-white shadow-lg border border-white/20 hover:bg-red-600 transition cursor-pointer"
                                                >
                                                    <i class="fa-solid fa-xmark text-sm"></i>
                                                </button>
                                            </div>
                                            <p class="mt-4 text-center text-xs font-bold text-white/90 bg-slate-900/90 px-4 py-2 rounded-full border border-white/10 backdrop-blur-xs flex items-center gap-1.5 shadow-lg">
                                                <i class="fa-solid fa-xmark text-emerald-400"></i> Klik di mana saja untuk menutup
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Upload Form --}}
                            @if($order->status !== 'completed' && $order->status !== 'cancelled')
                                <form action="{{ route('buyer.orders.upload-proof', $order) }}" method="POST" enctype="multipart/form-data" class="mt-3">
                                    @csrf
                                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                        {{ $order->payment?->proof ? 'Ganti / Unggah Ulang Bukti Transfer:' : 'Unggah Struk / Screenshot Bukti Bayar:' }}
                                    </label>
                                    <div class="flex flex-col sm:flex-row items-center gap-2">
                                        <input
                                            type="file"
                                            name="proof"
                                            accept="image/*"
                                            required
                                            class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:rounded-xl file:border-0 file:bg-emerald-600 file:px-3 file:py-2 file:text-xs file:font-bold file:text-white hover:file:bg-emerald-700 cursor-pointer"
                                        >
                                        <button
                                            type="submit"
                                            class="w-full sm:w-auto shrink-0 rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white shadow-xs transition hover:bg-slate-800 cursor-pointer dark:bg-slate-700 dark:hover:bg-slate-600"
                                        >
                                            <i class="fa-solid fa-cloud-arrow-up mr-1"></i> Unggah Bukti
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Items Table --}}
                <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900">

                    <div class="border-b border-slate-100 px-6 py-4 dark:border-slate-800">
                        <h2 class="font-bold text-slate-900 dark:text-white">
                            Produk Pesanan
                        </h2>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-slate-800">

                        @foreach ($order->items as $item)

                            <div class="flex gap-4 p-5 sm:p-6">

                                <div class="h-20 w-20 shrink-0 overflow-hidden rounded-2xl bg-slate-100 dark:bg-slate-800 sm:h-24 sm:w-24">

                                    @if ($item->product?->images?->first())
                                        <img
                                            src="{{ Storage::url($item->product->images->first()->image) }}"
                                            alt="{{ $item->product_name }}"
                                            class="h-full w-full object-cover"
                                        >
                                    @endif

                                </div>

                                <div class="min-w-0 flex-1">

                                    <h3 class="font-bold text-slate-900 text-sm sm:text-base dark:text-white">
                                        {{ $item->product_name ?? $item->product?->name }}
                                    </h3>

                                    @if($item->variant_name || $item->note)
                                        <p class="mt-1 inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                            <i class="fa-solid fa-layer-group text-[10px]"></i> Varian: {{ $item->variant_name ?: $item->note }}
                                        </p>
                                    @endif

                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                        {{ $item->quantity }} × Rp {{ number_format($item->price ?? $item->unit_price ?? 0, 0, ',', '.') }}
                                    </p>

                                    <p class="mt-2 font-black text-slate-900 text-sm sm:text-base dark:text-white">
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

                <div class="sticky top-24 rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs space-y-4 dark:border-slate-800 dark:bg-slate-900">

                    <h2 class="font-bold text-slate-900 border-b border-slate-100 pb-3 dark:text-white dark:border-slate-800">
                        Ringkasan Pembayaran
                    </h2>

                    <div class="space-y-3 text-xs sm:text-sm">

                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500 dark:text-slate-400">
                                Total Barang
                            </span>
                            <span class="font-bold text-slate-900 dark:text-white">
                                {{ $order->items->sum('quantity') }} pcs
                            </span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500 dark:text-slate-400">
                                Metode Pembayaran
                            </span>
                            <span class="font-bold text-slate-900 uppercase dark:text-white">
                                {{ $order->payment?->method ?? 'COD' }}
                            </span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500 dark:text-slate-400">
                                Status Pembayaran
                            </span>
                            <span class="font-bold text-emerald-700 dark:text-emerald-400">
                                {{ ucfirst($order->payment?->status ?? 'Pending') }}
                            </span>
                        </div>

                    </div>

                    <div class="border-t border-slate-100 pt-3 dark:border-slate-800">
                        <div class="flex justify-between gap-4">
                            <span class="font-bold text-slate-600 dark:text-slate-300">
                                Total Bayar
                            </span>

                            <span class="text-xl font-black text-emerald-700 dark:text-emerald-400">
                                Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    @if($waUrl)
                        <a
                            href="{{ $waUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="mt-4 flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3.5 text-xs font-bold text-white shadow-md transition hover:bg-emerald-700 cursor-pointer"
                        >
                            <i class="fa-brands fa-whatsapp text-lg"></i>
                            <span>Hubungi Seller via WA (Kirim Detail)</span>
                        </a>
                    @endif

                    @if ($order->status === 'completed')

                        <a
                            href="{{ route('buyer.reviews.create', ['order' => $order->id]) }}"
                            class="mt-3 flex w-full items-center justify-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-xs font-bold text-emerald-800 transition hover:bg-emerald-100 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300"
                        >
                            <i class="fa-solid fa-star text-amber-500"></i> Beri Ulasan Produk
                        </a>

                    @endif

                    @if (in_array($order->status, ['pending', 'confirmed', 'processing']))
                        <div x-data="{ showCancelModal: false }" class="mt-3">
                            <button
                                type="button"
                                @click="showCancelModal = true"
                                class="flex w-full items-center justify-center gap-2 rounded-2xl border border-red-200 bg-red-50 px-5 py-3 text-xs font-bold text-red-700 transition hover:bg-red-100 cursor-pointer dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-400"
                            >
                                <i class="fa-solid fa-ban"></i> {{ (strtolower($order->payment?->method ?? '') === 'qris' && ($order->payment?->proof || in_array($order->payment?->status, ['verified', 'paid']))) ? 'Ajukan Pembatalan Pesanan' : 'Batalkan Pesanan' }}
                            </button>

                            {{-- Cancel Modal --}}
                            <div
                                x-show="showCancelModal"
                                x-cloak
                                style="display: none;"
                                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs"
                            >
                                <div
                                    @click.away="showCancelModal = false"
                                    class="w-full max-w-md rounded-3xl bg-white p-6 shadow-xl dark:bg-slate-900 dark:border dark:border-slate-800 text-left"
                                >
                                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                            <i class="fa-solid fa-triangle-exclamation text-amber-500"></i> Konfirmasi Pembatalan
                                        </h3>
                                        <button @click="showCancelModal = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                            <i class="fa-solid fa-xmark text-lg"></i>
                                        </button>
                                    </div>

                                    <form action="{{ route('buyer.orders.cancel', $order) }}" method="POST" class="mt-4 space-y-4">
                                        @csrf
                                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                                            @if(strtolower($order->payment?->method ?? '') === 'qris' && ($order->payment?->proof || in_array($order->payment?->status, ['verified', 'paid'])))
                                                Karena Anda telah melakukan pembayaran QRIS, pengajuan pembatalan ini akan dikirimkan ke penjual untuk mengonfirmasi pengembalian dana (refund).
                                            @else
                                                Apakah Anda yakin ingin membatalkan pesanan ini? Stok barang akan dikembalikan secara otomatis.
                                            @endif
                                        </p>

                                        <div>
                                            <label for="reason" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                                Alasan Pembatalan <span class="text-red-500">*</span>
                                            </label>
                                            <textarea
                                                id="reason"
                                                name="reason"
                                                rows="3"
                                                required
                                                placeholder="Contoh: Salah memilih barang, mengubah rencana..."
                                                class="w-full rounded-2xl border border-slate-200 bg-white p-3 text-xs outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                            ></textarea>
                                        </div>

                                        <div class="flex justify-end gap-2 pt-2">
                                            <button
                                                type="button"
                                                @click="showCancelModal = false"
                                                class="rounded-xl bg-slate-100 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-200 cursor-pointer dark:bg-slate-800 dark:text-slate-300"
                                            >
                                                Kembali
                                            </button>
                                            <button
                                                type="submit"
                                                class="rounded-xl bg-red-600 px-4 py-2 text-xs font-bold text-white hover:bg-red-700 transition cursor-pointer"
                                            >
                                                Ya, Batalkan Pesanan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>

            </div>

        </div>

    </div>

</x-layouts.buyer>