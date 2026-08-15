<x-layouts.admin>
    <div class="space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Pembayaran
            </h1>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Periksa pembayaran dan bukti pembayaran dari pembeli.
            </p>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">

                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">
                                Pembayaran
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">
                                Pesanan
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">
                                Jumlah
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">
                                Status
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase text-gray-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">

                        @forelse ($payments as $payment)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">

                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        #{{ $payment->id }}
                                    </p>

                                    <p class="text-xs text-gray-500">
                                        {{ $payment->created_at?->format('d M Y H:i') }}
                                    </p>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        #{{ $payment->order_id }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 font-semibold">
                                    Rp {{ number_format($payment->amount ?? 0, 0, ',', '.') }}
                                </td>

                                <td class="px-6 py-4">
                                    <x-badge :status="$payment->status" />
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <a
                                        href="{{ route('admin.payments.show', $payment) }}"
                                        class="text-sm font-semibold text-indigo-600 hover:text-indigo-800"
                                    >
                                        Detail
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <x-empty-state
                                        title="Belum ada pembayaran"
                                        description="Data pembayaran akan muncul di sini."
                                    />
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

        </div>

        @if ($payments->hasPages())
            {{ $payments->links() }}
        @endif

    </div>
</x-layouts.admin>