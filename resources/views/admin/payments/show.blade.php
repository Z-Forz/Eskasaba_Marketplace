<x-layouts.admin title="Detail Pembayaran">
    <div class="mx-auto max-w-4xl space-y-6">

        <div>
            <a
                href="{{ route('admin.payments.index') }}"
                class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
            >
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Pembayaran
            </a>

            <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-credit-card text-emerald-600"></i> Detail Pembayaran #PAY-{{ $payment->id }}
                    </h1>

                    <p class="text-sm text-slate-500 flex items-center gap-1.5 mt-1">
                        <i class="fa-solid fa-receipt text-emerald-600"></i> Pesanan Invoice: <strong>#{{ $payment->order?->invoice_number ?? $payment->order_id }}</strong>
                    </p>
                </div>

                <span class="rounded-full px-4 py-1.5 text-xs font-bold
                    {{ in_array($payment->status, ['verified', 'paid'])
                        ? 'bg-green-100 text-green-700 dark:bg-green-950/40 dark:text-green-400'
                        : ($payment->status === 'rejected'
                            ? 'bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-400'
                            : 'bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400') }}"
                >
                    @if(in_array($payment->status, ['verified', 'paid']))
                        <i class="fa-solid fa-check mr-1"></i> Terverifikasi / Lunas
                    @elseif($payment->status === 'rejected')
                        <i class="fa-solid fa-xmark mr-1"></i> Ditolak
                    @else
                        <i class="fa-solid fa-clock mr-1"></i> Menunggu Verifikasi
                    @endif
                </span>
            </div>
        </div>

        @if (session('success'))
            <x-alert type="success" :message="session('success')" class="mb-4" />
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            {{-- Payment Info --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">

                <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice-dollar text-emerald-600"></i> Informasi Transaksi
                </h2>

                <div class="mt-5 space-y-4">

                    <div>
                        <p class="text-xs font-bold text-slate-400">Total Nominal Pembayaran</p>
                        <p class="mt-1 text-2xl font-black text-emerald-700 dark:text-emerald-400">
                            Rp {{ number_format($payment->amount ?? $payment->order?->total_price ?? 0, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-bold text-slate-400">Metode Pembayaran</p>
                            <p class="mt-1 font-bold text-slate-800 dark:text-slate-200 uppercase flex items-center gap-1">
                                @if(strtoupper($payment->method ?? '') === 'QRIS')
                                    <i class="fa-solid fa-qrcode text-emerald-600"></i> QRIS
                                @else
                                    <i class="fa-solid fa-money-bill-wave text-amber-600"></i> Tunai / COD
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-bold text-slate-400">Waktu Transaksi</p>
                            <p class="mt-1 font-semibold text-slate-800 dark:text-slate-200 text-xs">
                                <i class="fa-solid fa-clock text-slate-400 mr-1"></i> {{ $payment->created_at?->format('d M Y H:i') }}
                            </p>
                        </div>
                    </div>

                    @if($payment->order)
                        <div class="border-t border-slate-100 pt-4 dark:border-slate-800">
                            <p class="text-xs font-bold text-slate-400">Informasi Pembeli & Toko</p>
                            <div class="mt-2 space-y-1.5 text-xs font-semibold text-slate-700 dark:text-slate-300">
                                <p><i class="fa-solid fa-user text-slate-400 mr-1"></i> Pembeli: <strong>{{ $payment->order->user?->username }}</strong></p>
                                <p><i class="fa-solid fa-store text-slate-400 mr-1"></i> Toko Penjual: <strong>{{ $payment->order->seller?->user?->username }}</strong></p>
                            </div>
                        </div>
                    @endif

                </div>

            </div>

            {{-- Proof of Payment --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">

                <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-image text-emerald-600"></i> Bukti Transfer Pembayaran
                </h2>

                @if ($payment->proof_of_payment)
                    <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 p-2 dark:bg-slate-800">
                        <img
                            src="{{ asset('storage/' . $payment->proof_of_payment) }}"
                            alt="Bukti pembayaran"
                            class="max-h-72 w-full object-contain rounded-xl"
                        >
                        <div class="mt-3 text-center">
                            <a
                                href="{{ asset('storage/' . $payment->proof_of_payment) }}"
                                target="_blank"
                                class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 hover:underline dark:text-emerald-400"
                            >
                                <i class="fa-solid fa-magnifying-glass-plus"></i> Lihat Ukuran Asli
                            </a>
                        </div>
                    </div>
                @else
                    <div class="mt-5 rounded-2xl bg-slate-50 p-8 text-center dark:bg-slate-800/50">
                        <i class="fa-solid fa-receipt text-3xl text-slate-300 mb-2"></i>
                        <p class="text-xs font-semibold text-slate-500">
                            Pembayaran dengan metode Tunai / COD atau belum ada bukti transfer diunggah.
                        </p>
                    </div>
                @endif

            </div>

        </div>

        {{-- Verification Status Action Card --}}
        <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
                <i class="fa-solid fa-gavel text-emerald-600"></i> Aksi Konfirmasi Status Pembayaran
            </h2>

            <div class="flex flex-wrap items-center gap-3">
                <form action="{{ route('admin.payments.update', $payment) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="verified">
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-2xl bg-emerald-700 px-5 py-3 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-800"
                    >
                        <i class="fa-solid fa-circle-check"></i> Verifikasi Pembayaran (Setujui Lunas)
                    </button>
                </form>

                <form action="{{ route('admin.payments.update', $payment) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="rejected">
                    <button
                        type="submit"
                        onclick="return confirm('Yakin ingin menolak bukti pembayaran ini?')"
                        class="inline-flex items-center gap-2 rounded-2xl bg-red-600 px-5 py-3 text-xs font-bold text-white shadow-xs transition hover:bg-red-700"
                    >
                        <i class="fa-solid fa-circle-xmark"></i> Tolak Pembayaran
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-layouts.admin>