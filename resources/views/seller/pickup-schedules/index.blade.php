<x-layouts.seller title="Jadwal & Lokasi Pengambilan">

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days text-emerald-600"></i> Jadwal & Titik Pengambilan (COD)
                </h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Kelola titik temu pengambilan pesanan aktif di sekolah. Pesanan yang telah selesai tidak ditampilkan lagi di jadwal aktif.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <span class="rounded-full bg-emerald-100 px-3.5 py-1.5 text-xs font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                    <i class="fa-solid fa-clock text-xs mr-1"></i> {{ $activeCount ?? 0 }} Jadwal Aktif
                </span>
            </div>
        </div>

        @if (session('success'))
            <x-alert type="success" :message="session('success')" class="mb-4" />
        @endif

        {{-- Filter Tabs Bar --}}
        <div class="flex flex-wrap gap-2 rounded-2xl border border-slate-200/80 bg-white p-2 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            @php
                $currentStatus = request('status', 'active');
                $tabs = [
                    'active'           => ['label' => 'Jadwal Pengambilan Aktif', 'icon' => 'fa-clock'],
                    'ready_for_pickup' => ['label' => 'Siap Diambil',             'icon' => 'fa-box-open'],
                    'processing'       => ['label' => 'Sedang Diproses',          'icon' => 'fa-fire-burner'],
                    'completed'        => ['label' => 'Riwayat Selesai',          'icon' => 'fa-circle-check'],
                ];
            @endphp

            @foreach ($tabs as $key => $tab)
                <a
                    href="{{ route('seller.pickup-schedules.index', ['status' => $key]) }}"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-bold transition {{ $currentStatus === $key ? 'bg-emerald-700 text-white shadow-xs' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300' }}"
                >
                    <i class="fa-solid {{ $tab['icon'] }}"></i>
                    <span>{{ $tab['label'] }}</span>
                </a>
            @endforeach
        </div>

        {{-- Orders List with Pickup Locations --}}
        @if($orders->count())

            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">

                @foreach($orders as $order)

                    <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs transition hover:border-slate-300 dark:border-slate-800 dark:bg-slate-900 sm:p-6 flex flex-col justify-between">

                        <div>
                            {{-- Header --}}
                            <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-3 dark:border-slate-800">
                                <div>
                                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                                        Nomor Invoice
                                    </p>
                                    <h2 class="mt-0.5 text-base font-bold text-slate-900 dark:text-white">
                                        {{ $order->invoice_number ?? '#' . $order->id }}
                                    </h2>
                                </div>

                                <x-badge
                                    :type="$order->status"
                                    :label="ucfirst(str_replace('_', ' ', $order->status))"
                                />
                            </div>

                            {{-- Details --}}
                            <div class="mt-4 space-y-3 text-xs sm:text-sm">

                                <div>
                                    <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">
                                        Pembeli
                                    </p>
                                    <p class="mt-0.5 font-bold text-slate-900 dark:text-white">
                                        <i class="fa-solid fa-user text-slate-400 mr-1"></i> {{ $order->user?->username ?? '-' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">
                                        Titik Lokasi Pengambilan (Sekolah)
                                    </p>
                                    <p class="mt-0.5 font-extrabold text-emerald-700 dark:text-emerald-400">
                                        <i class="fa-solid fa-location-dot mr-1"></i> {{ $order->pickup_location }}
                                    </p>
                                </div>

                                {{-- Selected Items & Variants --}}
                                @if($order->items->count())
                                    <div>
                                        <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold mb-1">
                                            Item & Varian Rasa
                                        </p>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($order->items as $item)
                                                <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                    {{ $item->quantity }}x {{ $item->product_name ?? $item->product?->name }}
                                                    @if(!empty($item->note))
                                                        <span class="text-emerald-700 dark:text-emerald-400">({{ $item->note }})</span>
                                                    @endif
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <div>
                                    <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">
                                        Waktu Pesanan
                                    </p>
                                    <p class="mt-0.5 font-semibold text-slate-700 dark:text-slate-300">
                                        <i class="fa-regular fa-calendar text-slate-400 mr-1"></i> {{ $order->created_at?->format('d M Y, H:i') }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">
                                        Total Tagihan
                                    </p>
                                    <p class="mt-0.5 font-black text-slate-900 dark:text-white">
                                        Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}
                                    </p>
                                </div>

                            </div>
                        </div>

                        {{-- Action Button --}}
                        <div class="mt-5 border-t border-slate-100 pt-4 dark:border-slate-800">
                            <a
                                href="{{ route('seller.orders.show', $order) }}"
                                class="flex w-full items-center justify-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white shadow-xs transition hover:bg-slate-800 dark:bg-emerald-700 dark:hover:bg-emerald-800"
                            >
                                Edit Lokasi & Kelola Status <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>

                    </div>

                @endforeach

            </div>

            <div class="mt-6">
                {{ $orders->links() }}
            </div>

        @else

            <x-empty-state
                title="Tidak Ada Jadwal Pengambilan Aktif"
                description="Semua pesanan pengambilan telah selesai atau belum ada pesanan aktif baru."
            />

        @endif

    </div>

</x-layouts.seller>