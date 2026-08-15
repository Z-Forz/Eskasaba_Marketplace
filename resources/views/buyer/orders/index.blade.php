<x-layouts.buyer title="Pesanan Saya">

    <div class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        <div class="mb-8">
            <p class="text-sm font-medium text-slate-500">
                Transaksi
            </p>

            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                Pesanan Saya
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Lihat status dan detail seluruh pesananmu.
            </p>
        </div>

        @if (session('success'))
            <x-alert
                type="success"
                :message="session('success')"
                class="mb-6"
            />
        @endif

        @if ($orders->count())

            <div class="space-y-4">

                @foreach ($orders as $order)

                    <x-order-card :order="$order" />

                @endforeach

            </div>

            @if ($orders->hasPages())
                <div class="mt-8">
                    {{ $orders->links() }}
                </div>
            @endif

        @else

            <x-empty-state
                title="Belum ada pesanan"
                message="Pesanan yang kamu lakukan akan muncul di halaman ini."
                action="{{ route('products.index') }}"
                actionText="Mulai Belanja"
            />

        @endif

    </div>

</x-layouts.buyer>