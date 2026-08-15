{{-- resources/views/seller/payments/index.blade.php --}}
<x-layouts.seller>
    <div class="container">
        <h1>Payments</h1>
    </div>
</x-layouts.seller>
<x-layouts.seller>
    <div class="space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Pembayaran
            </h1>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Lihat pembayaran yang berkaitan dengan pesanan kamu.
            </p>
        </div>

        @if($payments->count())

            <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">

                <div class="overflow-x-auto">

                    <table class="min-w-full text-left text-sm">

                        <thead class="border-b border-gray-100 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">

                            <tr>

                                <th class="px-5 py-4 font-semibold text-gray-600 dark:text-gray-300">
                                    Pesanan
                                </th>

                                <th class="px-5 py-4 font-semibold text-gray-600 dark:text-gray-300">
                                    Pembeli
                                </th>

                                <th class="px-5 py-4 font-semibold text-gray-600 dark:text-gray-300">
                                    Metode
                                </th>

                                <th class="px-5 py-4 font-semibold text-gray-600 dark:text-gray-300">
                                    Jumlah
                                </th>

                                <th class="px-5 py-4 font-semibold text-gray-600 dark:text-gray-300">
                                    Status
                                </th>

                                <th class="px-5 py-4 text-right font-semibold text-gray-600 dark:text-gray-300">
                                    Aksi
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">

                            @foreach($payments as $payment)

                                <tr class="transition hover:bg-gray-50 dark:hover:bg-gray-800/50">

                                    <td class="px-5 py-4 font-semibold text-gray-900 dark:text-white">
                                        #{{ $payment->order_id }}
                                    </td>

                                    <td class="px-5 py-4 text-gray-600 dark:text-gray-300">
                                        {{ $payment->order?->user?->name ?? '-' }}
                                    </td>

                                    <td class="px-5 py-4 text-gray-600 dark:text-gray-300">
                                        {{ strtoupper($payment->method ?? '-') }}
                                    </td>

                                    <td class="px-5 py-4 font-semibold text-gray-900 dark:text-white">
                                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                    </td>

                                    <td class="px-5 py-4">

                                        <x-badge
                                            :type="$payment->status"
                                            :label="ucfirst(str_replace('_', ' ', $payment->status))"
                                        />

                                    </td>

                                    <td class="px-5 py-4 text-right">

                                        <a
                                            href="{{ route('seller.payments.show', $payment) }}"
                                            class="font-semibold text-blue-600 hover:text-blue-700">
                                            Detail
                                        </a>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

            {{ $payments->links() }}

        @else

            <x-empty-state
                title="Belum ada pembayaran"
                description="Data pembayaran akan muncul ketika pembeli melakukan pembayaran."
            />

        @endif

    </div>
</x-layouts.seller>