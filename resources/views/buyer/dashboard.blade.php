<x-layouts.buyer title="Dashboard">

    <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">

        {{-- =========================================================
             HEADER
        ========================================================== --}}
        <div class="mb-8">

            <p class="text-sm font-medium text-slate-500">
                Dashboard
            </p>

            <div class="mt-1 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">

                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                        Halo, {{ auth()->user()->name }} 👋
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Selamat datang kembali di Eskasaba Market.
                    </p>
                </div>

                <a
                    href="{{ route('products.index') }}"
                    class="inline-flex w-full items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 sm:w-auto"
                >
                    Mulai Belanja
                </a>

            </div>

        </div>


        {{-- =========================================================
             STATISTICS
        ========================================================== --}}
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">

            {{-- Total Orders --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100">
                        🛍️
                    </div>

                </div>

                <p class="mt-5 text-sm text-slate-500">
                    Total Pesanan
                </p>

                <p class="mt-1 text-2xl font-bold text-slate-900">
                    {{ $totalOrders ?? 0 }}
                </p>

            </div>


            {{-- Pending Orders --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50">
                        ⏳
                    </div>

                </div>

                <p class="mt-5 text-sm text-slate-500">
                    Menunggu
                </p>

                <p class="mt-1 text-2xl font-bold text-slate-900">
                    {{ $pendingOrders ?? 0 }}
                </p>

            </div>


            {{-- Completed Orders --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50">
                        ✓
                    </div>

                </div>

                <p class="mt-5 text-sm text-slate-500">
                    Selesai
                </p>

                <p class="mt-1 text-2xl font-bold text-slate-900">
                    {{ $completedOrders ?? 0 }}
                </p>

            </div>


            {{-- Cart --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50">
                        🛒
                    </div>

                </div>

                <p class="mt-5 text-sm text-slate-500">
                    Keranjang
                </p>

                <p class="mt-1 text-2xl font-bold text-slate-900">
                    {{ $cartCount ?? 0 }}
                </p>

            </div>

        </div>


        {{-- =========================================================
             MAIN CONTENT
        ========================================================== --}}
        <div class="mt-8 grid gap-6 lg:grid-cols-3">


            {{-- =====================================================
                 RECENT ORDERS
            ====================================================== --}}
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm lg:col-span-2">

                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5 sm:px-6">

                    <div>
                        <h2 class="font-bold text-slate-900">
                            Pesanan Terbaru
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Aktivitas transaksi terakhirmu.
                        </p>
                    </div>

                    <a
                        href="{{ route('buyer.orders.index') }}"
                        class="text-sm font-semibold text-slate-700 transition hover:text-slate-900"
                    >
                        Lihat semua
                    </a>

                </div>


                @if (isset($recentOrders) && $recentOrders->count())

                    <div class="divide-y divide-slate-100">

                        @foreach ($recentOrders as $order)

                            <x-order-card :order="$order" />

                        @endforeach

                    </div>

                @else

                    <div class="px-5 py-10 sm:px-6">

                        <x-empty-state
                            title="Belum ada pesanan"
                            message="Pesanan yang kamu lakukan akan muncul di sini."
                            action="{{ route('products.index') }}"
                            actionText="Mulai Belanja"
                        />

                    </div>

                @endif

            </div>


            {{-- =====================================================
                 QUICK MENU
            ====================================================== --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">

                <h2 class="font-bold text-slate-900">
                    Akses Cepat
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Menu yang sering kamu gunakan.
                </p>


                <div class="mt-5 space-y-3">

                    {{-- Products --}}
                    <a
                        href="{{ route('products.index') }}"
                        class="group flex items-center gap-4 rounded-2xl border border-slate-100 p-4 transition hover:border-slate-200 hover:bg-slate-50"
                    >

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100">
                            🛍️
                        </div>

                        <div class="min-w-0 flex-1">

                            <p class="font-semibold text-slate-900">
                                Belanja
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Lihat semua produk
                            </p>

                        </div>

                        <span class="text-slate-400 transition group-hover:translate-x-1">
                            →
                        </span>

                    </a>


                    {{-- Cart --}}
                    <a
                        href="{{ route('buyer.cart.index') }}"
                        class="group flex items-center gap-4 rounded-2xl border border-slate-100 p-4 transition hover:border-slate-200 hover:bg-slate-50"
                    >

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100">
                            🛒
                        </div>

                        <div class="min-w-0 flex-1">

                            <p class="font-semibold text-slate-900">
                                Keranjang
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $cartCount ?? 0 }} item
                            </p>

                        </div>

                        <span class="text-slate-400 transition group-hover:translate-x-1">
                            →
                        </span>

                    </a>


                    {{-- Orders --}}
                    <a
                        href="{{ route('buyer.orders.index') }}"
                        class="group flex items-center gap-4 rounded-2xl border border-slate-100 p-4 transition hover:border-slate-200 hover:bg-slate-50"
                    >

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100">
                            📦
                        </div>

                        <div class="min-w-0 flex-1">

                            <p class="font-semibold text-slate-900">
                                Pesanan
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Lihat riwayat pesanan
                            </p>

                        </div>

                        <span class="text-slate-400 transition group-hover:translate-x-1">
                            →
                        </span>

                    </a>


                    {{-- Chats --}}
                    <a
                        href="{{ route('buyer.chats.index') }}"
                        class="group flex items-center gap-4 rounded-2xl border border-slate-100 p-4 transition hover:border-slate-200 hover:bg-slate-50"
                    >

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100">
                            💬
                        </div>

                        <div class="min-w-0 flex-1">

                            <p class="font-semibold text-slate-900">
                                Chat
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Hubungi seller
                            </p>

                        </div>

                        <span class="text-slate-400 transition group-hover:translate-x-1">
                            →
                        </span>

                    </a>


                    {{-- Profile --}}
                    <a
                        href="{{ route('profile.index') }}"
                        class="group flex items-center gap-4 rounded-2xl border border-slate-100 p-4 transition hover:border-slate-200 hover:bg-slate-50"
                    >

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100">
                            👤
                        </div>

                        <div class="min-w-0 flex-1">

                            <p class="font-semibold text-slate-900">
                                Profil
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Kelola informasi akun
                            </p>

                        </div>

                        <span class="text-slate-400 transition group-hover:translate-x-1">
                            →
                        </span>

                    </a>

                </div>

            </div>

        </div>


        {{-- =========================================================
             RECENT CHATS
        ========================================================== --}}
        @if (isset($recentChats) && $recentChats->count())

            <div class="mt-8 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5 sm:px-6">

                    <div>
                        <h2 class="font-bold text-slate-900">
                            Percakapan Terbaru
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Chat terbaru dengan seller.
                        </p>
                    </div>

                    <a
                        href="{{ route('buyer.chats.index') }}"
                        class="text-sm font-semibold text-slate-700 hover:text-slate-900"
                    >
                        Lihat semua
                    </a>

                </div>


                <div class="divide-y divide-slate-100">

                    @foreach ($recentChats as $chat)

                        <a
                            href="{{ route('buyer.chats.show', $chat) }}"
                            class="flex gap-4 p-5 transition hover:bg-slate-50 sm:p-6"
                        >

                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-slate-100 font-bold text-slate-600">

                                {{ strtoupper(substr($chat->seller->user->name ?? 'S', 0, 1)) }}

                            </div>

                            <div class="min-w-0 flex-1">

                                <div class="flex items-start justify-between gap-3">

                                    <p class="truncate font-semibold text-slate-900">
                                        {{ $chat->seller->user->name ?? 'Seller' }}
                                    </p>

                                    @if ($chat->updated_at)
                                        <span class="shrink-0 text-xs text-slate-400">
                                            {{ $chat->updated_at->diffForHumans() }}
                                        </span>
                                    @endif

                                </div>

                                @if ($chat->product)
                                    <p class="mt-1 truncate text-xs text-slate-400">
                                        {{ $chat->product->name }}
                                    </p>
                                @endif

                                <p class="mt-1 truncate text-sm text-slate-500">
                                    {{ $chat->latestMessage->message ?? 'Belum ada pesan.' }}
                                </p>

                            </div>

                        </a>

                    @endforeach

                </div>

            </div>

        @endif


        {{-- =========================================================
             BECOME SELLER
        ========================================================== --}}
        @if (isset($canBecomeSeller) && $canBecomeSeller)

            <div class="mt-8 overflow-hidden rounded-3xl bg-slate-900 p-6 text-white sm:p-8">

                <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">

                    <div class="max-w-2xl">

                        <p class="text-sm font-medium text-slate-300">
                            Punya produk sendiri?
                        </p>

                        <h2 class="mt-1 text-xl font-bold sm:text-2xl">
                            Mulai berjualan di Eskasaba Market.
                        </h2>

                        <p class="mt-2 text-sm leading-6 text-slate-300">
                            Daftarkan dirimu sebagai seller dan mulai tawarkan produk kepada komunitas sekolah.
                        </p>

                    </div>

                    <a
                        href="{{ route('profile.index') }}"
                        class="inline-flex shrink-0 items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-100"
                    >
                        Daftar sebagai Seller
                    </a>

                </div>

            </div>

        @endif

    </div>

</x-layouts.buyer>
```
