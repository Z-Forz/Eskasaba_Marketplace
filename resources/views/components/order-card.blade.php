@props([
    'order',
    'href' => null,
])

@php
    $href = $href ?? route('buyer.orders.show', $order);

    $statusClasses = [
        'pending'          => 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:ring-amber-800',
        'confirmed'        => 'bg-blue-50 text-blue-700 ring-blue-200 dark:bg-blue-950/50 dark:text-blue-300 dark:ring-blue-800',
        'processing'       => 'bg-indigo-50 text-indigo-700 ring-indigo-200 dark:bg-indigo-950/50 dark:text-indigo-300 dark:ring-indigo-800',
        'ready_for_pickup' => 'bg-purple-50 text-purple-700 ring-purple-200 dark:bg-purple-950/50 dark:text-purple-300 dark:ring-purple-800',
        'completed'        => 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:ring-emerald-800',
        'cancelled'        => 'bg-red-50 text-red-700 ring-red-200 dark:bg-red-950/50 dark:text-red-300 dark:ring-red-800',
    ];

    $statusLabels = [
        'pending'          => 'Menunggu',
        'confirmed'        => 'Dikonfirmasi',
        'processing'       => 'Diproses',
        'ready_for_pickup' => 'Siap Diambil',
        'completed'        => 'Selesai',
        'cancelled'        => 'Dibatalkan',
    ];

    $status = $order->status ?? 'pending';

    $statusClass = $statusClasses[$status] ?? 'bg-slate-50 text-slate-600 ring-slate-200';
    $statusLabel = $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status));

    $firstItem = $order->items?->first();
    $firstProductName = $firstItem?->product?->name ?? 'Produk Pesanan';
    $itemVariant = $firstItem?->variant_name ?: $firstItem?->note;
    $itemCount = $order->items?->count() ?? 1;

    $orderTitle = $itemCount > 1
        ? $firstProductName . ' + ' . ($itemCount - 1) . ' produk lainnya'
        : $firstProductName;
@endphp

<div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs transition hover:border-slate-300 dark:border-slate-800 dark:bg-slate-900 sm:p-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-2 py-0.5 text-[11px] font-extrabold text-emerald-800 border border-emerald-200/60 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-900/60">
                    <i class="fa-solid fa-receipt text-[9px]"></i> {{ $order->invoice_number ?? '#' . $order->id }}
                </span>
                @if(!empty($itemVariant))
                    <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                        <i class="fa-solid fa-layer-group text-[8px] text-emerald-600"></i> {{ $itemVariant }}
                    </span>
                @endif
                @if($order->created_at)
                    <span class="text-xs text-slate-400">
                        • {{ $order->created_at->format('d M Y, H:i') }}
                    </span>
                @endif
            </div>

            <h3 class="truncate text-base font-bold text-slate-900 dark:text-white sm:text-lg" title="{{ $orderTitle }}">
                {{ $orderTitle }}
            </h3>
        </div>

        <span class="w-fit rounded-full px-3 py-1 text-xs font-bold ring-1 {{ $statusClass }} shrink-0">
            {{ $statusLabel }}
        </span>

    </div>

    {{-- Location & Seller Info --}}
    <div class="mt-4 grid gap-3 border-t border-slate-100 pt-4 dark:border-slate-800 sm:grid-cols-2">

        @if($order->seller?->user)
            <div class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-900 text-xs font-bold text-white dark:bg-slate-700">
                    {{ strtoupper(substr($order->seller->user->username ?? 'S', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-medium uppercase tracking-wider text-slate-400">Penjual Toko</p>
                    <p class="truncate text-xs font-bold text-slate-800 dark:text-slate-200">
                        {{ $order->seller->user->username }}
                    </p>
                </div>
            </div>
        @endif

        @if($order->pickup_location)
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-location-dot text-emerald-600 text-sm"></i>
                <div class="min-w-0">
                    <p class="text-[11px] font-medium uppercase tracking-wider text-slate-400">Lokasi Pengambilan</p>
                    <p class="truncate text-xs font-semibold text-emerald-700 dark:text-emerald-400">
                        {{ $order->pickup_location }}
                    </p>
                </div>
            </div>
        @endif

    </div>

    {{-- Summary & Actions --}}
    <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4 dark:border-slate-800">

        <div>
            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                Total Tagihan
            </p>
            <p class="mt-0.5 text-base font-black text-slate-900 dark:text-white sm:text-lg">
                Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if(strtolower($order->payment?->method ?? '') === 'qris' && strtolower($order->payment?->status ?? 'pending') === 'pending' && $order->status !== 'cancelled' && $order->status !== 'completed')
                <a
                    href="{{ $href }}"
                    class="inline-flex items-center gap-1.5 rounded-2xl bg-amber-500 px-3.5 py-2 text-xs font-black text-white shadow-xs transition hover:bg-amber-600 sm:px-4"
                >
                    <i class="fa-solid fa-cloud-arrow-up"></i> Unggah Bukti Bayar
                </a>
            @endif

            <a
                href="{{ $href }}"
                class="inline-flex items-center gap-1.5 rounded-2xl bg-emerald-700 px-4 py-2.5 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-800 sm:px-5 sm:text-sm"
            >
                Lihat Detail Pesanan <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

    </div>

</div>