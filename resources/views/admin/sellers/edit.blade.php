<x-layouts.admin title="Kelola Seller">

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
                        Pending (Menunggu Verifikasi)
                    </option>

                    <option
                        value="approved"
                        @selected(old('status', $seller->status) === 'approved')
                    >
                        Approved (Disetujui)
                    </option>

                    <option
                        value="revision"
                        @selected(old('status', $seller->status) === 'revision')
                    >
                        Revision (Perlu Revisi)
                    </option>

                    <option
                        value="rejected"
                        @selected(old('status', $seller->status) === 'rejected')
                    >
                        Rejected (Ditolak)
                    </option>

                </select>

                @error('status')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror

            </div>

            {{-- WhatsApp Number --}}
            <div>

                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Nomor WhatsApp
                </label>

                <input
                    type="text"
                    name="whatsapp_number"
                    value="{{ old('whatsapp_number', $seller->whatsapp_number) }}"
                    placeholder="Contoh: 081234567890"
                    class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                />

                @error('whatsapp_number')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror

            </div>

            {{-- Description --}}
            <div>

                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Deskripsi Toko
                </label>

                <textarea
                    name="description"
                    rows="4"
                    class="w-full resize-none rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                >{{ old('description', $seller->description) }}</textarea>

                @error('description')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror

            </div>

            {{-- Rejection / Revision Note --}}
            <div>

                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Catatan Admin (Revisi / Penolakan)
                </label>

                <textarea
                    name="rejection_note"
                    rows="3"
                    class="w-full resize-none rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                    placeholder="Jelaskan alasan jika ditolak atau minta revisi"
                >{{ old('rejection_note', $seller->rejection_note) }}</textarea>

                @error('rejection_note')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror

            </div>

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
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</x-layouts.admin>