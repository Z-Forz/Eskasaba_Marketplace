<x-layouts.buyer title="Pesanan Saya">

    <div class="mx-auto w-full max-w-6xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-6">
            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700">
                Riwayat Transaksi
            </p>

            <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-900 sm:text-3xl flex items-center gap-2">
                <i class="fa-solid fa-bag-shopping text-emerald-600"></i> Pesanan Belanja Saya
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Pantau status pengerjaan, lokasi titik temu COD, dan rincian pesanan Anda.
            </p>
        </div>

        @if (session('success'))
            <x-alert
                type="success"
                :message="session('success')"
                class="mb-6"
            />
        @endif

        {{-- Filter Tabs --}}
        <div class="mb-6 flex gap-2 overflow-x-auto pb-2 scrollbar-none">
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
                    href="{{ route('buyer.orders.index', array_filter(['status' => $val])) }}"
                    class="shrink-0 rounded-2xl px-4 py-2 text-xs font-bold transition {{ $currentStatus === $val || (is_null($currentStatus) && $val === '') ? 'bg-emerald-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                >
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Orders List --}}
        @if ($orders->count())

            <div class="space-y-5">

                @foreach ($orders as $order)

                    <x-order-card :order="$order" />

                @endforeach

            </div>

            @if ($orders->hasPages())
                <div class="mt-8">
                    {{ $orders->withQueryString()->links() }}
                </div>
            @endif

        @else

            <x-empty-state
                title="Pesanan tidak ditemukan"
                description="Belum ada pesanan pada kategori status yang Anda pilih."
                action="{{ route('products.index') }}"
                actionText="Mulai Belanja Produk Sekolah"
            />

        @endif

    </div>

</x-layouts.buyer>