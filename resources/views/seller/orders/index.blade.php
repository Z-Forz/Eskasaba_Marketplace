<x-layouts.seller title="Kelola Pesanan Seller">
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-box text-emerald-600"></i> Kelola Pesanan Toko
                </h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Pantau pesanan masuk dari pembeli, konfirmasi pembayaran QRIS, dan perbarui titik lokasi COD.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <span class="rounded-full bg-emerald-100 px-3.5 py-1.5 text-xs font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                    Total {{ $orders->total() }} Pesanan
                </span>
            </div>
        </div>

        @if (session('success'))
            <x-alert type="success" :message="session('success')" class="mb-4" />
        @endif

        {{-- Status Filter Tabs --}}
        <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-none">
            @php
                $currentStatus = request('status');
                $statuses = [
                    ''                 => 'Semua Pesanan',
                    'pending'          => 'Menunggu',
                    'confirmed'        => 'Dikonfirmasi',
                    'processing'       => 'Diproses',
                    'ready_for_pickup' => 'Siap Diambil',
                    'completed'        => 'Selesai',
                    'cancelled'        => 'Dibatalkan',
                ];
            @endphp

            @foreach($statuses as $val => $label)
                <a
                    href="{{ route('seller.orders.index', array_filter(['status' => $val, 'search' => request('search')])) }}"
                    class="shrink-0 rounded-2xl px-4 py-2.5 text-xs font-bold transition {{ $currentStatus === $val || (is_null($currentStatus) && $val === '') ? 'bg-emerald-700 text-white shadow-xs' : 'bg-white border border-slate-200/80 text-slate-700 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800' }}"
                >
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Search Input --}}
        <div class="rounded-3xl border border-slate-200/80 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <form
                method="GET"
                action="{{ route('seller.orders.index') }}"
                class="flex items-center gap-3"
            >
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                <div class="relative flex-1">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nomor invoice pesanan (contoh: INV-...)"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 pl-10 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    >
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-xs text-slate-400"></i>
                </div>

                <button
                    type="submit"
                    class="rounded-2xl bg-emerald-700 px-5 py-2.5 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-800 flex items-center gap-1.5"
                >
                    <i class="fa-solid fa-magnifying-glass"></i> Cari Pesanan
                </button>

                @if(request()->hasAny(['status', 'search']))
                    <a
                        href="{{ route('seller.orders.index') }}"
                        class="rounded-2xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300"
                    >
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Orders List --}}
        @if($orders->count())

            <div class="space-y-4">

                @foreach($orders as $order)

                    <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs transition hover:border-slate-300 dark:border-slate-800 dark:bg-slate-900 sm:p-6">

                        {{-- Order Header --}}
                        <div class="flex flex-col gap-3 border-b border-slate-100 pb-4 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                    Nomor Invoice
                                </p>
                                <h2 class="mt-0.5 text-base font-bold text-slate-900 dark:text-white sm:text-lg">
                                    {{ $order->invoice_number ?? '#' . $order->id }}
                                </h2>
                                <p class="mt-0.5 text-xs text-slate-500">
                                    <i class="fa-regular fa-calendar mr-1"></i> {{ $order->created_at?->format('d M Y, H:i') }}
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="rounded-full px-3 py-1 text-xs font-bold
                                    {{ match($order->status) {
                                        'completed'        => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300',
                                        'pending'          => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300',
                                        'cancelled'        => 'bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-300',
                                        'ready_for_pickup' => 'bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300',
                                        default            => 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300'
                                    } }}"
                                >
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </div>
                        </div>

                        {{-- Buyer + Summary Grid --}}
                        <div class="grid gap-4 py-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                    Pembeli
                                </p>
                                <p class="mt-1 font-bold text-slate-900 dark:text-white text-sm">
                                    <i class="fa-solid fa-user mr-1 text-slate-500"></i> {{ $order->user?->username ?? '-' }}
                                </p>
                                @if($order->user?->email)
                                    <p class="mt-0.5 text-xs text-slate-500">
                                        {{ $order->user->email }}
                                    </p>
                                @endif
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                    Lokasi Pengambilan (COD)
                                </p>
                                <p class="mt-1 font-bold text-emerald-700 dark:text-emerald-400 text-sm">
                                    <i class="fa-solid fa-location-dot mr-1"></i> {{ $order->pickup_location ?? 'Belum ditentukan' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                    Total & Pembayaran
                                </p>
                                <p class="mt-1 text-lg font-black text-slate-900 dark:text-white">
                                    Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}
                                </p>
                                <p class="text-xs font-semibold text-slate-500">
                                    Metode: {{ strtoupper($order->payment?->method ?? 'COD') }} ({{ ucfirst($order->payment?->status ?? 'pending') }})
                                </p>
                            </div>
                        </div>

                        {{-- Items Preview --}}
                        @if($order->items->count())
                            <div class="space-y-2 border-t border-slate-100 pt-4 dark:border-slate-800">
                                @foreach($order->items->take(2) as $item)
                                    <div class="flex items-center justify-between gap-3 text-xs sm:text-sm">
                                        <div class="min-w-0">
                                            <p class="truncate font-bold text-slate-800 dark:text-slate-200">
                                                {{ $item->product_name ?? $item->product?->name }}
                                            </p>
                                            <p class="text-xs text-slate-400">
                                                {{ $item->quantity }} × Rp {{ number_format($item->price ?? 0, 0, ',', '.') }}
                                            </p>
                                        </div>
                                        <p class="shrink-0 font-extrabold text-slate-900 dark:text-white">
                                            Rp {{ number_format(($item->quantity * ($item->price ?? 0)), 0, ',', '.') }}
                                        </p>
                                    </div>
                                @endforeach

                                @if($order->items->count() > 2)
                                    <p class="pt-1 text-xs text-slate-400">
                                        + {{ $order->items->count() - 2 }} item lainnya
                                    </p>
                                @endif
                            </div>
                        @endif

                        {{-- Actions & Quick Buttons --}}
                        <div class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4 dark:border-slate-800">

                            {{-- Quick Action Button --}}
                            <div class="flex flex-wrap gap-2">
                                @if($order->status === 'pending')
                                    <form action="{{ route('seller.orders.update', $order) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="confirmed">
                                        <input type="hidden" name="payment_status" value="verified">
                                        <button type="submit" class="rounded-xl bg-emerald-700 px-3.5 py-1.5 text-xs font-bold text-white transition hover:bg-emerald-800 flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-check"></i> Konfirmasi & Terima
                                        </button>
                                    </form>
                                @elseif($order->status === 'confirmed')
                                    <form action="{{ route('seller.orders.update', $order) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="processing">
                                        <button type="submit" class="rounded-xl bg-blue-700 px-3.5 py-1.5 text-xs font-bold text-white transition hover:bg-blue-800 flex items-center gap-1.5">
                                            <i class="fa-solid fa-spinner"></i> Tandai Diproses
                                        </button>
                                    </form>
                                @elseif($order->status === 'processing')
                                    <form action="{{ route('seller.orders.update', $order) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="ready_for_pickup">
                                        <button type="submit" class="rounded-xl bg-purple-700 px-3.5 py-1.5 text-xs font-bold text-white transition hover:bg-purple-800 flex items-center gap-1.5">
                                            <i class="fa-solid fa-location-dot"></i> Siap Diambil
                                        </button>
                                    </form>
                                @elseif($order->status === 'ready_for_pickup')
                                    <form action="{{ route('seller.orders.update', $order) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="rounded-xl bg-emerald-700 px-3.5 py-1.5 text-xs font-bold text-white transition hover:bg-emerald-800 flex items-center gap-1.5">
                                            <i class="fa-solid fa-flag-checkered"></i> Selesaikan Pesanan
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <a
                                href="{{ route('seller.orders.show', $order) }}"
                                class="inline-flex items-center gap-1.5 rounded-2xl bg-slate-900 px-5 py-2 text-xs font-bold text-white transition hover:bg-slate-800 dark:bg-emerald-700 dark:hover:bg-emerald-800"
                            >
                                Detail & Kelola Status →
                            </a>
                        </div>

                    </div>

                @endforeach

            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $orders->withQueryString()->links() }}
            </div>

        @else

            <x-empty-state
                title="Belum ada pesanan"
                description="Pesanan dari pembeli untuk produk jualanmu akan muncul di halaman ini."
            />

        @endif

    </div>
</x-layouts.seller>