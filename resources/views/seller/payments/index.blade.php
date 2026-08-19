<x-layouts.seller title="Riwayat Pembayaran Seller">
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-credit-card text-emerald-600"></i> Riwayat & Verifikasi Pembayaran
                </h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Pantau transaksi pembayaran COD maupun QRIS dari pembeli produk Anda.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <span class="rounded-full bg-emerald-100 px-3.5 py-1.5 text-xs font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                    Total {{ $payments->total() }} Pembayaran
                </span>
            </div>
        </div>

        @if (session('success'))
            <x-alert type="success" :message="session('success')" class="mb-4" />
        @endif

        @if($payments->count())

            <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900">

                <div class="overflow-x-auto">

                    <table class="min-w-full text-left text-sm">

                        <thead class="border-b border-slate-100 bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:bg-slate-800/50">

                            <tr>

                                <th class="px-6 py-4">
                                    Invoice / Pesanan
                                </th>

                                <th class="px-6 py-4">
                                    Pembeli
                                </th>

                                <th class="px-6 py-4">
                                    Metode
                                </th>

                                <th class="px-6 py-4">
                                    Jumlah Tagihan
                                </th>

                                <th class="px-6 py-4">
                                    Status Bayar
                                </th>

                                <th class="px-6 py-4 text-right">
                                    Aksi
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">

                            @foreach($payments as $payment)

                                <tr class="transition hover:bg-slate-50/80 dark:hover:bg-slate-800/50">

                                    <td class="whitespace-nowrap px-6 py-4 font-bold text-slate-900 dark:text-white">
                                        {{ $payment->order?->invoice_number ?? '#' . $payment->order_id }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 font-semibold text-slate-800 dark:text-slate-200">
                                        <i class="fa-solid fa-user text-slate-400 mr-1"></i> {{ $payment->order?->user?->username ?? '-' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-extrabold uppercase tracking-wide
                                            {{ strtolower($payment->method) === 'qris'
                                                ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300'
                                                : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}"
                                        >
                                            <i class="{{ strtolower($payment->method) === 'qris' ? 'fa-solid fa-qrcode' : 'fa-solid fa-money-bill-wave' }}"></i>
                                            {{ $payment->method === 'qris' ? 'QRIS' : 'COD' }}
                                        </span>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 font-black text-slate-900 dark:text-white">
                                        Rp {{ number_format($payment->amount ?? $payment->order?->total_price ?? 0, 0, ',', '.') }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="rounded-full px-3 py-1 text-xs font-bold
                                            {{ match($payment->status) {
                                                'verified'  => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300',
                                                'pending'   => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300',
                                                'rejected'  => 'bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-300',
                                                default     => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300'
                                            } }}"
                                        >
                                            {{ ucfirst($payment->status ?? 'pending') }}
                                        </span>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-right">

                                        <a
                                            href="{{ route('seller.payments.show', $payment) }}"
                                            class="inline-flex items-center gap-1 rounded-xl bg-slate-900 px-3.5 py-1.5 text-xs font-bold text-white transition hover:bg-slate-800 dark:bg-emerald-700 dark:hover:bg-emerald-800"
                                        >
                                            Detail <i class="fa-solid fa-arrow-right"></i>
                                        </a>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

            <div class="mt-6">
                {{ $payments->links() }}
            </div>

        @else

            <x-empty-state
                title="Belum ada transaksi pembayaran"
                description="Data pembayaran dari pesanan pembeli akan muncul secara otomatis di sini."
            />

        @endif

    </div>
</x-layouts.seller>