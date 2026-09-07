<x-layouts.seller>
    <div class="mx-auto max-w-3xl space-y-6">

        <div>
            <a
                href="{{ route('seller.pickup-schedules.index') }}"
                class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-700">
                <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Jadwal
            </a>

            <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">
                Detail Pengambilan
            </h1>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div>

                    <p class="text-sm text-gray-500">
                        Pesanan
                    </p>

                    <h2 class="mt-1 text-xl font-bold text-gray-900 dark:text-white">
                        #{{ $pickupSchedule->order_id }}
                    </h2>

                </div>

                <x-badge
                    :type="$pickupSchedule->status"
                    :label="ucfirst(str_replace('_', ' ', $pickupSchedule->status))"
                />

            </div>

            <div class="mt-6 grid gap-5 sm:grid-cols-2">

                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-400">
                        Pembeli
                    </p>

                    <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                        {{ $pickupSchedule->order?->user?->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-400">
                        Tanggal
                    </p>

                    <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                        {{ $pickupSchedule->pickup_date?->format('d M Y') ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-400">
                        Waktu
                    </p>

                    <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                        {{ $pickupSchedule->pickup_time ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-400">
                        Lokasi
                    </p>

                    <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                        {{ $pickupSchedule->location ?? '-' }}
                    </p>
                </div>

            </div>

            @if($pickupSchedule->notes)

                <div class="mt-6 rounded-xl bg-gray-50 p-4 dark:bg-gray-800">

                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                        Catatan
                    </p>

                    <p class="mt-2 text-sm leading-relaxed text-gray-700 dark:text-gray-300">
                        {{ $pickupSchedule->notes }}
                    </p>

                </div>

            @endif

        </div>

        {{-- Update Pickup Status --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">

            <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                Update Status Pengambilan
            </h2>

            <form
                action="{{ route('seller.pickup-schedules.update', $pickupSchedule) }}"
                method="POST"
                class="mt-4 flex flex-col gap-3 sm:flex-row">

                @csrf
                @method('PUT')

                <select
                    name="status"
                    class="flex-1 rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">

                    <option value="scheduled"
                        @selected($pickupSchedule->status === 'scheduled')}>
                        Terjadwal
                    </option>

                    <option value="ready"
                        @selected($pickupSchedule->status === 'ready')}>
                        Siap Diambil
                    </option>

                    <option value="picked_up"
                        @selected($pickupSchedule->status === 'picked_up')}>
                        Sudah Diambil
                    </option>

                    <option value="cancelled"
                        @selected($pickupSchedule->status === 'cancelled')}>
                        Dibatalkan
                    </option>

                </select>

                <button
                    type="submit"
                    class="rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Simpan
                </button>

            </form>

        </div>

    </div>
</x-layouts.seller>