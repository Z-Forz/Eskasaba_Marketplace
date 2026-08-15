<x-layouts.admin title="Detail Pembayaran">
    <div class="mx-auto max-w-4xl space-y-6">

        <div>
            <a
                href="{{ route('admin.payments.index') }}"
                class="inline-flex items-center gap-1 text-sm font-semibold text-slate-600 hover:text-slate-900 dark:text-gray-400 dark:hover:text-white"
            >
                ← Kembali ke List Pembayaran
            </a>

            <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Pembayaran #{{ $payment->id }}
                    </h1>

                    <p class="text-sm text-gray-500">
                        Pesanan #{{ $payment->order_id }}
                    </p>
                </div>

                <span class="rounded-full px-3.5 py-1 text-xs font-bold
                    {{ $payment->status === 'verified' || $payment->status === 'paid'
                        ? 'bg-green-100 text-green-800'
                        : ($payment->status === 'rejected'
                            ? 'bg-red-100 text-red-800'
                            : 'bg-yellow-100 text-yellow-800') }}"
                >
                    {{ ucfirst($payment->status) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            {{-- Payment Info --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-gray-700 dark:bg-gray-900">

                <h2 class="font-bold text-gray-900 dark:text-white">
                    Informasi Transaksi Pembayaran
                </h2>

                <div class="mt-5 space-y-4">

                    <div>
                        <p class="text-xs text-gray-500">Nominal Pembayaran</p>
                        <p class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white">
                            Rp {{ number_format($payment->amount ?? 0, 0, ',', '.') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Metode Pembayaran</p>
                        <p class="mt-1 font-semibold text-slate-800 dark:text-gray-200">
                            {{ strtoupper($payment->method ?? '-') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Waktu Transaksi</p>
                        <p class="mt-1 font-semibold text-slate-800 dark:text-gray-200">
                            {{ $payment->created_at?->format('d M Y H:i') }}
                        </p>
                    </div>

                </div>

            </div>

            {{-- Proof --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-gray-700 dark:bg-gray-900">

                <h2 class="font-bold text-gray-900 dark:text-white">
                    Bukti Pembayaran
                </h2>

                @if ($payment->proof_of_payment)
                    <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 dark:border-gray-700">
                        <img
                            src="{{ asset('storage/' . $payment->proof_of_payment) }}"
                            alt="Bukti pembayaran"
                            class="max-h-96 w-full object-contain"
                        >
                    </div>
                @else
                    <div class="mt-5 rounded-2xl bg-slate-50 p-8 text-center dark:bg-gray-800">
                        <p class="text-sm text-slate-500">
                            Belum ada bukti pembayaran diunggah.
                        </p>
                    </div>
                @endif

            </div>

        </div>

        {{-- Monitoring Readonly Notice --}}
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs text-slate-500 dark:text-gray-400">
                🔒 <strong>Mode Pemantauan Admin</strong>: Data pembayaran dan pesanan dipantau secara otomatis dari aktivitas seller dan buyer.
            </p>
        </div>

    </div>
</x-layouts.admin>