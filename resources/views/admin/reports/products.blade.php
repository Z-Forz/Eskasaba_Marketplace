<x-layouts.admin title="Laporan Produk">

    <div class="space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                📊 Laporan Produk Marketplace
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Ringkasan statistik produk, stok barang, dan kategori di Eskasaba Marketplace.
            </p>
        </div>

        {{-- Summary Cards --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-xs dark:border-gray-700 dark:bg-gray-900">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Produk</p>
                <p class="mt-2 text-3xl font-extrabold text-slate-900 dark:text-white">
                    {{ number_format($totalProducts ?? 0) }}
                </p>
                <p class="mt-1 text-xs text-slate-500">Produk terdaftar dalam katalog</p>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-xs dark:border-gray-700 dark:bg-gray-900">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Stok Habis</p>
                <p class="mt-2 text-3xl font-extrabold text-red-600 dark:text-red-400">
                    {{ number_format($outOfStockCount ?? 0) }}
                </p>
                <p class="mt-1 text-xs text-slate-500">Perlu restok ulang oleh seller</p>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-xs dark:border-gray-700 dark:bg-gray-900">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Kategori</p>
                <p class="mt-2 text-3xl font-extrabold text-emerald-700 dark:text-emerald-400">
                    {{ number_format($categories->count() ?? 0) }}
                </p>
                <p class="mt-1 text-xs text-slate-500">Kategori aktif terhubung</p>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-xs dark:border-gray-700 dark:bg-gray-900">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Status Katalog</p>
                <p class="mt-2 text-3xl font-extrabold text-blue-600 dark:text-blue-400">
                    Aktif
                </p>
                <p class="mt-1 text-xs text-slate-500">Sistem marketplace berjalan lancar</p>
            </div>
        </div>

        {{-- Categories Breakdown --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-gray-700 dark:bg-gray-900">
            <h2 class="text-base font-bold text-slate-900 dark:text-white">
                Sebaran Kategori Produk
            </h2>

            <div class="mt-4 flex flex-wrap gap-3">
                @foreach ($categories as $cat)
                    <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800">
                        <span class="text-sm font-semibold text-slate-800 dark:text-gray-200">{{ $cat->name }}</span>
                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                            {{ $cat->products_count }} Produk
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Products Table --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xs dark:border-gray-700 dark:bg-gray-900">
            <div class="p-6 border-b border-slate-100 dark:border-gray-800">
                <h2 class="text-base font-bold text-slate-900 dark:text-white">
                    Daftar Produk & Penjualan
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:border-gray-800 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-4">Nama Produk</th>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4">Seller / Penjual</th>
                            <th class="px-6 py-4">Harga</th>
                            <th class="px-6 py-4">Stok</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-gray-800">
                        @forelse ($products as $prod)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-gray-800/50">
                                <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                                    {{ $prod->name }}
                                </td>
                                <td class="px-6 py-4 text-xs font-medium text-slate-600 dark:text-gray-300">
                                    {{ $prod->category?->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-xs font-medium text-emerald-800 dark:text-emerald-400">
                                    {{ $prod->seller?->user?->username ?? '-' }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">
                                    Rp {{ number_format($prod->price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    <span class="rounded-full px-2.5 py-1 font-bold {{ $prod->stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $prod->stock }} Item
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-slate-500">
                                    Belum ada data produk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if (method_exists($products, 'links'))
            <div>
                {{ $products->links() }}
            </div>
        @endif

    </div>

</x-layouts.admin>
