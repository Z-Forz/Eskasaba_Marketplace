<x-layouts.seller title="Detail & Konfirmasi Pesanan">

    <div class="mx-auto max-w-5xl space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <a
                    href="{{ route('seller.orders.index', request()->query()) }}"
                    class="mb-2 inline-flex items-center gap-1 text-xs font-bold text-emerald-700 transition hover:text-emerald-800 dark:text-emerald-400">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Pesanan
                </a>

                <h1 class="text-2xl font-black text-slate-900 dark:text-white">
                    Pesanan {{ $order->invoice_number ?? '#' . $order->id }}
                </h1>

                <p class="mt-1 text-xs text-slate-500">
                    <i class="fa-regular fa-calendar mr-1"></i> Tanggal Pesanan: {{ $order->created_at?->format('d M Y, H:i') }}
                </p>
            </div>

            <x-badge
                :type="$order->status"
                :label="match($order->status) {
                    'cancel_requested'                  => 'Pengajuan Batal Pembeli',
                    'return_requested'                  => 'Pengajuan Return Barang',
                    'refund_pending_buyer_confirmation' => 'Menunggu Konfirmasi Refund Pembeli',
                    'cancelled'                         => 'Pembatalan Berhasil',
                    'refunded', 'returned'              => 'Return & Refund Berhasil',
                    default                             => ucfirst(str_replace('_', ' ', $order->status))
                }"
            />

        </div>


        {{-- Order Progress Stepper Bar --}}
        @php
            $statusStep = match($order->status) {
                'pending'          => 1,
                'confirmed'        => 2,
                'processing'       => 3,
                'ready_for_pickup' => 4,
                'completed'        => 5,
                default            => 0,
            };

            $progressWidthClass = match($statusStep) {
                1 => 'w-0',
                2 => 'w-1/4',
                3 => 'w-1/2',
                4 => 'w-3/4',
                5 => 'w-full',
                default => 'w-0',
            };

            // Format WhatsApp pre-filled text for seller to contact buyer
            $buyerName  = $order->user?->username ?? 'Pembeli';
            $sellerName = auth()->user()->username ?? 'Penjual';

            $itemsSummary = "";
            foreach($order->items as $idx => $item) {
                $pName = $item->product_name ?: $item->product?->name ?: 'Produk';
                $opt = $item->variant_name ?: $item->note;
                $qty = $item->quantity;
                $subTitle = !empty($opt) ? "{$opt} / {$qty} Pcs" : "{$qty} Pcs";
                $itemsSummary .= ($idx + 1) . ". *{$pName}*\n   └ {$subTitle}\n";
            }

            $sellerPhoneNum = $order->seller?->whatsapp_number ?: ($order->seller?->user?->phone ?: '-');
            $sellerWaText = "Halo Kak {$buyerName}, saya {$sellerName} (No. HP Toko: {$sellerPhoneNum}) dari Toko Eskasaba Marketplace.\n\n"
                . "📦 *INFORMASI PESANAN ANDA*\n"
                . "• Invoice: {$order->invoice_number}\n"
                . "• Status Pesanan: " . ucfirst(str_replace('_', ' ', $order->status)) . "\n"
                . "• Status Pembayaran: " . ucfirst($order->payment?->status ?? 'pending') . "\n\n"
                . "🛍️ *ITEM PESANAN:*\n" . $itemsSummary . "\n"
                . "📍 *TITIK PENGAMBILAN:* " . ($order->pickup_location ?? 'Kantin Sekolah') . "\n\n"
                . "Pesanan kakak sedang disiapkan. Silakan konfirmasi titik pengambilan ya. Terima kasih!";

            $buyerPhone = preg_replace('/[^0-9]/', '', $order->user?->phone ?? '');
            if (!empty($buyerPhone) && str_starts_with($buyerPhone, '0')) {
                $buyerPhone = '62' . substr($buyerPhone, 1);
            }
            $sellerWaUrl = !empty($buyerPhone) ? "https://wa.me/{$buyerPhone}?text=" . urlencode($sellerWaText) : null;
        @endphp

        @if(!in_array($order->status, ['cancelled', 'cancel_requested', 'return_requested', 'refund_pending_buyer_confirmation', 'refunded', 'returned']))
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-4">
                    Status Progres Pesanan
                </p>

                <div class="relative flex items-center justify-between">
                    {{-- Progress Line --}}
                    <div class="absolute left-4 right-4 top-4.5 -z-0 h-1 -translate-y-1/2 sm:left-5 sm:right-5">
                        <div class="h-full w-full rounded-full bg-slate-100 dark:bg-slate-800"></div>
                        <div class="absolute left-0 top-0 h-full rounded-full bg-emerald-600 transition-all duration-500 {{ $progressWidthClass }}"></div>
                    </div>

                    {{-- Step 1 --}}
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-bold shadow-xs {{ $statusStep >= 1 ? 'bg-emerald-700 text-white ring-4 ring-emerald-100 dark:ring-emerald-950' : 'bg-slate-100 text-slate-400 dark:bg-slate-800' }}">
                            1
                        </div>
                        <span class="mt-2 text-[11px] font-bold {{ $statusStep >= 1 ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-400' }}">Pending</span>
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

        {{-- Banner: Pengajuan Pembatalan / Return dari Pembeli --}}
        @if(in_array($order->status, ['cancel_requested', 'return_requested']))
            <div class="rounded-3xl border-2 border-amber-400 bg-amber-50/90 p-6 shadow-md dark:border-amber-700/80 dark:bg-amber-950/40" x-data="{ showRejectModal: false, showReturnItemModal: false }">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-500 text-white text-xl font-bold shadow-xs">
                        <i class="{{ $order->status === 'return_requested' ? 'fa-solid fa-rotate-left' : 'fa-solid fa-triangle-exclamation' }}"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-lg font-black text-amber-950 dark:text-amber-200">
                            {{ $order->status === 'return_requested' ? 'Pengajuan Return / Pengembalian Barang dari Pembeli (' . $order->user?->username . ')' : 'Pengajuan Pembatalan Pesanan dari Pembeli (' . $order->user?->username . ')' }}
                        </h2>
                        <p class="mt-1 text-xs text-amber-800 dark:text-amber-300">
                            Pembeli telah mengajukan {{ $order->status === 'return_requested' ? 'pengembalian barang (return)' : 'pembatalan pesanan' }} ini dengan alasan:
                            @if($order->previous_status)
                                <span class="ml-2 inline-block rounded-md bg-amber-200/80 px-2 py-0.5 text-[11px] font-bold text-amber-900 dark:bg-amber-900 dark:text-amber-200">
                                    Status Terakhir: {{ ucfirst(str_replace('_', ' ', $order->previous_status)) }}
                                </span>
                            @endif
                        </p>

                        <div class="mt-2 rounded-2xl bg-white/90 p-4 border border-amber-200 dark:border-amber-900/60 dark:bg-slate-900 space-y-3">
                            <p class="text-xs text-slate-800 dark:text-slate-200 font-semibold italic">"{{ $order->cancellation_reason }}"</p>

                            {{-- Foto Bukti Kondisi Barang (jika return_requested) --}}
                            @if($order->return_proof_image)
                                <div class="border-t border-slate-100 pt-3 dark:border-slate-800">
                                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-2">
                                        <i class="fa-solid fa-camera text-emerald-600 mr-1"></i> Foto Bukti Kondisi Barang dari Pembeli:
                                    </p>
                                    <div class="flex items-center gap-3">
                                        <img
                                            src="{{ Storage::url($order->return_proof_image) }}"
                                            alt="Bukti Foto Barang"
                                            class="h-24 w-24 rounded-2xl border border-slate-200 object-cover shadow-xs cursor-pointer hover:scale-105 transition"
                                            @click="showReturnItemModal = true"
                                        >
                                        <div>
                                            <button
                                                type="button"
                                                @click="showReturnItemModal = true"
                                                class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-3 py-1.5 text-xs font-bold text-white shadow-xs hover:bg-slate-800 transition cursor-pointer dark:bg-slate-800 dark:hover:bg-slate-700"
                                            >
                                                <i class="fa-solid fa-magnifying-glass-plus text-emerald-400"></i> Lihat Foto Bukti Penuh
                                            </button>
                                            <p class="mt-1 text-[11px] text-slate-500">Periksa foto kondisi barang yang dikomplain pembeli.</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Lightbox Modal foto barang --}}
                                <div
                                    x-show="showReturnItemModal"
                                    x-cloak
                                    @click="showReturnItemModal = false"
                                    @keydown.escape.window="showReturnItemModal = false"
                                    class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md cursor-pointer"
                                >
                                    <div class="relative max-w-lg w-full flex flex-col items-center justify-center p-2" @click.stop>
                                        <img
                                            src="{{ Storage::url($order->return_proof_image) }}"
                                            alt="Bukti Foto Barang"
                                            class="max-h-[80vh] max-w-full rounded-3xl bg-white p-3 shadow-2xl object-contain border-4 border-amber-500/30 dark:bg-slate-900"
                                        >
                                        <button
                                            type="button"
                                            @click="showReturnItemModal = false"
                                            class="absolute -top-3 -right-3 flex h-9 w-9 items-center justify-center rounded-full bg-slate-900 text-white shadow-lg border border-white/20 hover:bg-red-600 transition"
                                        >
                                            <i class="fa-solid fa-xmark text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Form Setujui Cancellation/Return --}}
                        <form action="{{ route('seller.orders.confirm-cancellation', $order) }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                            @csrf
                            @php
                                $hasPaid = strtolower($order->payment?->method ?? '') === 'qris' &&
                                    ($order->payment?->proof || in_array($order->payment?->status, ['verified', 'paid']));
                            @endphp

                            @if($hasPaid)
                                <div class="rounded-2xl bg-white p-4 border border-emerald-200 shadow-2xs dark:bg-slate-900 dark:border-slate-800 space-y-3">
                                    <label class="block text-xs font-bold text-slate-900 dark:text-white">
                                        <i class="fa-solid fa-receipt text-emerald-600 mr-1"></i> Unggah Foto Struk / Screenshot Bukti Transfer Pengembalian Dana (Refund QRIS) <span class="text-emerald-700 font-extrabold">(Wajib)</span>:
                                    </label>
                                    <input
                                        type="file"
                                        name="refund_proof"
                                        accept="image/*"
                                        class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:rounded-xl file:border-0 file:bg-emerald-700 file:px-3 file:py-2 file:text-xs file:font-bold file:text-white hover:file:bg-emerald-800 cursor-pointer"
                                    >
                                    <div>
                                        <label for="refund_notes" class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400 mb-1">Catatan Refund (Opsional):</label>
                                        <input
                                            type="text"
                                            id="refund_notes"
                                            name="refund_notes"
                                            placeholder="Contoh: Refund via ShopeePay/DANA a.n. Pembeli..."
                                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs outline-none focus:border-emerald-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                        >
                                    </div>
                                </div>
                            @else
                                <div class="rounded-2xl bg-amber-100/70 p-3.5 text-xs text-amber-900 dark:bg-amber-950/40 dark:text-amber-300">
                                    <p class="font-semibold"><i class="fa-solid fa-circle-info text-amber-600 mr-1"></i> Pembeli menggunakan pembayaran COD / belum membayar, sehingga refund tidak diperlukan.</p>
                                </div>
                            @endif

                            <div class="flex flex-wrap items-center gap-3">
                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-2 rounded-2xl bg-emerald-700 px-5 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-emerald-800 transition cursor-pointer"
                                >
                                    <i class="fa-solid fa-check-circle"></i> Setujui {{ $order->status === 'return_requested' ? 'Pengajuan Return & Refund' : 'Pembatalan Pesanan' }}
                                </button>

                                <button
                                    type="button"
                                    @click="showRejectModal = true"
                                    class="inline-flex items-center gap-2 rounded-2xl border border-red-300 bg-red-50 px-5 py-2.5 text-xs font-bold text-red-700 hover:bg-red-100 transition cursor-pointer dark:bg-red-950/40 dark:border-red-900 dark:text-red-400"
                                >
                                    <i class="fa-solid fa-xmark"></i> Tolak Pengajuan
                                </button>
                            </div>
                        </form>

                        {{-- Reject Modal --}}
                        <div
                            x-show="showRejectModal"
                            x-cloak
                            style="display: none;"
                            class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs"
                        >
                            <div
                                @click.away="showRejectModal = false"
                                class="w-full max-w-md rounded-3xl bg-white p-6 shadow-xl dark:bg-slate-900 dark:border dark:border-slate-800 text-left"
                            >
                                <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                        <i class="fa-solid fa-ban text-red-600"></i> Tolak Pengajuan {{ $order->status === 'return_requested' ? 'Return' : 'Pembatalan' }}
                                    </h3>
                                    <button @click="showRejectModal = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                        <i class="fa-solid fa-xmark text-lg"></i>
                                    </button>
                                </div>

                                <form action="{{ route('seller.orders.reject-cancellation', $order) }}" method="POST" class="mt-4 space-y-4">
                                    @csrf
                                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                                        Berikan alasan penolakan yang jelas agar pembeli dapat memahami pertimbangan Anda.
                                    </p>

                                    <div>
                                        <label for="rejection_reason" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                            Alasan Penolakan <span class="text-red-500">*</span>
                                        </label>
                                        <textarea
                                            id="rejection_reason"
                                            name="rejection_reason"
                                            rows="3"
                                            required
                                            placeholder="Contoh: Pesanan makanan sudah terlanjur siap disajikan, atau bukti foto tidak menunjukkan kerusakan..."
                                            class="w-full rounded-2xl border border-slate-200 bg-white p-3 text-xs outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                        ></textarea>
                                    </div>

                                    <div class="flex justify-end gap-2 pt-2">
                                        <button
                                            type="button"
                                            @click="showRejectModal = false"
                                            class="rounded-xl bg-slate-100 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-200 cursor-pointer dark:bg-slate-800 dark:text-slate-300"
                                        >
                                            Batal
                                        </button>
                                        <button
                                            type="submit"
                                            class="rounded-xl bg-red-600 px-4 py-2 text-xs font-bold text-white hover:bg-red-700 transition cursor-pointer"
                                        >
                                            Kirim Penolakan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @elseif($order->status === 'refund_pending_buyer_confirmation')
            <div class="rounded-3xl border-2 border-emerald-400 bg-emerald-50/90 p-6 shadow-md dark:border-emerald-800 dark:bg-emerald-950/40">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-600 text-white text-xl font-bold shadow-xs">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-lg font-black text-emerald-950 dark:text-emerald-200">
                            Bukti Pengembalian Dana (Refund) Telah Diunggah
                        </h2>
                        <p class="mt-1 text-xs text-emerald-800 dark:text-emerald-300">
                            Anda telah mengunggah bukti refund sebesar <strong>Rp {{ number_format((float) ($order->total_price ?? 0), 0, ',', '.') }}</strong>. Sistem saat ini sedang **menunggu konfirmasi dari pembeli** ({{ $order->user?->username }}) bahwa dana telah diterima di rekening/e-wallet mereka.
                        </p>

                        @if($order->payment?->refund_proof)
                            <div class="mt-3 rounded-2xl bg-white p-4 border border-emerald-200 shadow-2xs dark:bg-slate-900 dark:border-slate-800 flex items-center gap-4" x-data="{ showSellerRefundModal: false }">
                                <img
                                    src="{{ Storage::url($order->payment->refund_proof) }}"
                                    alt="Bukti Refund Penjual"
                                    class="h-20 w-20 rounded-xl border border-slate-200 object-cover shadow-xs cursor-pointer hover:scale-105 transition"
                                    @click="showSellerRefundModal = true"
                                >
                                <div>
                                    <p class="text-xs font-bold text-slate-900 dark:text-white">Foto Struk Refund Dikirimkan</p>
                                    @if($order->payment->refund_notes)
                                        <p class="mt-0.5 text-xs text-slate-500 italic">"{{ $order->payment->refund_notes }}"</p>
                                    @endif
                                    <button
                                        type="button"
                                        @click="showSellerRefundModal = true"
                                        class="mt-2 inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 hover:underline dark:text-emerald-400"
                                    >
                                        <i class="fa-solid fa-magnifying-glass-plus"></i> Lihat Bukti Refund
                                    </button>
                                </div>

                                {{-- Modal --}}
                                <div
                                    x-show="showSellerRefundModal"
                                    x-cloak
                                    @click="showSellerRefundModal = false"
                                    class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md cursor-pointer"
                                >
                                    <div class="relative max-w-lg w-full flex flex-col items-center justify-center p-2" @click.stop>
                                        <img
                                            src="{{ Storage::url($order->payment->refund_proof) }}"
                                            alt="Bukti Refund"
                                            class="max-h-[80vh] max-w-full rounded-3xl bg-white p-3 shadow-2xl object-contain border-4 border-emerald-500/30 dark:bg-slate-900"
                                        >
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @elseif(in_array($order->status, ['cancelled', 'refunded', 'returned']))
            <div class="rounded-3xl border border-emerald-300 bg-emerald-50/90 p-6 shadow-xs dark:border-emerald-800 dark:bg-emerald-950/40">
                <div class="flex items-start gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-800 text-xl font-bold dark:bg-emerald-900/60 dark:text-emerald-300">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-base font-black text-emerald-950 dark:text-emerald-200">
                            {{ $order->status === 'returned' ? 'Return & Refund Berhasil' : 'Pembatalan Pesanan Berhasil' }}
                        </h2>
                        <p class="mt-1 text-xs font-semibold text-emerald-800 dark:text-emerald-300">
                            Dibatalkan Oleh: <strong>{{ match($order->cancelled_by) { 'buyer' => 'Pembeli', 'seller' => 'Penjual Toko (Anda)', 'admin' => 'Admin Sekolah', default => 'Sistem' } }}</strong>
                            @if($order->previous_status)
                                <span class="ml-2 inline-block rounded-md bg-emerald-100 px-2 py-0.5 text-[11px] font-bold text-emerald-900 dark:bg-emerald-900 dark:text-emerald-200">
                                    Status Terakhir: {{ ucfirst(str_replace('_', ' ', $order->previous_status)) }}
                                </span>
                            @endif
                        </p>
                        @if($order->cancellation_reason)
                            <div class="mt-2.5 rounded-2xl bg-white/90 p-3.5 border border-emerald-200/80 dark:border-emerald-900/40 dark:bg-slate-900/60">
                                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wide">Alasan Pembatalan / Return:</p>
                                <p class="mt-0.5 text-xs text-slate-800 dark:text-slate-200 font-medium italic">"{{ $order->cancellation_reason }}"</p>
                            </div>
                        @endif

                        {{-- Bukti Refund Display --}}
                        @if($order->payment?->refund_proof)
                            <div class="mt-4 rounded-2xl border border-emerald-200 bg-white p-4 shadow-2xs dark:border-slate-800 dark:bg-slate-900" x-data="{ showRefundModal: false }">
                                <p class="text-xs font-bold text-emerald-800 dark:text-emerald-300 flex items-center gap-1.5 mb-3">
                                    <i class="fa-solid fa-file-circle-check text-emerald-600 text-sm"></i> Foto Bukti Pengembalian Dana (Refund QRIS)
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
                                            Status Refund: <span class="text-emerald-700 dark:text-emerald-400 font-black">✓ Lunas Dikembalikan & Dikonfirmasi Pembeli</span>
                                        </p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                            Pengembalian dana sebesar <strong>Rp {{ number_format((float) ($order->total_price ?? 0), 0, ',', '.') }}</strong> telah selesai dan terkonfirmasi.
                                        </p>
                                        @if($order->payment->refund_notes)
                                            <p class="mt-1 text-xs text-slate-600 dark:text-slate-300 italic">"{{ $order->payment->refund_notes }}"</p>
                                        @endif
                                        <div class="mt-3">
                                            <button
                                                type="button"
                                                @click="showRefundModal = true"
                                                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-700 px-3 py-1.5 text-xs font-bold text-white shadow-xs hover:bg-emerald-800 transition cursor-pointer"
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
                                    @click="showRefundModal = false"
                                    @keydown.escape.window="showRefundModal = false"
                                    class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md cursor-pointer select-none"
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
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Buyer & Pickup Info --}}
        <div class="grid gap-6 sm:grid-cols-2">

            {{-- Buyer Details Card --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900 flex flex-col justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">
                        <i class="fa-solid fa-user mr-1.5 text-slate-500"></i> Informasi Pembeli
                    </h2>

                    <div class="mt-4 space-y-3 text-sm">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-400">Nama Pembeli</p>
                            <p class="mt-0.5 font-bold text-slate-900 dark:text-white">
                                {{ $order->user?->username ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-400">Kontak Pembeli</p>
                            <p class="mt-0.5 font-semibold text-slate-700 dark:text-slate-300">
                                {{ $order->user?->phone ?? $order->user?->email ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                @if($sellerWaUrl)
                    <div class="mt-5 border-t border-slate-100 pt-4 dark:border-slate-800">
                        <a
                            href="{{ $sellerWaUrl }}"
                            target="_blank"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-700 cursor-pointer"
                        >
                            <i class="fa-brands fa-whatsapp text-base"></i>
                            <span>Chat WA Pembeli (Kirim Info)</span>
                        </a>
                    </div>
                @endif
            </div>

            {{-- Pickup & Note Details --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <h2 class="text-base font-bold text-slate-900 dark:text-white">
                    <i class="fa-solid fa-location-dot mr-1.5 text-emerald-600"></i>Titik Temu & Catatan
                </h2>

                <div class="mt-4 space-y-3 text-sm">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400">Lokasi Pengambilan (Sekolah)</p>
                        <p class="mt-0.5 font-bold text-emerald-700 dark:text-emerald-400">
                            <i class="fa-solid fa-map-pin mr-1"></i> {{ $order->pickup_location ?? 'Belum ditentukan' }}
                        </p>
                    </div>

                    @if($order->note)
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-400">Catatan dari Pembeli</p>
                            <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-300 italic">
                                "{{ $order->note }}"
                            </p>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Payment & QRIS Info Card --}}
        <div class="rounded-3xl border border-emerald-200/80 bg-emerald-50/50 p-6 shadow-xs dark:border-emerald-950 dark:bg-emerald-950/20">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-black uppercase tracking-wide
                        {{ strtolower($order->payment?->method ?? '') === 'qris' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200' : 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-200' }}"
                    >
                        <i class="{{ strtolower($order->payment?->method ?? '') === 'qris' ? 'fa-solid fa-qrcode' : 'fa-solid fa-money-bill-wave' }}"></i>
                        {{ strtolower($order->payment?->method ?? '') === 'qris' ? 'Pembayaran QRIS Non-Tunai' : 'Pembayaran Tunai (COD)' }}
                    </span>

                    <h2 class="mt-2 text-lg font-black text-slate-900 dark:text-white">
                        Status Pembayaran:
                        <span class="
                            {{ match(true) {
                                in_array($order->payment?->status, ['refunded']) || in_array($order->status, ['refunded', 'returned', 'refund_pending_buyer_confirmation']) || !empty($order->payment?->refund_proof) => 'text-emerald-700 dark:text-emerald-400',
                                in_array($order->payment?->status, ['verified', 'paid']) => 'text-emerald-700 dark:text-emerald-400',
                                $order->payment?->status === 'rejected' => 'text-red-600 dark:text-red-400',
                                default => 'text-amber-600 dark:text-amber-400'
                            } }}"
                        >
                            {{ match(true) {
                                in_array($order->payment?->status, ['refunded']) || in_array($order->status, ['refunded', 'returned', 'refund_pending_buyer_confirmation']) || !empty($order->payment?->refund_proof) => 'Refunded (Pengembalian Dana)',
                                in_array($order->payment?->status, ['verified', 'paid']) => 'Terverifikasi (Lunas)',
                                $order->payment?->status === 'rejected' => 'Pembayaran Ditolak',
                                default => 'Menunggu Konfirmasi / Verifikasi Seller'
                            } }}
                        </span>
                    </h2>
                </div>

                <div class="text-right">
                    <p class="text-xs uppercase tracking-wide text-slate-400 font-semibold">Total Tagihan Pesanan</p>
                    <p class="text-2xl font-black text-emerald-700 dark:text-emerald-400">
                        Rp {{ number_format((float) ($order->total_price ?? 0), 0, ',', '.') }}
                    </p>
                </div>
            </div>

            {{-- QRIS Preview for Seller --}}
            @if(strtolower($order->payment?->method ?? '') === 'qris')
                <div class="mt-5 space-y-4">
                    {{-- Uploaded Proof Card --}}
                    @if($order->payment?->proof)
                        <div class="rounded-3xl border-2 border-emerald-500/80 bg-emerald-50/50 p-5 dark:border-emerald-700/60 dark:bg-emerald-950/40" x-data="{ showProofModal: false }">
                            <div class="flex flex-col sm:flex-row items-center gap-5">
                                <div class="relative shrink-0">
                                    <img
                                        src="{{ Storage::url($order->payment->proof) }}"
                                        alt="Bukti Transfer Pembeli"
                                        class="h-32 w-32 rounded-2xl border border-emerald-200 object-cover shadow-md cursor-pointer hover:opacity-90 hover:scale-105 transition"
                                        @click="showProofModal = true"
                                    >
                                </div>

                                <div class="flex-1 text-center sm:text-left">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-900 border border-emerald-300 dark:bg-emerald-950 dark:text-emerald-300">
                                        <i class="fa-solid fa-shield-check text-emerald-600"></i> Bukti Transfer Diunggah Pembeli
                                    </span>

                                    <h3 class="mt-2 text-sm font-bold text-slate-900 dark:text-white">
                                        Verifikasi Dana Masuk Sebesar: <span class="text-emerald-700 dark:text-emerald-400 font-black">Rp {{ number_format((float) ($order->total_price ?? 0), 0, ',', '.') }}</span>
                                    </h3>

                                    <p class="mt-1 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                                        <i class="fa-solid fa-shield-halved text-emerald-600 mr-1"></i> <strong>Keamanan Pembayaran:</strong> Pastikan nama pengirim dan nominal mutasi di aplikasi m-banking / e-wallet Anda sudah sesuai dengan foto bukti transfer di atas sebelum memproses pesanan.
                                    </p>

                                    @if(!in_array($order->status, ['cancelled', 'refunded', 'returned', 'refund_pending_buyer_confirmation', 'cancel_requested', 'return_requested']))
                                        <div class="mt-3 flex flex-wrap items-center gap-2">
                                            <button
                                                type="button"
                                                @click="showProofModal = true"
                                                class="inline-flex items-center gap-1.5 rounded-xl bg-white px-3.5 py-1.5 text-xs font-bold text-slate-800 shadow-2xs border border-slate-200 hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-200 dark:border-slate-700 cursor-pointer"
                                            >
                                                <i class="fa-solid fa-magnifying-glass-plus text-emerald-600"></i> Perbesar Bukti Transfer
                                            </button>

                                            @if($order->payment->status !== 'verified' && $order->payment->status !== 'paid')
                                                <form action="{{ route('seller.orders.update', $order) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="payment_status" value="verified">
                                                    <button
                                                        type="submit"
                                                        class="inline-flex items-center gap-1 rounded-xl bg-emerald-700 px-3.5 py-1.5 text-xs font-bold text-white shadow-xs hover:bg-emerald-800 transition cursor-pointer"
                                                    >
                                                        <i class="fa-solid fa-check-double"></i> Konfirmasi Pembayaran Valid (Lunas)
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Centered Lightbox Modal --}}
                            <div
                                x-show="showProofModal"
                                x-cloak
                                @click="showProofModal = false"
                                @keydown.escape.window="showProofModal = false"
                                class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md cursor-pointer select-none"
                            >
                                <div class="relative max-w-lg w-full flex flex-col items-center justify-center p-2" @click.stop>
                                    <div class="relative w-full flex justify-center">
                                        <img
                                            src="{{ Storage::url($order->payment->proof) }}"
                                            alt="Bukti Transfer Pembeli"
                                            class="max-h-[80vh] max-w-full rounded-3xl bg-white p-3 shadow-2xl object-contain border-4 border-emerald-500/30 dark:bg-slate-900"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="rounded-2xl border border-amber-200 bg-amber-50/60 p-4 dark:border-amber-900/50 dark:bg-amber-950/20 flex items-center gap-3">
                            <i class="fa-solid fa-clock text-amber-600 text-lg"></i>
                            <div>
                                <p class="text-xs font-bold text-amber-900 dark:text-amber-300">Menunggu Pembeli Mengunggah Bukti Pembayaran</p>
                                <p class="text-[11px] text-amber-700 dark:text-amber-400">Pembeli belum mengunggah struk/screenshot transfer QRIS. Anda dapat menanyakan via WhatsApp.</p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Order Items --}}
        <div class="rounded-3xl border border-slate-200/80 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900 overflow-hidden">

            <div class="border-b border-slate-100 p-5 dark:border-slate-800">
                <h2 class="text-base font-bold text-slate-900 dark:text-white">
                    <i class="fa-solid fa-box mr-1.5 text-slate-500"></i> Produk Pesanan
                </h2>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($order->items as $item)
                    @php
                        $opt = $item->variant_name ?: $item->note;
                        $subTitle = !empty($opt) ? "{$opt} / {$item->quantity} Pcs" : "{$item->quantity} Pcs";
                    @endphp
                    <div class="flex items-center justify-between p-5">
                        <div>
                            <p class="font-bold text-slate-900 dark:text-white text-base">
                                {{ $item->product_name ?? $item->product?->name }}
                            </p>
                            <p class="mt-1 inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                <i class="fa-solid fa-layer-group text-[10px]"></i> {{ $subTitle }}
                            </p>
                            <p class="mt-1 text-xs text-slate-400">
                                Harga Satuan: Rp {{ number_format((float) ($item->price ?? $item->unit_price ?? 0), 0, ',', '.') }}
                            </p>
                        </div>

                        <p class="font-extrabold text-slate-900 dark:text-white text-base">
                            Rp {{ number_format((float) (($item->quantity) * ($item->price ?? $item->unit_price ?? 0)), 0, ',', '.') }}
                        </p>
                    </div>
                @endforeach
            </div>

        </div>

        {{-- Seller Update Form (STRICTLY HIDDEN IF ORDER IS CANCELLED / REFUNDED / RETURNED / IN RETURN-REFUND FLOW) --}}
        @if(!in_array($order->status, ['cancelled', 'refunded', 'returned', 'refund_pending_buyer_confirmation', 'cancel_requested', 'return_requested']))
            <div
                x-data="{ locationInput: '{{ old('pickup_location', $order->pickup_location ?? 'Kantin Utama') }}', showSellerCancelModal: false }"
                class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900"
            >

                <h2 class="text-base font-black text-slate-900 dark:text-white">
                    <i class="fa-solid fa-bolt text-emerald-600 mr-1.5"></i> Konfirmasi Pesanan & Pembayaran QRIS (Seller)
                </h2>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                    Konfirmasi pembayaran QRIS dan perbarui status pengerjaan atau lokasi pengambilan barang.
                </p>

                <form
                    action="{{ route('seller.orders.update', $order) }}"
                    method="POST"
                    class="mt-5 space-y-5"
                >
                    @csrf
                    @method('PUT')

                    <div class="grid gap-4 sm:grid-cols-2">

                        {{-- Order Status --}}
                        <div>
                            <label for="status" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Status Pesanan
                            </label>
                            <x-custom-select
                                name="status"
                                :options="[
                                    'pending'          => 'Menunggu Konfirmasi',
                                    'confirmed'        => 'Dikonfirmasi (Diterima)',
                                    'processing'       => 'Sedang Diproses',
                                    'ready_for_pickup' => 'Siap Diambil (Ready for Pickup)',
                                    'completed'        => 'Pesanan Selesai',
                                ]"
                                :selected="$order->status"
                                placeholder=""
                            />
                        </div>

                        {{-- QRIS Payment Verification --}}
                        <div>
                            <label for="payment_status" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Konfirmasi Pembayaran QRIS / COD
                            </label>
                            @if(in_array($order->payment?->status, ['verified', 'paid']))
                                <div class="w-full rounded-2xl border border-emerald-200 bg-emerald-50/80 px-4 py-3 text-xs font-extrabold text-emerald-800 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300 flex items-center justify-between">
                                    <span class="flex items-center gap-2">
                                        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                                        Pembayaran Lunas & Terverifikasi
                                    </span>
                                    <span class="text-[10px] uppercase tracking-wider text-emerald-700 dark:text-emerald-400 font-black bg-emerald-100 dark:bg-emerald-900/80 px-2 py-0.5 rounded-md border border-emerald-300 dark:border-emerald-800">
                                        <i class="fa-solid fa-lock text-[9px] mr-1"></i> Terkunci
                                    </span>
                                </div>
                                <input type="hidden" name="payment_status" value="{{ $order->payment->status }}">
                            @else
                                <x-custom-select
                                    name="payment_status"
                                    :options="[
                                        'pending'  => 'Menunggu Pembayaran / Verifikasi',
                                        'verified' => 'Terverifikasi / Pembayaran Lunas',
                                        'rejected' => 'Pembayaran Ditolak',
                                    ]"
                                    :selected="($order->payment?->status === 'paid' ? 'verified' : ($order->payment?->status ?? 'pending'))"
                                    placeholder=""
                                />
                            @endif
                        </div>

                    </div>

                    {{-- Pickup Location Input & Quick Chips --}}
                    <div>
                        <label for="pickup_location" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Lokasi Titik Pengambilan di Sekolah (COD)
                        </label>

                        <input
                            id="pickup_location"
                            type="text"
                            name="pickup_location"
                            x-model="locationInput"
                            placeholder="Contoh: Kantin Utama, Gazebo RPL, Depan Perpus..."
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >

                        {{-- Quick Recommendation Chips --}}
                        <div class="mt-2.5 flex flex-wrap items-center gap-2">
                            <span class="text-xs font-medium text-slate-400">Pilih Cepat:</span>
                            <button
                                type="button"
                                @click="locationInput = 'Kantin Utama Sekolah'"
                                class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700 transition hover:bg-emerald-50 hover:text-emerald-800 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                            >
                                <i class="fa-solid fa-utensils mr-1"></i> Kantin Utama
                            </button>
                            <button
                                type="button"
                                @click="locationInput = 'Gazebo RPL / Lapangan Tengah'"
                                class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700 transition hover:bg-emerald-50 hover:text-emerald-800 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                            >
                                <i class="fa-solid fa-tree mr-1"></i> Gazebo RPL
                            </button>
                            <button
                                type="button"
                                @click="locationInput = 'Depan Perpustakaan Sekolah'"
                                class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700 transition hover:bg-emerald-50 hover:text-emerald-800 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                            >
                                <i class="fa-solid fa-book mr-1"></i> Depan Perpus
                            </button>
                            <button
                                type="button"
                                @click="locationInput = 'Pos Satpam Gerbang Sekolah'"
                                class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700 transition hover:bg-emerald-50 hover:text-emerald-800 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                            >
                                <i class="fa-solid fa-building-shield mr-1"></i> Pos Satpam Gerbang
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-4 border-t border-slate-100 pt-5 dark:border-slate-800">
                        {{-- Seller Direct Cancel Trigger (Left) --}}
                        @if(in_array($order->status, ['pending', 'confirmed', 'processing']))
                            <button
                                type="button"
                                @click="showSellerCancelModal = true"
                                class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-2xl border border-red-200 bg-red-50 px-5 py-3 text-xs font-bold text-red-700 hover:bg-red-100 transition cursor-pointer dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-400"
                            >
                                <i class="fa-solid fa-ban"></i> Batalkan Pesanan Ini
                            </button>
                        @else
                            <div></div>
                        @endif

                        {{-- Save Submit Button (Right) --}}
                        <button
                            type="submit"
                            class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-6 py-3 text-sm font-bold text-white shadow-xs transition hover:bg-emerald-800 cursor-pointer"
                        >
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Konfirmasi Pesanan & Pembayaran
                        </button>
                    </div>

                </form>

                {{-- Cancel Modal --}}
                @if(in_array($order->status, ['pending', 'confirmed', 'processing']))
                    <div
                        x-show="showSellerCancelModal"
                        x-cloak
                        style="display: none;"
                        class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs"
                    >
                        <div
                            @click.away="showSellerCancelModal = false"
                            class="w-full max-w-md rounded-3xl bg-white p-6 shadow-xl dark:bg-slate-900 dark:border dark:border-slate-800 text-left"
                        >
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <i class="fa-solid fa-ban text-red-600"></i> Batalkan Pesanan oleh Penjual
                                </h3>
                                <button @click="showSellerCancelModal = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                    <i class="fa-solid fa-xmark text-lg"></i>
                                </button>
                            </div>

                            <form action="{{ route('seller.orders.cancel', $order) }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                                @csrf
                                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                                    Membatalkan pesanan akan mengembalikan stok barang ke toko secara otomatis.
                                </p>

                                <div>
                                    <label for="seller_reason" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        Alasan Pembatalan <span class="text-red-500">*</span>
                                    </label>
                                    <textarea
                                        id="seller_reason"
                                        name="reason"
                                        rows="3"
                                        required
                                        placeholder="Contoh: Stok barang fisik habis, bahan baku tidak tersedia..."
                                        class="w-full rounded-2xl border border-slate-200 bg-white p-3 text-xs outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                    ></textarea>
                                </div>

                                @if(strtolower($order->payment?->method ?? '') === 'qris' && ($order->payment?->proof || in_array($order->payment?->status, ['verified', 'paid'])))
                                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-3.5 space-y-2 dark:border-emerald-900 dark:bg-emerald-950/30">
                                        <label class="block text-xs font-bold text-slate-900 dark:text-white">
                                            <i class="fa-solid fa-receipt text-emerald-600"></i> Unggah Bukti Refund QRIS (Jika Dana Sudah Masuk):
                                        </label>
                                        <input
                                            type="file"
                                            name="refund_proof"
                                            accept="image/*"
                                            class="w-full text-xs text-slate-500 file:mr-2 file:rounded-xl file:border-0 file:bg-emerald-700 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-white cursor-pointer"
                                        >
                                    </div>
                                @endif

                                <div class="flex justify-end gap-2 pt-2">
                                    <button
                                        type="button"
                                        @click="showSellerCancelModal = false"
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
                @endif

            </div>
        @endif

    </div>

</x-layouts.seller>