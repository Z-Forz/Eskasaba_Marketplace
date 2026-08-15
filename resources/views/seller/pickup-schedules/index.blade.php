<x-layouts.seller>
    <div class="space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Jadwal Pengambilan
            </h1>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Kelola jadwal pengambilan pesanan oleh pembeli.
            </p>
        </div>

        @if($pickupSchedules->count())

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">

                @foreach($pickupSchedules as $schedule)

                    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-800 dark:bg-gray-900">

                        <div class="flex items-start justify-between gap-3">

                            <div>

                                <p class="text-xs uppercase tracking-wide text-gray-400">
                                    Pesanan
                                </p>

                                <h2 class="mt-1 text-lg font-bold text-gray-900 dark:text-white">
                                    #{{ $schedule->order_id }}
                                </h2>

                            </div>

                            <x-badge
                                :type="$schedule->status"
                                :label="ucfirst(str_replace('_', ' ', $schedule->status))"
                            />

                        </div>

                        <div class="mt-5 space-y-3">

                            <div>
                                <p class="text-xs text-gray-400">
                                    Pembeli
                                </p>

                                <p class="mt-1 font-medium text-gray-800 dark:text-gray-200">
                                    {{ $schedule->order?->user?->name ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-400">
                                    Tanggal
                                </p>

                                <p class="mt-1 font-medium text-gray-800 dark:text-gray-200">
                                    {{ $schedule->pickup_date?->format('d M Y') ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-400">
                                    Waktu
                                </p>

                                <p class="mt-1 font-medium text-gray-800 dark:text-gray-200">
                                    {{ $schedule->pickup_time ?? '-' }}
                                </p>
                            </div>

                            @if($schedule->location)

                                <div>
                                    <p class="text-xs text-gray-400">
                                        Lokasi
                                    </p>

                                    <p class="mt-1 font-medium text-gray-800 dark:text-gray-200">
                                        {{ $schedule->location }}
                                    </p>
                                </div>

                            @endif

                        </div>

                        <div class="mt-5 border-t border-gray-100 pt-4 dark:border-gray-800">

                            <a
                                href="{{ route('seller.pickup-schedules.show', $schedule) }}"
                                class="block rounded-xl bg-gray-900 px-4 py-2.5 text-center text-sm font-semibold text-white transition hover:bg-gray-800 dark:bg-white dark:text-gray-900">
                                Lihat Detail
                            </a>

                        </div>

                    </div>

                @endforeach

            </div>

            {{ $pickupSchedules->links() }}

        @else

            <x-empty-state
                title="Belum ada jadwal"
                description="Jadwal pengambilan pesanan akan muncul di sini."
            />

        @endif

    </div>
</x-layouts.seller>