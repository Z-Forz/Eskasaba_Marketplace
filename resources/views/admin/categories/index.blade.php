<x-layouts.admin title="Kelola Kategori">

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-layer-group text-emerald-600"></i> Kelola Kategori Produk
                </h1>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Kelola kategori dan pengelompokan produk di Eskasaba Marketplace.
                </p>
            </div>

            <a
                href="{{ route('admin.categories.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-5 py-3 text-sm font-bold text-white shadow-xs transition hover:bg-emerald-800"
            >
                <i class="fa-solid fa-plus"></i> Tambah Kategori Baru
            </a>

        </div>

        {{-- Alert --}}
        @if (session('success'))
            <x-alert type="success" :message="session('success')" class="mb-4" />
        @endif

        {{-- Categories Table --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white dark:border-slate-800 dark:bg-slate-900 shadow-xs">

            @if ($categories->count())

                {{-- Desktop Table --}}
                <div class="hidden overflow-x-auto md:block">

                    <table class="w-full text-left text-sm">

                        <thead class="border-b border-slate-100 bg-slate-50/80 text-xs font-bold uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:bg-slate-800/80 dark:text-slate-400">
                            <tr>
                                <th class="px-6 py-4">Kategori</th>
                                <th class="px-6 py-4">Jumlah Produk</th>
                                <th class="px-6 py-4">Tanggal Dibuat</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">

                            @foreach ($categories as $category)

                                <tr class="transition hover:bg-slate-50/60 dark:hover:bg-slate-800/50">

                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                            <i class="{{ $category->icon ?: 'fa-solid fa-folder' }} text-emerald-600"></i> {{ $category->name }}
                                        </div>

                                        @if ($category->description)
                                            <div class="mt-1 max-w-md truncate text-xs text-slate-500 dark:text-slate-400">
                                                {{ $category->description }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1 rounded-xl bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                            <i class="fa-solid fa-boxes-stacked text-xs text-slate-400"></i> {{ $category->products_count ?? $category->products?->count() ?? 0 }} Produk
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-xs font-medium text-slate-500 dark:text-slate-400">
                                        {{ $category->created_at?->format('d M Y') ?? '-' }}
                                    </td>

                                    <td class="px-6 py-4">

                                        <div class="flex justify-end gap-2">

                                            <a
                                                href="{{ route('admin.categories.show', $category) }}"
                                                class="inline-flex items-center gap-1 rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-bold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                                            >
                                                <i class="fa-solid fa-eye"></i> Detail
                                            </a>

                                            <a
                                                href="{{ route('admin.categories.edit', $category) }}"
                                                class="inline-flex items-center gap-1 rounded-xl bg-slate-900 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-slate-800 dark:bg-emerald-700 dark:hover:bg-emerald-800"
                                            >
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </a>

                                            <form
                                                id="delete-category-{{ $category->id }}"
                                                action="{{ route('admin.categories.destroy', $category) }}"
                                                method="POST"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="button"
                                                    onclick="confirmAction({ title: 'Hapus Kategori', message: 'Apakah Anda yakin ingin menghapus kategori {{ addslashes($category->name) }}?', form: 'delete-category-{{ $category->id }}', variant: 'danger', confirmText: 'Ya, Hapus' })"
                                                    class="inline-flex items-center gap-1 rounded-xl bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 transition hover:bg-red-100 dark:bg-red-950/40 dark:text-red-400 cursor-pointer"
                                                >
                                                    <i class="fa-solid fa-trash"></i> Hapus
                                                </button>
                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                {{-- Mobile List --}}
                <div class="divide-y divide-slate-100 md:hidden dark:divide-slate-800">

                    @foreach ($categories as $category)

                        <div class="space-y-4 p-5">

                            <div>
                                <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <i class="{{ $category->icon ?: 'fa-solid fa-folder' }} text-emerald-600"></i> {{ $category->name }}
                                </h2>

                                @if ($category->description)
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                        {{ $category->description }}
                                    </p>
                                @endif
                            </div>

                            <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                                <span>
                                    {{ $category->products_count ?? $category->products?->count() ?? 0 }} produk
                                </span>

                                <span>
                                    {{ $category->created_at?->format('d M Y') ?? '-' }}
                                </span>
                            </div>

                            <div class="flex flex-wrap gap-2">

                                <a
                                    href="{{ route('admin.categories.show', $category) }}"
                                    class="rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-bold text-slate-700 dark:border-slate-700 dark:text-slate-300 flex items-center gap-1"
                                >
                                    <i class="fa-solid fa-eye"></i> Detail
                                </a>

                                <a
                                    href="{{ route('admin.categories.edit', $category) }}"
                                    class="rounded-xl bg-slate-900 px-3 py-1.5 text-xs font-bold text-white dark:bg-emerald-700 flex items-center gap-1"
                                >
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>

                                <form
                                    id="delete-category-mobile-{{ $category->id }}"
                                    action="{{ route('admin.categories.destroy', $category) }}"
                                    method="POST"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="button"
                                        onclick="confirmAction({ title: 'Hapus Kategori', message: 'Apakah Anda yakin ingin menghapus kategori {{ addslashes($category->name) }}?', form: 'delete-category-mobile-{{ $category->id }}', variant: 'danger', confirmText: 'Ya, Hapus' })"
                                        class="inline-flex items-center gap-1 rounded-xl bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 dark:bg-red-950/40 dark:text-red-400 cursor-pointer"
                                    >
                                        <i class="fa-solid fa-trash"></i> Hapus
                                    </button>
                                </form>

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="p-12 text-center">

                    <div class="text-4xl text-slate-300 mb-3"><i class="fa-solid fa-layer-group"></i></div>

                    <h2 class="font-bold text-slate-900 dark:text-white text-base">
                        Belum Ada Kategori
                    </h2>

                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Tambahkan kategori pertama untuk pengelompokan produk di marketplace.
                    </p>

                    <a
                        href="{{ route('admin.categories.create') }}"
                        class="mt-5 inline-flex items-center gap-2 rounded-2xl bg-emerald-700 px-5 py-3 text-xs font-bold text-white shadow-xs"
                    >
                        <i class="fa-solid fa-plus"></i> Tambah Kategori Baru
                    </a>

                </div>

            @endif

        </div>

        @if (method_exists($categories, 'links'))
            {{ $categories->links() }}
        @endif

    </div>

</x-layouts.admin>
