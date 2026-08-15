<x-layouts.seller>
    <div class="mx-auto max-w-3xl space-y-6">

        <div>
            <a
                href="{{ route('seller.payments.index') }}"
                class="text-sm font-medium text-blue-600 hover:text-blue-700">
                ← Kembali
            </a>

            <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">
                Detail Pembayaran
            </h1>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <p class="text-sm text-gray-500">
                        Pembayaran Pesanan
                    </p>

                    <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">
                        #{{ $payment->order_id }}
                    </p>
                </div>

                <x-badge
                    :type="$payment->status"
                    :label="ucfirst(str_replace('_', ' ', $payment->status))"
                />

            </div>

            <div class="mt-6 grid gap-5 sm:grid-cols-2">

                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-400">
                        Metode Pembayaran
                    </p>

                    <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                        {{ strtoupper($payment->method ?? '-') }}
                    </p>
                </div>

                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-400">
                        Jumlah
                    </p>

                    <p class="mt-1 text-lg font-bold text-blue-600">
                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                    </p>
                </div>

                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-400">
                        Pembeli
                    </p>

                    <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                        {{ $payment->order?->user?->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-400">
                        Waktu Pembayaran
                    </p>

                    <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                        {{ $payment->paid_at?->format('d M Y, H:i') ?? '-' }}
                    </p>
                </div>

            </div>

            @if($payment->proof_of_payment)

                <div class="mt-6 border-t border-gray-100 pt-6 dark:border-gray-800">

                    <h2 class="font-bold text-gray-900 dark:text-white">
                        Bukti Pembayaran
                    </h2>

                    <div class="mt-4 overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700">

                        <img
                            src="{{ Storage::url($payment->proof_of_payment) }}"
                            alt="Bukti pembayaran"
                            class="max-h-150 w-full object-contain">

                    </div>

                </div>

            @endif

        </div>

    </div>
</x-layouts.seller>