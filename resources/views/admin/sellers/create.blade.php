<x-layouts.admin>

    <div class="mx-auto max-w-2xl space-y-6">

        <div>

            <a
                href="{{ route('admin.sellers.index') }}"
                class="text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
            >
                ← Kembali ke seller
            </a>

            <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">
                Tambah Seller
            </h1>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Buat profil seller untuk pengguna yang tersedia.
            </p>

        </div>

        <form
            action="{{ route('admin.sellers.store') }}"
            method="POST"
            class="space-y-6 rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-700 dark:bg-gray-900"
        >

            @csrf

            <div>

                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Pengguna
                </label>

                <select
                    name="user_id"
                    required
                    class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                >

                    <option value="">
                        Pilih pengguna
                    </option>

                    @foreach ($users ?? [] as $user)

                        <option
                            value="{{ $user->id }}"
                            @selected(old('user_id') == $user->id)
                        >
                            {{ $user->name ?? $user->username }}
                            —
                            {{ $user->email ?? $user->school_number ?? '-' }}
                        </option>

                    @endforeach

                </select>

                @error('user_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror

            </div>

            <div>

                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Deskripsi Seller
                </label>

                <textarea
                    name="description"
                    rows="5"
                    class="w-full resize-none rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                    placeholder="Deskripsi toko seller"
                >{{ old('description') }}</textarea>

                @error('description')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror

            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end dark:border-gray-800">

                <a
                    href="{{ route('admin.sellers.index') }}"
                    class="rounded-xl border border-gray-200 px-5 py-3 text-center text-sm font-semibold dark:border-gray-700 dark:text-gray-300"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white dark:bg-white dark:text-gray-900"
                >
                    Simpan Seller
                </button>

            </div>

        </form>

    </div>

</x-layouts.admin>