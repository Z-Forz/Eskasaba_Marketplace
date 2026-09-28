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

        {{-- Filter Dropdown Bar --}}
        <div class="mb-6 rounded-3xl border border-slate-200/80 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <form method="GET" action="{{ route('buyer.orders.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <div class="w-full sm:w-80">
                    <x-custom-select
                        name="status"
                        :options="[
                            ''                                  => 'Semua Status Pesanan',
                            'pending'                           => 'Menunggu Konfirmasi',
                            'confirmed'                         => 'Dikonfirmasi',
                            'processing'                        => 'Sedang Diproses',
                            'ready_for_pickup'                  => 'Siap Diambil',
                            'completed'                         => 'Pesanan Selesai',
                            'cancel_requested'                  => 'Pengajuan Pembatalan',
                            'return_requested'                  => 'Pengajuan Return',
                            'refund_pending_buyer_confirmation' => 'Menunggu Refund',
                            'cancelled'                         => 'Dibatalkan',
                            'returned'                          => 'Return Berhasil',
                        ]"
                        :selected="request('status')"
                        placeholder=""
                        :submitOnSelect="true"
                    />
                </div>
                @if(request('status'))
                    <a
                        href="{{ route('buyer.orders.index') }}"
                        class="rounded-2xl border border-slate-200 px-4 py-3 text-xs font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 text-center whitespace-nowrap"
                    >
                        Reset Filter
                    </a>
                @endif
            </form>
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