<x-layouts.admin title="Kelola Pembayaran">
    <div class="space-y-6">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-credit-card text-emerald-600"></i> Kelola Transaksi Pembayaran
                </h1>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Pantau dan kelola bukti verifikasi pembayaran QRIS dan Cash dari pembeli.
                </p>
            </div>
        </div>

        @if (session('success'))
            <x-alert type="success" :message="session('success')" class="mb-4" />
        @endif

        {{-- Status Filter Tabs Bar --}}
        <div class="flex flex-wrap gap-2 rounded-3xl border border-slate-200/80 bg-white p-2 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            @php
                $tabs = [
                    'all'      => ['label' => 'Semua Transaksi',    'color' => 'slate',   'icon' => 'fa-list-ul'],
                    'pending'  => ['label' => 'Menunggu Konfirmasi','color' => 'amber',   'icon' => 'fa-clock'],
                    'verified' => ['label' => 'Terverifikasi / Lunas','color' => 'emerald','icon' => 'fa-circle-check'],
                    'rejected' => ['label' => 'Ditolak / Gagal',    'color' => 'red',     'icon' => 'fa-circle-xmark'],
                ];
            @endphp

            @foreach ($tabs as $key => $tab)
                @php
                    $isActive = ($status ?? 'all') === $key;
                    $count = $counts[$key] ?? 0;
                    
                    if ($isActive) {
                        $activeClass = match ($tab['color']) {
                            'emerald' => 'bg-emerald-700 text-white font-bold shadow-xs',
                            'amber'   => 'bg-amber-600 text-white font-bold shadow-xs',
                            'red'     => 'bg-red-600 text-white font-bold shadow-xs',
                            default   => 'bg-slate-900 text-white font-bold shadow-xs dark:bg-white dark:text-slate-900',
                        };
                    } else {
                        $activeClass = 'bg-slate-50 text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 font-semibold';
                    }
                @endphp

                <a
                    href="{{ route('admin.payments.index', ['status' => $key]) }}"
                    class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 text-xs transition {{ $activeClass }}"
                >
                    <i class="fa-solid {{ $tab['icon'] }} text-xs"></i>
                    <span>{{ $tab['label'] }}</span>

                    <span class="rounded-full px-2 py-0.5 text-[11px] font-extrabold {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300' }}">
                        {{ number_format($count) }}
                    </span>
                </a>
            @endforeach
        </div>

        {{-- Payments Table --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900">

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">

                    <thead class="border-b border-slate-100 bg-slate-50/80 text-xs font-bold uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:bg-slate-800/80 dark:text-slate-400">
                        <tr>
                            <th class="px-6 py-4">ID Transaksi & Waktu</th>
                            <th class="px-6 py-4">Pesanan & Pembeli</th>
                            <th class="px-6 py-4">Metode Pembayaran</th>
                            <th class="px-6 py-4">Total Nominal</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">

                        @forelse ($payments as $payment)
                            <tr class="transition hover:bg-slate-50/60 dark:hover:bg-slate-800/50">

                                <td class="px-6 py-4">
                                    <p class="font-black text-slate-900 dark:text-white">
                                     PAY-{{ $payment->id }}
                                    </p>

                                    <p class="text-xs text-slate-400 flex items-center gap-1 mt-0.5">
                                        <i class="fa-solid fa-clock text-[10px]"></i> {{ $payment->created_at?->format('d M Y H:i') }}
                                    </p>
                                </td>

                                <td class="px-6 py-4">
                                    <p class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                        <i class="fa-solid fa-receipt text-emerald-600 text-xs"></i> Invoice {{ $payment->order?->invoice_number ?? $payment->order_id }}
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-0.5">
                                        <i class="fa-solid fa-user text-[10px]"></i> {{ $payment->order?->user?->username ?? 'Pembeli' }}
                                    </p>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 rounded-xl bg-slate-100 px-3 py-1 text-xs font-bold text-slate-800 dark:bg-slate-800 dark:text-slate-200">
                                        @if(strtoupper($payment->method ?? '') === 'QRIS')
                                            <i class="fa-solid fa-qrcode text-emerald-600"></i> QRIS
                                        @else
                                            <i class="fa-solid fa-money-bill-wave text-amber-600"></i> Tunai / COD
                                        @endif
                                    </span>
                                </td>

                                <td class="px-6 py-4 font-black text-emerald-700 dark:text-emerald-400 text-base">
                                    Rp {{ number_format($payment->amount ?? $payment->order?->total_price ?? 0, 0, ',', '.') }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-bold
                                        {{ in_array($payment->status, ['verified', 'paid'])
                                            ? 'bg-green-100 text-green-700 dark:bg-green-950/40 dark:text-green-400'
                                            : ($payment->status === 'rejected'
                                                ? 'bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-400'
                                                : 'bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400') }}"
                                    >
                                        @if(in_array($payment->status, ['verified', 'paid']))
                                            <i class="fa-solid fa-check mr-1"></i> Terverifikasi
                                        @elseif($payment->status === 'rejected')
                                            <i class="fa-solid fa-xmark mr-1"></i> Ditolak
                                        @else
                                            <i class="fa-solid fa-clock mr-1"></i> Menunggu Konfirmasi
                                        @endif
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <a
                                        href="{{ route('admin.payments.show', $payment) }}"
                                        class="inline-flex items-center gap-1.5 rounded-2xl bg-slate-900 px-4 py-2 text-xs font-bold text-white transition hover:bg-slate-800 dark:bg-emerald-700 dark:hover:bg-emerald-800"
                                    >
                                        <i class="fa-solid fa-eye"></i> Detail & Bukti
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">
                                    Belum ada data pembayaran dalam kategori ini.
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