<x-layouts.admin title="Detail Pesanan">
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a
                    href="{{ route('admin.orders.index', request()->query()) }}"
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
                {{ match($order->status) {
                    'completed'        => 'bg-green-100 text-green-700 dark:bg-green-950/40 dark:text-green-400',
                    'cancelled'        => 'bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-400',
                    'cancel_requested' => 'bg-orange-100 text-orange-700 dark:bg-orange-950/40 dark:text-orange-400',
                    default            => 'bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400'
                } }}"
            >
                <i class="fa-solid fa-circle-info mr-1"></i> {{ $order->status === 'cancel_requested' ? 'Pengajuan Pembatalan' : ucfirst(str_replace('_', ' ', $order->status)) }}
            </span>
        </div>

        {{-- Cancellation & Refund Details Card for Admin --}}
        @if($order->status === 'cancel_requested' || $order->status === 'cancelled')
            <div class="rounded-3xl border {{ $order->status === 'cancel_requested' ? 'border-amber-200 bg-amber-50/80 dark:border-amber-900/60 dark:bg-amber-950/40' : 'border-red-200 bg-red-50/80 dark:border-red-900/60 dark:bg-red-950/40' }} p-6 shadow-xs">
                <div class="flex items-start gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl {{ $order->status === 'cancel_requested' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300' : 'bg-red-100 text-red-700 dark:bg-red-900/60 dark:text-red-300' }} text-xl font-bold">
                        <i class="{{ $order->status === 'cancel_requested' ? 'fa-solid fa-clock-rotate-left' : 'fa-solid fa-circle-xmark' }}"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-base font-black {{ $order->status === 'cancel_requested' ? 'text-amber-900 dark:text-amber-300' : 'text-red-900 dark:text-red-300' }}">
                            {{ $order->status === 'cancel_requested' ? 'Pengajuan Pembatalan Dalam Proses Verifikasi Seller' : 'Pesanan Ini Dibatalkan' }}
                        </h2>
                        <p class="mt-1 text-xs font-semibold text-slate-700 dark:text-slate-300">
                            Inisiator Pembatalan: <strong>{{ match($order->cancelled_by) { 'buyer' => 'Pembeli', 'seller' => 'Penjual Toko', 'admin' => 'Admin Sekolah', default => 'Sistem' } }}</strong>
                        </p>

                        @if($order->cancellation_reason)
                            <div class="mt-2.5 rounded-2xl bg-white/90 p-3.5 border border-slate-200/80 dark:border-slate-800 dark:bg-slate-900">
                                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wide">Alasan Pembatalan:</p>
                                <p class="mt-0.5 text-xs text-slate-800 dark:text-slate-200 italic font-medium">"{{ $order->cancellation_reason }}"</p>
                            </div>
                        @endif

                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Bukti Pembayaran QRIS --}}
                            @if($order->payment?->proof)
                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs dark:border-slate-800 dark:bg-slate-900" x-data="{ showProofModal: false }">
                                    <p class="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-1.5 mb-2">
                                        <i class="fa-solid fa-qrcode text-emerald-600"></i> Bukti Pembayaran Pembeli (QRIS)
                                    </p>
                                    <div class="flex items-center gap-3">
                                        <img
                                            src="{{ Storage::url($order->payment->proof) }}"
                                            alt="Bukti Transfer"
                                            class="h-20 w-20 rounded-xl border border-slate-200 object-cover shadow-xs cursor-pointer hover:opacity-90 hover:scale-105 transition"
                                            @click="showProofModal = true"
                                        >
                                        <div>
                                            <p class="text-[11px] font-semibold text-slate-500">Status Pembayaran: {{ ucfirst($order->payment->status) }}</p>
                                            <button
                                                type="button"
                                                @click="showProofModal = true"
                                                class="mt-1.5 inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 hover:underline dark:text-emerald-400 cursor-pointer"
                                            >
                                                <i class="fa-solid fa-magnifying-glass-plus text-[10px]"></i> Perbesar Pembayaran
                                            </button>
                                        </div>
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
                                                    alt="Bukti Transfer"
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

                            {{-- Bukti Pengembalian Dana (Refund) --}}
                            @if($order->payment?->refund_proof)
                                <div class="rounded-2xl border border-emerald-200 bg-white p-4 shadow-2xs dark:border-slate-800 dark:bg-slate-900" x-data="{ showRefundModal: false }">
                                    <p class="text-xs font-bold text-emerald-800 dark:text-emerald-300 flex items-center gap-1.5 mb-2">
                                        <i class="fa-solid fa-file-circle-check text-emerald-600"></i> Bukti Pengembalian Dana (Refund QRIS)
                                    </p>
                                    <div class="flex items-center gap-3">
                                        <img
                                            src="{{ Storage::url($order->payment->refund_proof) }}"
                                            alt="Bukti Refund"
                                            class="h-20 w-20 rounded-xl border border-emerald-200 object-cover shadow-xs cursor-pointer hover:opacity-90 hover:scale-105 transition"
                                            @click="showRefundModal = true"
                                        >
                                        <div>
                                            <p class="text-[11px] font-bold text-emerald-700 dark:text-emerald-400">✓ Refund Berhasil Unggah</p>
                                            @if($order->payment->refund_notes)
                                                <p class="text-[11px] text-slate-500 italic truncate">{{ $order->payment->refund_notes }}</p>
                                            @endif
                                            <button
                                                type="button"
                                                @click="showRefundModal = true"
                                                class="mt-1.5 inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 hover:underline dark:text-emerald-400 cursor-pointer"
                                            >
                                                <i class="fa-solid fa-magnifying-glass-plus text-[10px]"></i> Perbesar Bukti Refund
                                            </button>
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
                                                    alt="Bukti Refund"
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
            </div>
        @endif

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

                                    @php
                                        $opt = $item->variant_name ?: $item->note;
                                        $subTitle = !empty($opt) ? "{$opt} / " . ($item->quantity ?? 1) . " Pcs" : ($item->quantity ?? 1) . " Pcs";
                                    @endphp
                                    <div class="mt-1">
                                        <span class="inline-flex items-center gap-1 rounded-xl bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                            <i class="fa-solid fa-layer-group text-[9px]"></i> {{ $subTitle }}
                                        </span>
                                    </div>

                                    <p class="mt-2 text-xs font-semibold text-slate-400">
                                        Harga Satuan: Rp {{ number_format($item->unit_price ?? $item->price ?? 0, 0, ',', '.') }}
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