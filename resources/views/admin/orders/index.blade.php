<x-layouts.admin title="Kelola Pesanan">
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-emerald-600"></i> Pemantauan Pesanan Marketplace
                </h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Pantau seluruh transaksi, invoice, item varian, dan status pesanan antara pembeli dan seller.
                </p>
            </div>
        </div>

        {{-- Status Filter Tabs Bar --}}
        <div class="flex flex-wrap gap-2 rounded-3xl border border-slate-200/80 bg-white p-2 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            @php
                $tabs = [
                    'all'              => ['label' => 'Semua Pesanan',       'color' => 'slate',   'icon' => 'fa-list-ul'],
                    'pending'          => ['label' => 'Menunggu Konfirmasi', 'color' => 'amber',   'icon' => 'fa-clock'],
                    'processing'       => ['label' => 'Diproses Seller',     'color' => 'blue',    'icon' => 'fa-fire-burner'],
                    'ready_for_pickup' => ['label' => 'Siap Diambil',        'color' => 'indigo',  'icon' => 'fa-box-open'],
                    'completed'        => ['label' => 'Selesai',             'color' => 'emerald', 'icon' => 'fa-circle-check'],
                    'cancelled'        => ['label' => 'Dibatalkan / Return', 'color' => 'red',     'icon' => 'fa-circle-xmark'],
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
                            'blue'    => 'bg-blue-600 text-white font-bold shadow-xs',
                            'indigo'  => 'bg-indigo-600 text-white font-bold shadow-xs',
                            'red'     => 'bg-red-600 text-white font-bold shadow-xs',
                            default   => 'bg-slate-900 text-white font-bold shadow-xs dark:bg-white dark:text-slate-900',
                        };
                    } else {
                        $activeClass = 'bg-slate-50 text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 font-semibold';
                    }
                @endphp

                <a
                    href="{{ route('admin.orders.index', array_merge(request()->except('page'), ['status' => $key])) }}"
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

        {{-- Search Card --}}
        <div class="rounded-3xl border border-slate-200/80 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <input type="hidden" name="status" value="{{ $status ?? 'all' }}">

                <div class="relative flex-1">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nomor invoice pesanan atau ID (contoh: INV/...)"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 pl-10 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    >
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button
                        type="submit"
                        class="rounded-2xl bg-emerald-700 px-5 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-800 flex items-center gap-1.5 shadow-xs"
                    >
                        <i class="fa-solid fa-magnifying-glass"></i> Cari
                    </button>
                    
                    @if(request('search'))
                        <a
                            href="{{ route('admin.orders.index', ['status' => $status ?? 'all']) }}"
                            class="rounded-2xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 flex items-center gap-1"
                        >
                            <i class="fa-solid fa-rotate-left"></i> Reset Cari
                        </a>
                    @endif
                </div>
            </form>
        </div>
        
        {{-- Table Container --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50/80 text-xs font-bold uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:bg-slate-800/80 dark:text-slate-400">
                        <tr>
                            <th class="px-6 py-4">Produk Pesanan</th>
                            <th class="px-6 py-4">Pembeli (Buyer)</th>
                            <th class="px-6 py-4">Penjual (Seller)</th>
                            <th class="px-6 py-4">Total Transaksi</th>
                            <th class="px-6 py-4">Status Pesanan</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($orders as $order)
                            <tr class="transition hover:bg-slate-50/60 dark:hover:bg-slate-800/50">

                                <td class="px-6 py-4">
                                    @php
                                        $firstItem = $order->items?->first();
                                        $pName = $firstItem?->product_name ?: ($firstItem?->product?->name ?: 'Produk Pesanan');
                                        $opt = $firstItem?->variant_name ?: $firstItem?->note;
                                        $qty = $firstItem?->quantity ?? 1;
                                        $subTitle = !empty($opt) ? "{$opt} / {$qty} Pcs" : "{$qty} Pcs";
                                        $moreCount = ($order->items?->count() ?? 1) - 1;
                                    @endphp

                                    <h4 class="font-black text-slate-900 dark:text-white text-base">
                                        {{ $pName }} @if($moreCount > 0) <span class="text-xs font-semibold text-slate-400">(+{{ $moreCount }} produk lain)</span> @endif
                                    </h4>

                                    <p class="mt-1 text-xs font-bold text-emerald-700 dark:text-emerald-400 flex items-center gap-1">
                                        <i class="fa-solid fa-layer-group text-[10px]"></i> {{ $subTitle }}
                                    </p>
                                </td>

                                <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-user-tag text-blue-600 text-xs"></i>
                                        <span>{{ $order->buyer?->username ?? $order->user?->username ?? '-' }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-bold text-emerald-800 dark:text-emerald-400">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-store text-emerald-600 text-xs"></i>
                                        <span>{{ $order->seller_name_with_status }}</span>
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 font-black text-emerald-700 dark:text-emerald-400 text-base">
                                    Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold whitespace-nowrap
                                        {{ match($order->status) {
                                            'completed'        => 'bg-green-100 text-green-700 dark:bg-green-950/40 dark:text-green-400',
                                            'cancelled'        => 'bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-400',
                                            'cancel_requested' => 'bg-orange-100 text-orange-700 dark:bg-orange-950/40 dark:text-orange-400',
                                            'ready_for_pickup' => 'bg-blue-100 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400',
                                            default            => 'bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400'
                                        } }}"
                                    >
                                        @if($order->status === 'completed')
                                            <i class="fa-solid fa-circle-check"></i> Selesai
                                        @elseif($order->status === 'cancelled')
                                            <i class="fa-solid fa-circle-xmark"></i> Dibatalkan
                                        @elseif($order->status === 'cancel_requested')
                                            <i class="fa-solid fa-clock-rotate-left"></i> Pengajuan Pembatalan
                                        @elseif($order->status === 'ready_for_pickup')
                                            <i class="fa-solid fa-box-open"></i> Siap Diambil
                                        @elseif($order->status === 'processing')
                                            <i class="fa-solid fa-fire-burner"></i> Diproses
                                        @else
                                            <i class="fa-solid fa-clock"></i> {{ ucfirst($order->status) }}
                                        @endif
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <a
                                        href="{{ route('admin.orders.show', array_merge(['order' => $order->id], request()->query())) }}"
                                        class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-3.5 py-2 text-xs font-bold text-white transition hover:bg-slate-800 dark:bg-emerald-700 dark:hover:bg-emerald-800 whitespace-nowrap shadow-xs"
                                    >
                                        <i class="fa-solid fa-eye"></i> Detail Pesanan
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">
                                    Belum ada data pesanan ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile List --}}
            <div class="divide-y divide-slate-100 md:hidden dark:divide-slate-800">
                @forelse ($orders as $order)
                    <div class="space-y-3 p-5">
                        @php
                            $firstItem = $order->items?->first();
                            $pName = $firstItem?->product_name ?: ($firstItem?->product?->name ?: 'Produk Pesanan');
                            $opt = $firstItem?->variant_name ?: $firstItem?->note;
                            $qty = $firstItem?->quantity ?? 1;
                            $subTitle = !empty($opt) ? "{$opt} / {$qty} Pcs" : "{$qty} Pcs";
                            $moreCount = ($order->items?->count() ?? 1) - 1;
                        @endphp

                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs text-slate-400">
                                <i class="fa-regular fa-calendar mr-1"></i> {{ $order->created_at?->format('d M Y H:i') }}
                            </span>
                            <span class="rounded-full px-3 py-1 text-xs font-bold bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-200">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>

                        <div>
                            <h4 class="font-extrabold text-slate-900 dark:text-white text-base">
                                {{ $pName }} @if($moreCount > 0) <span class="text-xs font-semibold text-slate-400">(+{{ $moreCount }} produk lain)</span> @endif
                            </h4>
                            <p class="text-xs font-bold text-emerald-700 dark:text-emerald-400 mt-0.5">
                                <i class="fa-solid fa-layer-group text-[10px]"></i> {{ $subTitle }}
                            </p>
                        </div>

                        <div class="space-y-1 text-xs text-slate-600 dark:text-slate-300 border-t border-slate-100 pt-2.5 dark:border-slate-800">
                            <p><span class="text-slate-400">Tanggal:</span> {{ $order->created_at?->format('d M Y H:i') }}</p>
                            <p><span class="text-slate-400">Pembeli:</span> {{ $order->buyer?->username ?? $order->user?->username ?? '-' }}</p>
                            <p><span class="text-slate-400">Seller:</span> {{ $order->seller_name_with_status }}</p>
                            <p class="font-black text-emerald-700 dark:text-emerald-400 text-sm">Total: Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}</p>
                        </div>

                        <a
                            href="{{ route('admin.orders.show', array_merge(['order' => $order->id], request()->query())) }}"
                            class="flex items-center justify-center gap-2 rounded-2xl bg-slate-900 py-2.5 text-xs font-bold text-white dark:bg-emerald-700"
                        >
                            <i class="fa-solid fa-eye"></i> Detail Pesanan
                        </a>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-slate-500">
                        Belum ada pesanan ditemukan.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Pagination --}}
        @if ($orders->hasPages())
            <div>
                {{ $orders->withQueryString()->links() }}
            </div>
        @endif

    </div>
</x-layouts.admin>