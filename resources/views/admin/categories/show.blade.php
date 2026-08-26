<x-layouts.admin>

    <div class="space-y-6">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <a
                    href="{{ route('admin.categories.index') }}"
                    class="inline-flex items-center text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                >
                    <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke kategori
                </a>

                <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="{{ $category->icon ?: 'fa-solid fa-folder' }} text-emerald-600"></i> {{ $category->name }}
                </h1>
            </div>

            <a
                href="{{ route('admin.categories.edit', $category) }}"
                class="rounded-xl bg-gray-900 px-5 py-3 text-center text-sm font-semibold text-white dark:bg-white dark:text-gray-900"
            >
                Edit Kategori
            </a>

        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            <div class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 lg:col-span-1 dark:border-gray-700 dark:bg-gray-900">

                <p class="text-xs text-gray-400">
                    Nama Kategori
                </p>

                <h2 class="mt-2 text-xl font-bold text-gray-900 dark:text-white">
                    {{ $category->name }}
                </h2>

                <p class="mt-4 text-sm leading-6 text-gray-500 dark:text-gray-400">
                    {{ $category->description ?: 'Tidak ada deskripsi.' }}
                </p>

            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 lg:col-span-2 dark:border-gray-700 dark:bg-gray-900">

                <div class="flex items-center justify-between">

                    <div>
                        <h2 class="font-semibold text-gray-900 dark:text-white">
                            Produk dalam kategori
                        </h2>

                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Produk yang menggunakan kategori ini.
                        </p>
                    </div>

                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold dark:bg-gray-800 dark:text-gray-300">
                        {{ $category->products_count ?? $category->products?->count() ?? 0 }}
                    </span>

                </div>

                @if ($category->products && $category->products->count())

                    <div class="mt-5 divide-y divide-gray-100 dark:divide-gray-800">

                        @foreach ($category->products as $product)

                            <div class="flex items-center gap-4 py-4">

                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800">
                                    📦
                                </div>

                                <div class="min-w-0 flex-1">

                                    <p class="truncate text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $product->name }}
                                    </p>

                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </p>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                        Belum ada produk dalam kategori ini.
                    </div>

                @endif

            </div>

        </div>

    </div>

</x-layouts.admin>
```
