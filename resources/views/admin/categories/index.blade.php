<x-layouts.admin>

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Kategori
                </h1>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Kelola kategori produk marketplace.
                </p>
            </div>

            <a
                href="{{ route('admin.categories.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white hover:opacity-90 dark:bg-white dark:text-gray-900"
            >
                + Tambah Kategori
            </a>

        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-900/50 dark:bg-green-950/30 dark:text-green-400">
                {{ session('success') }}
            </div>
        @endif

        {{-- Categories --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">

            @if ($categories->count())

                {{-- Desktop --}}
                <div class="hidden overflow-x-auto md:block">

                    <table class="w-full text-left text-sm">

                        <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-4">Kategori</th>
                                <th class="px-6 py-4">Produk</th>
                                <th class="px-6 py-4">Dibuat</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">

                            @foreach ($categories as $category)

                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">

                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900 dark:text-white">
                                            {{ $category->name }}
                                        </div>

                                        @if ($category->description)
                                            <div class="mt-1 max-w-md truncate text-xs text-gray-500 dark:text-gray-400">
                                                {{ $category->description }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                        {{ $category->products_count ?? $category->products?->count() ?? 0 }}
                                    </td>

                                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                        {{ $category->created_at?->format('d M Y') ?? '-' }}
                                    </td>

                                    <td class="px-6 py-4">

                                        <div class="flex justify-end gap-2">

                                            <a
                                                href="{{ route('admin.categories.show', $category) }}"
                                                class="rounded-lg px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                                            >
                                                Detail
                                            </a>

                                            <a
                                                href="{{ route('admin.categories.edit', $category) }}"
                                                class="rounded-lg px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                                            >
                                                Edit
                                            </a>

                                            <form
                                                action="{{ route('admin.categories.destroy', $category) }}"
                                                method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus kategori ini?')"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="rounded-lg px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30"
                                                >
                                                    Hapus
                                                </button>
                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                {{-- Mobile --}}
                <div class="divide-y divide-gray-100 md:hidden dark:divide-gray-800">

                    @foreach ($categories as $category)

                        <div class="space-y-4 p-5">

                            <div>
                                <h2 class="font-semibold text-gray-900 dark:text-white">
                                    {{ $category->name }}
                                </h2>

                                @if ($category->description)
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $category->description }}
                                    </p>
                                @endif
                            </div>

                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
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
                                    class="rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold dark:border-gray-700"
                                >
                                    Detail
                                </a>

                                <a
                                    href="{{ route('admin.categories.edit', $category) }}"
                                    class="rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold dark:border-gray-700"
                                >
                                    Edit
                                </a>

                                <form
                                    action="{{ route('admin.categories.destroy', $category) }}"
                                    method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus kategori ini?')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        class="rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-600 dark:border-red-900/50"
                                    >
                                        Hapus
                                    </button>
                                </form>

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="p-10 text-center">

                    <div class="text-4xl">📂</div>

                    <h2 class="mt-4 font-semibold text-gray-900 dark:text-white">
                        Belum ada kategori
                    </h2>

                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Tambahkan kategori pertama untuk produk marketplace.
                    </p>

                    <a
                        href="{{ route('admin.categories.create') }}"
                        class="mt-5 inline-flex rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white dark:bg-white dark:text-gray-900"
                    >
                        Tambah Kategori
                    </a>

                </div>

            @endif

        </div>

        @if (method_exists($categories, 'links'))
            {{ $categories->links() }}
        @endif

    </div>

</x-layouts.admin>
