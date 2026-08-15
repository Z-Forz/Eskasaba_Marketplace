<x-layouts.admin>
    <div class="mx-auto max-w-4xl space-y-6">

        <div>
            <a
                href="{{ route('admin.payments.index') }}"
                class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
            >
                ← Kembali ke Pembayaran
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

                <x-badge :status="$payment->status" />
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            {{-- Payment Info --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">

                <h2 class="font-bold text-gray-900 dark:text-white">
                    Informasi Pembayaran
                </h2>

                <div class="mt-5 space-y-4">

                    <div>
                        <p class="text-xs text-gray-500">Jumlah</p>
                        <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">
                            Rp {{ number_format($payment->amount ?? 0, 0, ',', '.') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Metode</p>
                        <p class="mt-1 font-medium text-gray-900 dark:text-white">
                            {{ strtoupper($payment->method ?? '-') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Tanggal</p>
                        <p class="mt-1 font-medium text-gray-900 dark:text-white">
                            {{ $payment->created_at?->format('d M Y H:i') }}
                        </p>
                    </div>

                </div>

            </div>

            {{-- Proof --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">

                <h2 class="font-bold text-gray-900 dark:text-white">
                    Bukti Pembayaran
                </h2>

                @if ($payment->proof_of_payment)
                    <div class="mt-5 overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                        <img
                            src="{{ asset('storage/' . $payment->proof_of_payment) }}"
                            alt="Bukti pembayaran"
                            class="max-h-125 w-full object-contain"
                        >
                    </div>
                @else
                    <div class="mt-5 rounded-xl bg-gray-50 p-8 text-center dark:bg-gray-700/50">
                        <p class="text-sm text-gray-500">
                            Belum ada bukti pembayaran.
                        </p>
                    </div>
                @endif

            </div>

        </div>

        {{-- Update Status --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">

            <h2 class="font-bold text-gray-900 dark:text-white">
                Verifikasi Pembayaran
            </h2>

            <form
                action="{{ route('admin.payments.update', $payment) }}"
                method="POST"
                class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-end"
            >
                @csrf
                @method('PUT')

                <div class="flex-1">
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Status
                    </label>

                    <select
                        name="status"
                        class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >
                        <option value="pending" @selected($payment->status === 'pending')>
                            Pending
                        </option>

                        <option value="verified" @selected($payment->status === 'verified')>
                            Terverifikasi
                        </option>

                        <option value="rejected" @selected($payment->status === 'rejected')>
                            Ditolak
                        </option>
                    </select>
                </div>

                <button
                    type="submit"
                    class="rounded-xl bg-indigo-600 px-6 py-2.5 font-semibold text-white transition hover:bg-indigo-700"
                >
                    Simpan
                </button>

            </form>

        </div>

    </div>
</x-layouts.admin>