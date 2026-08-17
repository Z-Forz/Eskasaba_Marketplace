<x-layouts.seller title="Detail & Konfirmasi Pesanan">

    <div class="mx-auto max-w-5xl space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <a
                    href="{{ route('seller.orders.index') }}"
                    class="mb-2 inline-flex items-center gap-1 text-xs font-bold text-emerald-700 transition hover:text-emerald-800 dark:text-emerald-400">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Pesanan
                </a>

                <h1 class="text-2xl font-black text-slate-900 dark:text-white">
                    Pesanan {{ $order->invoice_number ?? '#' . $order->id }}
                </h1>

                <p class="mt-1 text-xs text-slate-500">
                    <i class="fa-regular fa-calendar mr-1"></i> Tanggal Pesanan: {{ $order->created_at?->format('d M Y, H:i') }}
                </p>
            </div>

            <x-badge
                :type="$order->status"
                :label="ucfirst(str_replace('_', ' ', $order->status))"
            />

        </div>

        @if (session('success'))
            <x-alert type="success" :message="session('success')" class="mb-4" />
        @endif

        {{-- Order Progress Stepper Bar --}}
        @php
            $statusStep = match($order->status) {
                'pending'          => 1,
                'confirmed'        => 2,
                'processing'       => 3,
                'ready_for_pickup' => 4,
                'completed'        => 5,
                'cancelled'        => 0,
                default            => 1,
            };
        @endphp

        @if($order->status !== 'cancelled')
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-4">
                    Status Progres Pesanan
                </p>

                <div class="relative flex items-center justify-between">
                    {{-- Progress Line --}}
                    <div class="absolute left-0 top-1/2 -z-0 h-1 w-full -translate-y-1/2 bg-slate-100 dark:bg-slate-800"></div>
                    <div
                        class="absolute left-0 top-1/2 -z-0 h-1 -translate-y-1/2 bg-emerald-600 transition-all duration-500"
                        style="width: {{ (($statusStep - 1) / 4) * 100 }}%"
                    ></div>

                    {{-- Step 1 --}}
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-bold shadow-xs {{ $statusStep >= 1 ? 'bg-emerald-700 text-white ring-4 ring-emerald-100 dark:ring-emerald-950' : 'bg-slate-100 text-slate-400 dark:bg-slate-800' }}">
                            1
                        </div>
                        <span class="mt-2 text-[11px] font-bold {{ $statusStep >= 1 ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-400' }}">Dibuat</span>
                    </div>

                    {{-- Step 2 --}}
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-bold shadow-xs {{ $statusStep >= 2 ? 'bg-emerald-700 text-white ring-4 ring-emerald-100 dark:ring-emerald-950' : 'bg-slate-100 text-slate-400 dark:bg-slate-800' }}">
                            2
                        </div>
                        <span class="mt-2 text-[11px] font-bold {{ $statusStep >= 2 ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-400' }}">Dikonfirmasi</span>
                    </div>

                    {{-- Step 3 --}}
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-bold shadow-xs {{ $statusStep >= 3 ? 'bg-emerald-700 text-white ring-4 ring-emerald-100 dark:ring-emerald-950' : 'bg-slate-100 text-slate-400 dark:bg-slate-800' }}">
                            3
                        </div>
                        <span class="mt-2 text-[11px] font-bold {{ $statusStep >= 3 ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-400' }}">Diproses</span>
                    </div>

                    {{-- Step 4 --}}
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-bold shadow-xs {{ $statusStep >= 4 ? 'bg-emerald-700 text-white ring-4 ring-emerald-100 dark:ring-emerald-950' : 'bg-slate-100 text-slate-400 dark:bg-slate-800' }}">
                            4
                        </div>
                        <span class="mt-2 text-[11px] font-bold {{ $statusStep >= 4 ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-400' }}">Siap Diambil</span>
                    </div>

                    {{-- Step 5 --}}
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-bold shadow-xs {{ $statusStep >= 5 ? 'bg-emerald-700 text-white ring-4 ring-emerald-100 dark:ring-emerald-950' : 'bg-slate-100 text-slate-400 dark:bg-slate-800' }}">
                            5
                        </div>
                        <span class="mt-2 text-[11px] font-bold {{ $statusStep >= 5 ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-400' }}">Selesai</span>
                    </div>
                </div>
            </div>
        @else
            <div class="rounded-3xl border border-red-200 bg-red-50 p-5 text-center dark:border-red-900/50 dark:bg-red-950/40">
                <p class="text-sm font-bold text-red-700 dark:text-red-400"><i class="fa-solid fa-circle-xmark mr-1"></i> Pesanan Ini Telah Dibatalkan / Ditolak</p>
            </div>
        @endif

        {{-- Buyer & Pickup Info --}}
        <div class="grid gap-6 sm:grid-cols-2">

            {{-- Buyer Details --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <h2 class="text-base font-bold text-slate-900 dark:text-white">
                    <i class="fa-solid fa-user mr-1.5 text-slate-500"></i> Informasi Pembeli
                </h2>

                <div class="mt-4 space-y-3 text-sm">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400">Nama Pembeli</p>
                        <p class="mt-0.5 font-bold text-slate-900 dark:text-white">
                            {{ $order->user?->username ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400">Email / No. HP</p>
                        <p class="mt-0.5 font-semibold text-slate-700 dark:text-slate-300">
                            {{ $order->user?->email ?? $order->user?->phone ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Pickup & Note Details --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <h2 class="text-base font-bold text-slate-900 dark:text-white">
                    <i class="fa-solid fa-location-dot mr-1.5 text-emerald-600"></i> Titik Temu & Catatan COD
                </h2>

                <div class="mt-4 space-y-3 text-sm">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400">Lokasi Pengambilan (Sekolah)</p>
                        <p class="mt-0.5 font-bold text-emerald-700 dark:text-emerald-400">
                            <i class="fa-solid fa-map-pin mr-1"></i> {{ $order->pickup_location ?? 'Belum ditentukan' }}
                        </p>
                    </div>

                    @if($order->note)
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-400">Catatan dari Pembeli</p>
                            <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-300 italic">
                                "{{ $order->note }}"
                            </p>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Payment & QRIS Info Card --}}
        <div class="rounded-3xl border border-emerald-200/80 bg-emerald-50/50 p-6 shadow-xs dark:border-emerald-950 dark:bg-emerald-950/20">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-black uppercase tracking-wide
                        {{ strtolower($order->payment?->method ?? '') === 'qris' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200' : 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-200' }}"
                    >
                        <i class="{{ strtolower($order->payment?->method ?? '') === 'qris' ? 'fa-solid fa-qrcode' : 'fa-solid fa-money-bill-wave' }}"></i>
                        {{ strtolower($order->payment?->method ?? '') === 'qris' ? 'Pembayaran QRIS Non-Tunai' : 'Pembayaran Tunai (COD)' }}
                    </span>

                    <h2 class="mt-2 text-lg font-black text-slate-900 dark:text-white">
                        Status Pembayaran:
                        <span class="
                            {{ match($order->payment?->status) {
                                'verified', 'paid' => 'text-emerald-700 dark:text-emerald-400',
                                'rejected'         => 'text-red-600 dark:text-red-400',
                                default            => 'text-amber-600 dark:text-amber-400'
                            } }}"
                        >
                            {{ match($order->payment?->status) {
                                'verified', 'paid' => 'Terverifikasi (Lunas)',
                                'rejected'         => 'Pembayaran Ditolak',
                                default            => 'Menunggu Konfirmasi / Verifikasi Seller'
                            } }}
                        </span>
                    </h2>
                </div>

                <div class="text-right">
                    <p class="text-xs uppercase tracking-wide text-slate-400 font-semibold">Total Tagihan Pesanan</p>
                    <p class="text-2xl font-black text-emerald-700 dark:text-emerald-400">
                        Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            {{-- QRIS Preview for Seller --}}
            @if(strtolower($order->payment?->method ?? '') === 'qris' && $order->seller?->qris_image)
                <div class="mt-5 rounded-2xl border border-emerald-100 bg-white p-4 dark:border-slate-800 dark:bg-slate-900 flex flex-col sm:flex-row items-center gap-4">
                    <img
                        src="{{ Storage::url($order->seller->qris_image) }}"
                        alt="QRIS Toko"
                        class="h-28 w-28 rounded-xl border border-slate-100 object-cover shadow-xs"
                    >
                    <div>
                        <p class="text-xs font-bold text-slate-900 dark:text-white"><i class="fa-solid fa-qrcode mr-1 text-emerald-600"></i> Barcode QRIS Toko Anda yang Digunakan Pembeli</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                            Silakan cek aplikasi mutasi m-banking/e-wallet Anda untuk memverifikasi dana sebesar <strong>Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}</strong> telah masuk sebelum mengonfirmasi pesanan.
                        </p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Order Items --}}
        <div class="rounded-3xl border border-slate-200/80 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900 overflow-hidden">

            <div class="border-b border-slate-100 p-5 dark:border-slate-800">
                <h2 class="text-base font-bold text-slate-900 dark:text-white">
                    <i class="fa-solid fa-box mr-1.5 text-slate-500"></i> Produk Pesanan
                </h2>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($order->items as $item)
                    <div class="flex items-center justify-between p-5">
                        <div>
                            <p class="font-bold text-slate-900 dark:text-white text-sm">
                                {{ $item->product_name ?? $item->product?->name }}
                            </p>
                            @if($item->note)
                                <p class="mt-1 inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-2 py-0.5 text-xs font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                    <i class="fa-solid fa-tag text-[10px]"></i> Varian / Rasa: {{ $item->note }}
                                </p>
                            @endif
                            <p class="mt-1 text-xs text-slate-500">
                                {{ $item->quantity }} × Rp {{ number_format($item->price ?? $item->unit_price ?? 0, 0, ',', '.') }}
                            </p>
                        </div>

                        <p class="font-extrabold text-slate-900 dark:text-white text-sm">
                            Rp {{ number_format(($item->quantity) * ($item->price ?? $item->unit_price ?? 0), 0, ',', '.') }}
                        </p>
                    </div>
                @endforeach
            </div>

        </div>

        {{-- Seller Update Form (Order Status, QRIS Payment Verification, & Pickup Location) --}}
        <div
            x-data="{ locationInput: '{{ old('pickup_location', $order->pickup_location ?? 'Kantin Utama') }}' }"
            class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900"
        >

            <h2 class="text-base font-black text-slate-900 dark:text-white">
                <i class="fa-solid fa-bolt text-emerald-600 mr-1.5"></i> Konfirmasi Pesanan & Pembayaran QRIS (Seller)
            </h2>
            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                Konfirmasi pembayaran QRIS dan perbarui status pengerjaan atau lokasi pengambilan barang.
            </p>

            <form
                action="{{ route('seller.orders.update', $order) }}"
                method="POST"
                class="mt-5 space-y-5"
            >
                @csrf
                @method('PUT')

                <div class="grid gap-4 sm:grid-cols-2">

                    {{-- Order Status --}}
                    <div>
                        <label for="status" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Status Pesanan
                        </label>
                        <select
                            id="status"
                            name="status"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >
                            <option value="pending" @selected($order->status === 'pending')>Menunggu Konfirmasi</option>
                            <option value="confirmed" @selected($order->status === 'confirmed')>Dikonfirmasi (Diterima)</option>
                            <option value="processing" @selected($order->status === 'processing')>Sedang Diproses</option>
                            <option value="ready_for_pickup" @selected($order->status === 'ready_for_pickup')>Siap Diambil (Ready for Pickup)</option>
                            <option value="completed" @selected($order->status === 'completed')>Pesanan Selesai</option>
                            <option value="cancelled" @selected($order->status === 'cancelled')>Dibatalkan / Ditolak</option>
                        </select>
                    </div>

                    {{-- QRIS Payment Verification --}}
                    <div>
                        <label for="payment_status" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Konfirmasi Pembayaran QRIS / COD
                        </label>
                        <select
                            id="payment_status"
                            name="payment_status"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >
                            <option value="verified" @selected(($order->payment?->status ?? '') === 'verified' || ($order->payment?->status ?? '') === 'paid')>
                                Terverifikasi / Pembayaran Lunas
                            </option>
                            <option value="pending" @selected(($order->payment?->status ?? '') === 'pending')>
                                Menunggu Pembayaran / Verifikasi
                            </option>
                            <option value="rejected" @selected(($order->payment?->status ?? '') === 'rejected')>
                                Pembayaran Ditolak
                            </option>
                        </select>
                    </div>

                </div>

                {{-- Pickup Location Input & Quick Chips --}}
                <div>
                    <label for="pickup_location" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Lokasi Titik Pengambilan di Sekolah (COD)
                    </label>

                    <input
                        id="pickup_location"
                        type="text"
                        name="pickup_location"
                        x-model="locationInput"
                        placeholder="Contoh: Kantin Utama, Gazebo RPL, Depan Perpus..."
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    >

                    {{-- Quick Recommendation Chips --}}
                    <div class="mt-2.5 flex flex-wrap items-center gap-2">
                        <span class="text-xs font-medium text-slate-400">Pilih Cepat:</span>
                        <button
                            type="button"
                            @click="locationInput = 'Kantin Utama Sekolah'"
                            class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700 transition hover:bg-emerald-50 hover:text-emerald-800 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                        >
                            <i class="fa-solid fa-utensils mr-1"></i> Kantin Utama
                        </button>
                        <button
                            type="button"
                            @click="locationInput = 'Gazebo RPL / Lapangan Tengah'"
                            class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700 transition hover:bg-emerald-50 hover:text-emerald-800 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                        >
                            <i class="fa-solid fa-tree mr-1"></i> Gazebo RPL
                        </button>
                        <button
                            type="button"
                            @click="locationInput = 'Depan Perpustakaan Sekolah'"
                            class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700 transition hover:bg-emerald-50 hover:text-emerald-800 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                        >
                            <i class="fa-solid fa-book mr-1"></i> Depan Perpus
                        </button>
                        <button
                            type="button"
                            @click="locationInput = 'Ruang Lab Komputer / Praktikum'"
                            class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700 transition hover:bg-emerald-50 hover:text-emerald-800 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                        >
                            <i class="fa-solid fa-laptop-code mr-1"></i> Lab Komputer
                        </button>
                        <button
                            type="button"
                            @click="locationInput = 'Pos Satpam Gerbang Sekolah'"
                            class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700 transition hover:bg-emerald-50 hover:text-emerald-800 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                        >
                            <i class="fa-solid fa-building-shield mr-1"></i> Pos Satpam Gerbang
                        </button>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button
                        type="submit"
                        class="rounded-2xl bg-emerald-700 px-6 py-3 text-sm font-bold text-white shadow-xs transition hover:bg-emerald-800 flex items-center gap-2"
                    >
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Konfirmasi Pesanan & Pembayaran
                    </button>
                </div>

            </form>

        </div>

    </div>

</x-layouts.seller>