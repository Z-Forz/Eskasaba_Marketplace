<x-layouts.seller title="Dashboard Seller">

    <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <p class="text-sm font-medium text-slate-500">
                Seller Dashboard
            </p>

            <div class="mt-1 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                        Halo, {{ auth()->user()->name }} 👋
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Kelola produk, pesanan, pembayaran, dan aktivitas tokomu.
                    </p>
                </div>

                <a
                    href="{{ route('seller.products.create') }}"
                    class="inline-flex w-full items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 sm:w-auto"
                >
                    + Tambah Produk
                </a>
            </div>
        </div>


        {{-- Statistics --}}
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100">
                    📦
                </div>

                <p class="mt-5 text-sm text-slate-500">
                    Total Produk
                </p>

                <p class="mt-1 text-2xl font-bold text-slate-900">
                    {{ $totalProducts ?? 0 }}
                </p>
            </div>


            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50">
                    🛒
                </div>

                <p class="mt-5 text-sm text-slate-500">
                    Pesanan Baru
                </p>

                <p class="mt-1 text-2xl font-bold text-slate-900">
                    {{ $pendingOrders ?? 0 }}
                </p>
            </div>


            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50">
                    💳
                </div>

                <p class="mt-5 text-sm text-slate-500">
                    Pembayaran
                </p>

                <p class="mt-1 text-2xl font-bold text-slate-900">
                    {{ $pendingPayments ?? 0 }}
                </p>
            </div>


            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50">
                    ✓
                </div>

                <p class="mt-5 text-sm text-slate-500">
                    Pesanan Selesai
                </p>

                <p class="mt-1 text-2xl font-bold text-slate-900">
                    {{ $completedOrders ?? 0 }}
                </p>
            </div>

        </div>


        {{-- Main --}}
        <div class="mt-8 grid gap-6 lg:grid-cols-3">

            {{-- Recent Orders --}}
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm lg:col-span-2">

                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5 sm:px-6">

                    <div>
                        <h2 class="font-bold text-slate-900">
                            Pesanan Terbaru
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Pesanan terbaru dari produkmu.
                        </p>
                    </div>

                    <a
                        href="{{ route('seller.orders.index') }}"
                        class="text-sm font-semibold text-slate-700 hover:text-slate-900"
                    >
                        Lihat semua
                    </a>

                </div>

                @if (isset($recentOrders) && $recentOrders->count())

                    <div class="divide-y divide-slate-100">

                        @foreach ($recentOrders as $order)

                            <a
                                href="{{ route('seller.orders.show', $order) }}"
                                class="block p-5 transition hover:bg-slate-50 sm:p-6"
                            >

                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                                    <div>
                                        <p class="font-semibold text-slate-900">
                                            #{{ $order->order_number ?? $order->id }}
                                        </p>

                                        <p class="mt-1 text-sm text-slate-500">
                                            {{ $order->user->name ?? 'Buyer' }}
                                        </p>
                                    </div>

                                    <div class="flex items-center justify-between gap-4 sm:justify-end">

                                        <span class="text-sm font-bold text-slate-900">
                                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                        </span>

                                        <x-badge :type="$order->status">
                                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                        </x-badge>

                                    </div>

                                </div>

                            </a>

                        @endforeach

                    </div>

                @else

                    <div class="px-5 py-10 sm:px-6">

                        <x-empty-state
                            title="Belum ada pesanan"
                            message="Pesanan dari buyer akan muncul di sini."
                        />

                    </div>

                @endif

            </div>


            {{-- Quick Menu --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">

                <h2 class="font-bold text-slate-900">
                    Kelola Toko
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Akses cepat fitur seller.
                </p>

                <div class="mt-5 space-y-3">

                    <a
                        href="{{ route('seller.products.index') }}"
                        class="group flex items-center gap-4 rounded-2xl border border-slate-100 p-4 transition hover:bg-slate-50"
                    >
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100">
                            📦
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-900">
                                Produk
                            </p>

                            <p class="text-xs text-slate-500">
                                Kelola produkmu
                            </p>
                        </div>

                        <span class="text-slate-400">
                            →
                        </span>
                    </a>


                    <a
                        href="{{ route('seller.orders.index') }}"
                        class="group flex items-center gap-4 rounded-2xl border border-slate-100 p-4 transition hover:bg-slate-50"
                    >
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100">
                            🛒
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-900">
                                Pesanan
                            </p>

                            <p class="text-xs text-slate-500">
                                Kelola pesanan buyer
                            </p>
                        </div>

                        <span class="text-slate-400">
                            →
                        </span>
                    </a>


                    <a
                        href="{{ route('seller.payments.index') }}"
                        class="group flex items-center gap-4 rounded-2xl border border-slate-100 p-4 transition hover:bg-slate-50"
                    >
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100">
                            💳
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-900">
                                Pembayaran
                            </p>

                            <p class="text-xs text-slate-500">
                                Periksa pembayaran
                            </p>
                        </div>

                        <span class="text-slate-400">
                            →
                        </span>
                    </a>


                    <a
                        href="{{ route('seller.chats.index') }}"
                        class="group flex items-center gap-4 rounded-2xl border border-slate-100 p-4 transition hover:bg-slate-50"
                    >
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100">
                            💬
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-900">
                                Chat
                            </p>

                            <p class="text-xs text-slate-500">
                                Hubungi buyer
                            </p>
                        </div>

                        <span class="text-slate-400">
                            →
                        </span>
                    </a>

                </div>

            </div>

        </div>


        {{-- Products --}}
        @if (isset($recentProducts) && $recentProducts->count())

            <div class="mt-8">

                <div class="mb-5 flex items-end justify-between">

                    <div>
                        <h2 class="text-xl font-bold text-slate-900">
                            Produk Terbaru
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Produk yang baru kamu tambahkan.
                        </p>
                    </div>

                    <a
                        href="{{ route('seller.products.index') }}"
                        class="text-sm font-semibold text-slate-700 hover:text-slate-900"
                    >
                        Lihat semua
                    </a>

                </div>


                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">

                    @foreach ($recentProducts as $product)

                        <x-product-card :product="$product" />

                    @endforeach

                </div>

            </div>

        @endif

    </div>

</x-layouts.seller>