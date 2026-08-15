<x-layouts.admin>

    <div class="mx-auto max-w-2xl space-y-6">

        <div>

            <a
                href="{{ route('admin.sellers.show', $seller) }}"
                class="text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
            >
                ← Kembali ke detail
            </a>

            <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">
                Kelola Seller
            </h1>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Verifikasi dan kelola status seller.
            </p>

        </div>

        <form
            action="{{ route('admin.sellers.update', $seller) }}"
            method="POST"
            class="space-y-6 rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-700 dark:bg-gray-900"
        >

            @csrf
            @method('PUT')

            {{-- Status --}}
            <div>

                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Status Seller
                </label>

                <select
                    name="status"
                    required
                    class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                >

                    <option
                        value="pending"
                        @selected(old('status', $seller->status) === 'pending')
                    >
                        Pending
                    </option>

                    <option
                        value="approved"
                        @selected(old('status', $seller->status) === 'approved')
                    >
                        Approved
                    </option>

                    <option
                        value="rejected"
                        @selected(old('status', $seller->status) === 'rejected')
                    >
                        Rejected
                    </option>

                </select>

                @error('status')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror

            </div>

            {{-- Description --}}
            <div>

                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Deskripsi
                </label>

                <textarea
                    name="description"
                    rows="5"
                    class="w-full resize-none rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                >{{ old('description', $seller->description) }}</textarea>

                @error('description')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror

            </div>

            {{-- Rejection --}}
            @if ($seller->status === 'rejected' || old('status') === 'rejected')

                <div>

                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Alasan Penolakan
                    </label>

                    <textarea
                        name="rejection_reason"
                        rows="4"
                        class="w-full resize-none rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        placeholder="Jelaskan alasan seller ditolak"
                    >{{ old('rejection_reason', $seller->rejection_reason) }}</textarea>

                    @error('rejection_reason')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror

                </div>

            @endif

            <div class="rounded-xl bg-gray-50 p-4 text-sm text-gray-600 dark:bg-gray-800 dark:text-gray-400">

                <strong class="text-gray-900 dark:text-white">
                    Catatan:
                </strong>

                Seller yang berstatus
                <strong>approved</strong>
                dapat menggunakan fitur seller dan mengelola produknya.

            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end dark:border-gray-800">

                <a
                    href="{{ route('admin.sellers.show', $seller) }}"
                    class="rounded-xl border border-gray-200 px-5 py-3 text-center text-sm font-semibold dark:border-gray-700 dark:text-gray-300"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white dark:bg-white dark:text-gray-900"
                >
                    Simpan Status
                </button>

            </div>

        </form>

    </div>

</x-layouts.admin>