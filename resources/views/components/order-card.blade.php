@props([
    'order',
    'seller' => null,
])

@php
    $statusClasses = [
        'pending' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'paid' => 'bg-blue-50 text-blue-700 ring-blue-200',
        'processing' => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
        'ready' => 'bg-purple-50 text-purple-700 ring-purple-200',
        'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'cancelled' => 'bg-red-50 text-red-700 ring-red-200',
        'rejected' => 'bg-red-50 text-red-700 ring-red-200',
    ];

    $statusLabels = [
        'pending' => 'Menunggu',
        'paid' => 'Dibayar',
        'processing' => 'Diproses',
        'ready' => 'Siap Diambil',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
        'rejected' => 'Ditolak',
    ];

    $status = $order->status ?? 'pending';

    $statusClass = $statusClasses[$status] ?? 'bg-slate-50 text-slate-600 ring-slate-200';
    $statusLabel = $statusLabels[$status] ?? ucfirst($status);
@endphp

<div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

        <div class="min-w-0">

            <p class="text-xs font-medium text-slate-400">
                Pesanan
            </p>

            <h3 class="mt-1 truncate text-sm font-bold text-slate-900 sm:text-base">
                #{{ $order->order_number ?? $order->id }}
            </h3>

            @if($order->created_at)
                <p class="mt-1 text-xs text-slate-500">
                    {{ $order->created_at->format('d M Y, H:i') }}
                </p>
            @endif

        </div>

        <span class="w-fit rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $statusClass }}">
            {{ $statusLabel }}
        </span>

    </div>

    {{-- Seller --}}
    @if($order->seller?->user)

        <div class="mt-4 flex items-center gap-2 border-t border-slate-100 pt-4">

            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-700">
                {{ strtoupper(substr($order->seller->user->name, 0, 1)) }}
            </div>

            <div class="min-w-0">
                <p class="text-xs text-slate-400">
                    Penjual
                </p>

                <p class="truncate text-sm font-medium text-slate-700">
                    {{ $order->seller->user->name }}
                </p>
            </div>

        </div>

    @endif

    {{-- Summary --}}
    <div class="mt-4 flex items-end justify-between border-t border-slate-100 pt-4">

        <div>
            <p class="text-xs text-slate-400">
                Total
            </p>

            <p class="mt-1 text-base font-bold text-slate-900 sm:text-lg">
                Rp {{ number_format($order->total ?? 0, 0, ',', '.') }}
            </p>
        </div>

        @if(isset($href))
            <a
                href="{{ $href }}"
                class="rounded-xl bg-slate-900 px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-slate-800 sm:px-4 sm:text-sm"
            >
                Lihat Detail
            </a>
        @endif

    </div>

</div>