<x-layouts.seller title="Detail Pembayaran">

    <div class="mx-auto max-w-3xl space-y-6">

        <div>
            <a
                href="{{ route('seller.payments.index') }}"
                class="text-xs font-bold text-emerald-700 transition hover:text-emerald-800 dark:text-emerald-400"
            >
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Riwayat Pembayaran
            </a>

            <h1 class="mt-2 text-2xl font-black text-slate-900 dark:text-white">
                Detail Transaksi Pembayaran
            </h1>
        </div>

        <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900 sm:p-8">

            <div class="flex flex-col gap-4 border-b border-slate-100 pb-5 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800">

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Nomor Invoice Pesanan
                    </p>

                    <p class="mt-0.5 text-xl font-black text-slate-900 dark:text-white">
                        {{ $payment->order?->invoice_number ?? '#' . $payment->order_id }}
                    </p>
                </div>

                <span class="w-fit rounded-full px-3.5 py-1 text-xs font-bold
                    {{ match($payment->status) {
                        'verified' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300',
                        'pending'  => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300',
                        'rejected' => 'bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-300',
                        default    => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300'
                    } }}"
                >
                    Status: {{ ucfirst($payment->status ?? 'pending') }}
                </span>

            </div>

            <div class="mt-6 grid gap-6 sm:grid-cols-2">

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Metode Pembayaran
                    </p>

                    <p class="mt-1 font-bold text-slate-900 dark:text-white text-base">
                        <i class="{{ $payment->method === 'qris' ? 'fa-solid fa-qrcode text-emerald-600' : 'fa-solid fa-money-bill-wave text-slate-600' }} mr-1"></i>
                        {{ $payment->method === 'qris' ? 'QRIS Non-Tunai' : 'Cash On Delivery (COD)' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Total Tagihan
                    </p>

                    <p class="mt-1 text-xl font-black text-emerald-700 dark:text-emerald-400">
                        Rp {{ number_format($payment->amount ?? $payment->order?->total_price ?? 0, 0, ',', '.') }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Pembeli (Buyer)
                    </p>

                    <p class="mt-1 font-bold text-slate-900 dark:text-white">
                        <i class="fa-solid fa-user text-slate-400 mr-1"></i> {{ $payment->order?->user?->username ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Waktu Transaksi
                    </p>

                    <p class="mt-1 font-bold text-slate-900 dark:text-white">
                        <i class="fa-regular fa-calendar text-slate-400 mr-1"></i> {{ $payment->created_at?->format('d M Y, H:i') ?? '-' }}
                    </p>
                </div>

            </div>

            @if($payment->proof_of_payment)

                <div class="mt-6 border-t border-slate-100 pt-6 dark:border-slate-800">

                    <h2 class="font-bold text-slate-900 dark:text-white text-sm">
                        Gambar Bukti Transaksi
                    </h2>

                    <div class="mt-3 overflow-hidden rounded-2xl border border-slate-200 p-2 dark:border-slate-700">

                        <img
                            src="{{ Storage::url($payment->proof_of_payment) }}"
                            alt="Bukti pembayaran"
                            class="max-h-120 w-full object-contain rounded-xl"
                        >

                    </div>

                </div>

            @endif

        </div>

    </div>
</x-layouts.seller>