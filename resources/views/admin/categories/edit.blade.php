<x-layouts.admin>

    <div class="mx-auto max-w-2xl space-y-6">

        <div>
            <a
                href="{{ route('admin.categories.show', $category) }}"
                class="inline-flex items-center text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
            >
                <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke detail
            </a>

            <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">
                Edit Kategori
            </h1>
        </div>

        <form
            action="{{ route('admin.categories.update', $category) }}"
            method="POST"
            class="space-y-6 rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-700 dark:bg-gray-900"
        >

            @csrf
            @method('PUT')

            <div>

                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Nama Kategori
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $category->name) }}"
                    required
                    class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                >

                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror

            </div>

            <div>

                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Deskripsi
                </label>

                <textarea
                    name="description"
                    rows="5"
                    class="w-full resize-none rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                >{{ old('description', $category->description) }}</textarea>

                @error('description')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror

            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end dark:border-gray-800">

                <a
                    href="{{ route('admin.categories.show', $category) }}"
                    class="rounded-xl border border-gray-200 px-5 py-3 text-center text-sm font-semibold text-gray-700 dark:border-gray-700 dark:text-gray-300"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white dark:bg-white dark:text-gray-900"
                >
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</x-layouts.admin>