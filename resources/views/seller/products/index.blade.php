<x-layouts.seller title="Katalog Produk Toko">

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-box-archive text-emerald-600"></i> Katalog Produk Toko
                </h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Kelola seluruh produk jualan Anda, atur ketersediaan stok, dan ubah varian/kondisi barang.
                </p>
            </div>

            <a
                href="{{ route('seller.products.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-5 py-3 text-sm font-bold text-white shadow-xs transition hover:bg-emerald-800"
            >
                <i class="fa-solid fa-plus"></i> Tambah Produk Baru
            </a>
        </div>

        {{-- Flash Alert --}}
        @if (session('success'))
            <x-alert type="success" :message="session('success')" class="mb-4" />
        @endif

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

                <select
                    name="status"
                    class="rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                >
                    <option value="">Semua Status</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                </select>

                <button
                    type="submit"
                    class="rounded-2xl bg-emerald-700 px-5 py-2.5 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-800 flex items-center gap-1.5 justify-center"
                >
                    <i class="fa-solid fa-magnifying-glass"></i> Cari
                </button>
            </form>
        </div>

        {{-- Products HTML Data Table --}}
        @if ($products->count())

            <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900">

                <div class="overflow-x-auto">

                    <table class="min-w-full text-left text-sm">

                        <thead class="border-b border-slate-100 bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:bg-slate-800/50">
                            <tr>
                                <th class="px-6 py-4">Produk</th>
                                <th class="px-6 py-4">Kategori</th>
                                <th class="px-6 py-4">Harga</th>
                                <th class="px-6 py-4">Sisa Stok</th>
                                <th class="px-6 py-4">Varian / Kondisi</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">

                            @foreach ($products as $product)

                                <tr class="transition hover:bg-slate-50/80 dark:hover:bg-slate-800/50">

                                    {{-- Product Image & Name --}}
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-12 w-12 shrink-0 overflow-hidden rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700">
                                                @if ($product->images->first())
                                                    <img
                                                        src="{{ Storage::url($product->images->first()->image) }}"
                                                        alt="{{ $product->name }}"
                                                        class="h-full w-full object-cover"
                                                    >
                                                @else
                                                    <div class="flex h-full w-full items-center justify-center text-slate-400">
                                                        <i class="fa-solid fa-box text-base"></i>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="min-w-0">
                                                <a
                                                    href="{{ route('products.show', $product) }}"
                                                    target="_blank"
                                                    class="font-bold text-slate-900 hover:text-emerald-700 dark:text-white dark:hover:text-emerald-400 text-sm transition"
                                                >
                                                    {{ $product->name }} <i class="fa-solid fa-arrow-up-right-from-square text-[10px] opacity-70"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Category --}}
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="inline-flex items-center rounded-xl bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                            {{ $product->category?->name ?? 'Umum' }}
                                        </span>
                                    </td>

                                    {{-- Price & Discount --}}
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <p class="font-black text-slate-900 dark:text-white">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </p>
                                        @if($product->discount > 0)
                                            <p class="text-[11px] font-bold text-red-600">
                                                Diskon: Rp {{ number_format($product->discount, 0, ',', '.') }}
                                            </p>
                                        @endif
                                    </td>

                                    {{-- Stock --}}
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if($product->stock > 0)
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                                <span class="h-2 w-2 rounded-full bg-emerald-600"></span> {{ $product->stock }} Pcs
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-3 py-1 text-xs font-bold text-red-700 dark:bg-red-950/60 dark:text-red-300">
                                                <span class="h-2 w-2 rounded-full bg-red-600"></span> Habis (0)
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Condition / Variant --}}
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if($product->condition)
                                            <span class="inline-flex items-center rounded-xl bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                                {{ $product->condition }}
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-400 font-medium">-</span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $product->status === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                                            {{ $product->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a
                                                href="{{ route('seller.products.edit', $product) }}"
                                                class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-3.5 py-1.5 text-xs font-bold text-white shadow-xs transition hover:bg-slate-800 dark:bg-emerald-700 dark:hover:bg-emerald-800"
                                            >
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </a>

                                            <form
                                                id="delete-product-{{ $product->id }}"
                                                action="{{ route('seller.products.destroy', $product) }}"
                                                method="POST"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="button"
                                                    onclick="confirmAction({ title: 'Hapus Produk', message: 'Apakah Anda yakin ingin menghapus produk {{ addslashes($product->name) }}?', form: 'delete-product-{{ $product->id }}', variant: 'danger', confirmText: 'Ya, Hapus' })"
                                                    class="inline-flex items-center gap-1.5 rounded-xl bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 transition hover:bg-red-100 dark:bg-red-950/40 dark:text-red-400 cursor-pointer"
                                                    title="Hapus Produk"
                                                >
                                                    <i class="fa-solid fa-trash"></i>
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

            @if ($products->hasPages())
                <div class="mt-6">
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