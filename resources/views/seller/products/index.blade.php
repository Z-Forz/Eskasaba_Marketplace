<x-layouts.seller title="Katalog Produk Toko">

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-box-archive text-emerald-600"></i> Katalog Produk Toko
                </h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Kelola seluruh produk jualan Anda, atur ketersediaan stok, dan ubah varian produk.
                </p>
            </div>

            <a
                href="{{ route('seller.products.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-5 py-3 text-sm font-bold text-white shadow-xs transition hover:bg-emerald-800"
            >
                <i class="fa-solid fa-plus"></i> Tambah Produk Baru
            </a>
        </div>

        {{-- Filter & Search Form --}}
        <div class="rounded-3xl border border-slate-200/80 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <form
                method="GET"
                action="{{ route('seller.products.index') }}"
                class="grid gap-3 md:grid-cols-[1fr_auto_auto]"
            >
                <div class="relative">
                    <input
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama produk jualan..."
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 pl-10 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    >
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-xs text-slate-400"></i>
                </div>

                <div class="w-full md:w-44">
                    <x-custom-select
                        name="status"
                        :options="['' => 'Semua Status', 'active' => 'Aktif', 'inactive' => 'Nonaktif']"
                        :selected="request('status')"
                        placeholder=""
                    />
                </div>

                <button
                    type="submit"
                    class="rounded-2xl bg-emerald-700 px-5 py-2.5 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-800 flex items-center gap-1.5 justify-center"
                >
                    <i class="fa-solid fa-magnifying-glass"></i> Cari
                </button>
            </form>
        </div>

        {{-- Products Section: Responsive Table for Desktop (md+) & Card Grid for Mobile (<md) --}}
        @if ($products->count())

            {{-- 1. DESKTOP VIEW: Sleek Modern Table (Visible on md and up) --}}
            <div class="hidden md:block overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/80 text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-400">
                                <th scope="col" class="py-4 px-6">Produk</th>
                                <th scope="col" class="py-4 px-4">Kategori</th>
                                <th scope="col" class="py-4 px-4">Varian & Stok</th>
                                <th scope="col" class="py-4 px-4">Harga Jual</th>
                                <th scope="col" class="py-4 px-4">Status</th>
                                <th scope="col" class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                            @foreach ($products as $product)
                                @php
                                    $firstImage = $product->images->first()?->image;
                                    $hasDiscount = !empty($product->discount) && $product->discount > 0;
                                    $stock = (int) ($product->stock ?? 0);
                                    $isOutofStock = $stock <= 0;
                                    $isActive = $product->status === 'active';
                                @endphp
                                <tr class="group hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                    {{-- Produk (Foto + Nama) --}}
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3.5">
                                            <div class="h-14 w-14 shrink-0 overflow-hidden rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700/60">
                                                @if ($firstImage)
                                                    <img
                                                        src="{{ Storage::url($firstImage) }}"
                                                        alt="{{ $product->name }}"
                                                        class="h-full w-full object-cover transition-transform group-hover:scale-105"
                                                    >
                                                @else
                                                    <div class="flex h-full w-full flex-col items-center justify-center text-slate-400">
                                                        <i class="fa-solid fa-box text-lg opacity-40 text-emerald-600"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <a href="{{ route('seller.products.edit', $product) }}" class="font-bold text-slate-900 group-hover:text-emerald-700 dark:text-white dark:group-hover:text-emerald-400 transition line-clamp-1">
                                                    {{ $product->name }}
                                                </a>
                                                <p class="text-xs text-slate-400 mt-0.5">
                                                    ID: #{{ $product->id }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kategori --}}
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        @if($product->category)
                                            <span class="inline-flex items-center gap-1 rounded-xl bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700">
                                                <i class="fa-solid fa-layer-group text-[10px] text-emerald-600"></i>
                                                {{ $product->category->name }}
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-400 italic">-</span>
                                        @endif
                                    </td>

                                    {{-- Varian & Stok --}}
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <div class="flex flex-col gap-1 items-start">
                                            @if(!empty($product->variants) && count($product->variants) > 0)
                                                <span class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-900/60">
                                                    <i class="fa-solid fa-layer-group text-[10px]"></i> {{ count($product->variants) }} Varian
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                                    Harga Tunggal
                                                </span>
                                            @endif

                                            @if($isOutofStock)
                                                <span class="inline-flex items-center gap-1 rounded-lg bg-red-50 px-2.5 py-0.5 text-xs font-bold text-red-700 dark:bg-red-950/60 dark:text-red-400 border border-red-200/60 dark:border-red-900/60">
                                                    <i class="fa-solid fa-circle-exclamation text-[10px]"></i> Stok Habis
                                                </span>
                                            @else
                                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                                    Stok: {{ $stock }} Pcs
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Harga Jual --}}
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <div>
                                            <p class="font-extrabold text-emerald-700 dark:text-emerald-400">
                                                Rp {{ number_format($product->price, 0, ',', '.') }}
                                            </p>
                                            @if($hasDiscount)
                                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-red-600 dark:text-red-400">
                                                    <i class="fa-solid fa-tag text-[9px]"></i> Disc Rp {{ number_format($product->discount, 0, ',', '.') }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-extrabold border {{ $isActive ? 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/80 dark:text-emerald-300 dark:border-emerald-900' : 'bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700' }}">
                                            <span class="h-2 w-2 rounded-full {{ $isActive ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                            {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            <a
                                                href="{{ route('seller.products.edit', $product) }}"
                                                class="inline-flex items-center justify-center gap-1 rounded-xl bg-emerald-700 px-3.5 py-1.5 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-800"
                                            >
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </a>

                                            <a
                                                href="{{ route('products.show', $product) }}"
                                                target="_blank"
                                                class="inline-flex items-center justify-center h-8 w-8 rounded-xl border border-slate-200 bg-white text-slate-600 shadow-xs transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                                                title="Pratinjau di Toko"
                                            >
                                                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                            </a>

                                            <form
                                                id="delete-product-table-{{ $product->id }}"
                                                action="{{ route('seller.products.destroy', $product) }}"
                                                method="POST"
                                                class="inline"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="button"
                                                    onclick="confirmAction({ title: 'Hapus Produk', message: 'Apakah Anda yakin ingin menghapus produk {{ addslashes($product->name) }}?', form: 'delete-product-table-{{ $product->id }}', variant: 'danger', confirmText: 'Ya, Hapus' })"
                                                    class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-red-50 text-red-600 transition hover:bg-red-100 dark:bg-red-950/40 dark:text-red-400 cursor-pointer"
                                                    title="Hapus Produk"
                                                >
                                                    <i class="fa-solid fa-trash text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 2. MOBILE VIEW: Responsive Card Grid (Visible on < md) --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 md:hidden">

                @foreach ($products as $product)

                    @php
                        $firstImage = $product->images->first()?->image;
                        $hasDiscount = !empty($product->discount) && $product->discount > 0;
                        $stock = (int) ($product->stock ?? 0);
                        $isOutofStock = $stock <= 0;
                        $isActive = $product->status === 'active';
                    @endphp

                    <div class="group relative flex flex-col overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-xs transition-all duration-300 hover:-translate-y-1 hover:border-emerald-500/50 hover:shadow-xl hover:shadow-emerald-950/5 dark:border-slate-800 dark:bg-slate-900">

                        {{-- Image Header --}}
                        <div class="relative aspect-4/3 w-full overflow-hidden bg-slate-100 dark:bg-slate-800">
                            @if ($firstImage)
                                <img
                                    src="{{ Storage::url($firstImage) }}"
                                    alt="{{ $product->name }}"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                >
                            @else
                                <div class="flex h-full w-full flex-col items-center justify-center text-slate-400">
                                    <i class="fa-solid fa-box text-3xl opacity-40 text-emerald-600"></i>
                                    <span class="mt-1 text-xs font-semibold text-slate-400">Foto Belum Tersedia</span>
                                </div>
                            @endif

                            {{-- Status Badge (Top Left) --}}
                            <div class="absolute left-3 top-3 z-10">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-black uppercase tracking-wider shadow-xs backdrop-blur-md {{ $isActive ? 'bg-emerald-950/85 text-emerald-300 border border-emerald-500/30' : 'bg-slate-900/85 text-slate-300 border border-slate-700' }}">
                                    <span class="h-2 w-2 rounded-full {{ $isActive ? 'bg-emerald-400' : 'bg-slate-400' }}"></span>
                                    {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>

                            {{-- Category Badge (Top Right) --}}
                            @if($product->category)
                                <div class="absolute right-3 top-3 z-10">
                                    <span class="inline-flex items-center gap-1 rounded-xl bg-slate-900/80 px-2.5 py-1 text-[11px] font-bold text-white shadow-xs backdrop-blur-md border border-white/20">
                                        <i class="fa-solid fa-layer-group text-[9px] text-emerald-400"></i>
                                        {{ $product->category->name }}
                                    </span>
                                </div>
                            @endif

                            {{-- Discount Tag --}}
                            @if($hasDiscount)
                                <div class="absolute left-3 bottom-3 z-10">
                                    <span class="inline-flex items-center gap-1 rounded-xl bg-red-600 px-2.5 py-1 text-xs font-black text-white shadow-xs border border-white/20">
                                        <i class="fa-solid fa-tag text-[9px]"></i> Diskon Rp {{ number_format($product->discount, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Card Body --}}
                        <div class="flex flex-1 flex-col p-4 sm:p-5">

                            {{-- Product Name --}}
                            <h3 class="text-base font-bold text-slate-900 transition-colors group-hover:text-emerald-700 dark:text-white dark:group-hover:text-emerald-400 line-clamp-2 leading-snug">
                                {{ $product->name }}
                            </h3>

                            {{-- Variants Tag / Info --}}
                            <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                                @if(!empty($product->variants) && count($product->variants) > 0)
                                    <span class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-2.5 py-0.5 font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-900/60">
                                        <i class="fa-solid fa-layer-group text-[10px]"></i> {{ count($product->variants) }} Varian
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-0.5 font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200/60 dark:border-slate-700">
                                        Harga Tunggal
                                    </span>
                                @endif

                                @if($isOutofStock)
                                    <span class="inline-flex items-center gap-1 rounded-lg bg-red-50 px-2.5 py-0.5 font-bold text-red-700 dark:bg-red-950/60 dark:text-red-400 border border-red-200/60 dark:border-red-900/60">
                                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i> Stok Habis
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-0.5 font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                        Stok: {{ $stock }} Pcs
                                    </span>
                                @endif
                            </div>

                            {{-- Price & Actions Footer (Pushed to bottom with mt-auto) --}}
                            <div class="mt-auto border-t border-slate-100 pt-3.5 dark:border-slate-800">
                                
                                <div class="flex items-baseline justify-between gap-1 mb-3">
                                    <div>
                                        <p class="text-xs text-slate-400 font-medium">Harga Jual</p>
                                        <p class="text-base sm:text-lg font-black text-emerald-700 dark:text-emerald-400 leading-tight">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    @if($hasDiscount)
                                        <p class="text-xs text-slate-400 line-through font-semibold">
                                            Rp {{ number_format($product->price + $product->discount, 0, ',', '.') }}
                                        </p>
                                    @endif
                                </div>

                                {{-- Buttons Row --}}
                                <div class="grid grid-cols-[1fr_auto_auto] gap-2">
                                    <a
                                        href="{{ route('seller.products.edit', $product) }}"
                                        class="inline-flex items-center justify-center gap-1.5 rounded-2xl bg-emerald-700 px-4 py-2 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-800"
                                    >
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>

                                    <a
                                        href="{{ route('products.show', $product) }}"
                                        target="_blank"
                                        class="inline-flex items-center justify-center h-9 w-9 rounded-2xl border border-slate-200 bg-white text-slate-600 shadow-xs transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                                        title="Pratinjau di Toko"
                                    >
                                        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                    </a>

                                    <form
                                        id="delete-product-mobile-{{ $product->id }}"
                                        action="{{ route('seller.products.destroy', $product) }}"
                                        method="POST"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="button"
                                            onclick="confirmAction({ title: 'Hapus Produk', message: 'Apakah Anda yakin ingin menghapus produk {{ addslashes($product->name) }}?', form: 'delete-product-mobile-{{ $product->id }}', variant: 'danger', confirmText: 'Ya, Hapus' })"
                                            class="inline-flex items-center justify-center h-9 w-9 rounded-2xl bg-red-50 text-red-600 transition hover:bg-red-100 dark:bg-red-950/40 dark:text-red-400 cursor-pointer"
                                            title="Hapus Produk"
                                        >
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

            @if ($products->hasPages())
                <div class="mt-8">
                    {{ $products->withQueryString()->links() }}
                </div>
            @endif

        @else

            <x-empty-state
                title="Belum ada produk"
                description="Tambahkan produk pertamamu untuk mulai berjualan di sekolah."
                action="{{ route('seller.products.create') }}"
                actionText="Tambah Produk Baru"
            />

        @endif

    </div>

</x-layouts.seller>