<x-layouts.buyer title="Checkout Pesanan">

    <div class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <a
                href="{{ route('buyer.cart.index') }}"
                class="inline-flex items-center text-sm font-medium text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
            >
                <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke keranjang
            </a>

            <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-3xl">
                Checkout Pesanan
            </h1>

            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                Tentukan titik temu lokasi pengambilan dan metode pembayaran pesananmu.
            </p>
        </div>

        @if ($errors->any())
            <x-alert
                type="error"
                :message="$errors->first()"
                class="mb-6"
            />
        @endif

        @if (session('error'))
            <x-alert
                type="error"
                :message="session('error')"
                class="mb-6"
            />
        @endif

        @if ($cart && $cart->items->count())

            @php
                $seller = $cart->items->first()?->product?->seller;
            @endphp

            <form
                method="POST"
                action="{{ route('buyer.checkout.store') }}"
            >
                @csrf

                <div class="grid gap-6 lg:grid-cols-3">

                    {{-- Left Column: Items & Pickup Details --}}
                    <div class="space-y-6 lg:col-span-2">

                        {{-- Order Items grouped by Seller --}}
                        @foreach ($cart->items->groupBy(fn ($item) => $item->product->seller_id) as $sellerId => $items)

                            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900">

                                <div class="border-b border-slate-100 bg-slate-50/50 px-5 py-4 dark:border-slate-800 dark:bg-slate-800/40 sm:px-6">
                                    <div class="flex items-center justify-between gap-4">
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                                Penjual Toko
                                            </p>
                                            <h2 class="mt-0.5 text-base font-bold text-slate-900 dark:text-white">
                                                <i class="fa-solid fa-store" style="color: {{ $items->first()->product->seller->user->color }};"></i> {{ $items->first()->product->seller->user->username ?? 'Penjual' }}
                                            </h2>
                                        </div>

                                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                                            Penjual Terverifikasi
                                        </span>
                                    </div>
                                </div>

                                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @foreach ($items as $item)
                                        <div class="flex gap-4 p-5 sm:p-6">
                                            <div class="h-20 w-20 shrink-0 overflow-hidden rounded-2xl bg-slate-100 dark:bg-slate-800 sm:h-24 sm:w-24">
                                                @if ($item->product->images->first())
                                                    <img
                                                        src="{{ Storage::url($item->product->images->first()->image) }}"
                                                        alt="{{ $item->product->name }}"
                                                        class="h-full w-full object-cover"
                                                    >
                                                @else
                                                    <div class="flex h-full w-full items-center justify-center text-xs text-slate-400">
                                                        No Image
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                <h3 class="font-bold text-slate-900 dark:text-white">
                                                    {{ $item->product->name }}
                                                </h3>

                                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                                    {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}
                                                </p>

                                                <p class="mt-2 text-base font-extrabold text-slate-900 dark:text-white">
                                                    Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            </div>

                        @endforeach

                        {{-- Manual Pickup Location Form --}}
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900 sm:p-8">

                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                                        Lokasi & Titik Pengambilan (COD Sekolah)
                                    </h2>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        Tentukan area titik temu di sekitar sekolah untuk mengambil pesanan ini.
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6 space-y-4">
                                <div>
                                    <label for="pickup_location" class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white">
                                        Titik Temu / Lokasi Pengambilan <span class="text-red-500">*</span>
                                    </label>

                                    <input
                                        id="pickup_location"
                                        type="text"
                                        name="pickup_location"
                                        value="{{ old('pickup_location') }}"
                                        required
                                        placeholder="Contoh: Kantin Utama, Gazebo RPL, Depan Perpustakaan, Lapangan Sekolah, dll."
                                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-emerald-500 dark:focus:ring-emerald-950"
                                    >

                                    @error('pickup_location')
                                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Quick Chips Recommendations --}}
                                <div>
                                    <p class="text-xs text-slate-400">Rekomendasi titik lokasi populer disekitar sekolah:</p>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @foreach(['Kantin', 'Gazebo', 'Depan Perpustakaan', 'Lobby', 'Depan Ruang Guru'] as $locationSpot)
                                            <button
                                                type="button"
                                                onclick="document.getElementById('pickup_location').value = '{{ $locationSpot }}'"
                                                class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700 transition hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                                            >
                                                + {{ $locationSpot }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- Payment Method Selection --}}
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900 sm:p-8">

                            <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                                Metode Pembayaran
                            </h2>

                            <div class="mt-4 grid gap-3 sm:grid-cols-2">

                                <label
                                    id="label-cod"
                                    class="relative flex cursor-pointer items-center justify-between rounded-2xl border border-emerald-500 bg-emerald-50/40 p-4 transition hover:bg-slate-50 dark:border-emerald-600 dark:bg-emerald-950/20 dark:hover:bg-slate-800"
                                >
                                    <div class="flex items-center gap-3">
                                        <input
                                            type="radio"
                                            name="payment_method"
                                            value="cod"
                                            checked
                                            onchange="togglePaymentMethod('cod')"
                                            class="h-4 w-4 text-emerald-600 focus:ring-emerald-500"
                                        >
                                        <div>
                                            <p class="font-bold text-slate-900 dark:text-white text-sm">Bayar di Tempat (COD)</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">Bayar tunai saat barang diterima</p>
                                        </div>
                                    </div>
                                    <span class="text-xl"><i class="fa-solid fa-hand-holding-dollar"></i></span>
                                </label>

                                <label
                                    id="label-qris"
                                    class="relative flex cursor-pointer items-center justify-between rounded-2xl border border-slate-200 p-4 transition hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800"
                                >
                                    <div class="flex items-center gap-3">
                                        <input
                                            type="radio"
                                            name="payment_method"
                                            value="qris"
                                            onchange="togglePaymentMethod('qris')"
                                            class="h-4 w-4 text-emerald-600 focus:ring-emerald-500"
                                        >
                                        <div>
                                            <p class="font-bold text-slate-900 dark:text-white text-sm">QRIS / Non-Tunai</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">Scan QRIS toko penjual langsung</p>
                                        </div>
                                    </div>
                                    <span class="text-xl"><i class="fa-solid fa-qrcode"></i></span>
                                </label>

                            </div>

                            {{-- Dynamic QRIS Display Box --}}
                            <div id="qris-display-box" class="mt-6 hidden rounded-2xl border border-emerald-200 bg-emerald-50/60 p-5 dark:border-emerald-900/50 dark:bg-emerald-950/30">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl"><i class="fa-solid fa-qrcode"></i></span></span>
                                    <div>
                                        <h3 class="font-bold text-slate-900 dark:text-white text-sm">
                                            Barcode QRIS Toko Penjual ({{ $seller?->user?->username ?? 'Seller' }})
                                        </h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">
                                            Silakan scan barcode QRIS di bawah ini untuk melakukan pembayaran non-tunai.
                                        </p>
                                    </div>
                                </div>

                                @if ($seller?->qris_image)
                                    <div class="mt-4 flex flex-col items-center justify-center rounded-2xl bg-white p-5 border border-emerald-100 shadow-xs dark:bg-slate-900 dark:border-slate-800">
                                        <img
                                            src="{{ Storage::url($seller->qris_image) }}"
                                            alt="QRIS Toko {{ $seller->user?->username }}"
                                            class="max-h-72 w-auto rounded-xl object-contain border border-slate-100 shadow-xs dark:border-slate-800"
                                        >
                                        <p class="mt-3 text-xs font-bold text-slate-800 dark:text-white">
                                            Barcode QRIS Resmi: {{ $seller->user?->username ?? 'Seller' }}
                                        </p>
                                        <p class="mt-0.5 text-xs text-emerald-700 dark:text-emerald-400 font-extrabold">
                                            Total Pembayaran: Rp {{ number_format($cart->items->sum(fn ($item) => $item->quantity * $item->price), 0, ',', '.') }}
                                        </p>
                                    </div>
                                @else
                                    <div class="mt-4 rounded-2xl bg-white p-4 text-center border border-amber-200 dark:bg-slate-900 dark:border-slate-800">
                                        <p class="text-xs font-bold text-amber-800 dark:text-amber-400">⚠️ Penjual Belum Mengunggah QRIS</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                            Penjual belum memasukkan gambar QRIS toko. Anda dapat melanjutkan checkout dan meminta nomor rekening / barcode QRIS via WhatsApp setelah pesanan dibuat.
                                        </p>
                                    </div>
                                @endif
                            </div>

                        </div>

                        {{-- Order Note --}}
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900 sm:p-8">

                            <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                                Catatan Tambahan (Opsional)
                            </h2>

                            <div class="mt-4">
                                <textarea
                                    name="note"
                                    rows="3"
                                    placeholder="Contoh: Tolong bawa pesanan pas jam istirahat pertama..."
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                >{{ old('note') }}</textarea>
                            </div>

                        </div>

                    </div>

                    {{-- Right Column: Summary --}}
                    <div>

                        <div class="sticky top-24 rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">

                            <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                                Ringkasan Pesanan
                            </h2>

                            <div class="mt-6 space-y-4 text-sm">

                                <div class="flex justify-between gap-4">
                                    <span class="text-slate-500 dark:text-slate-400">
                                        Total Barang
                                    </span>

                                    <span class="font-bold text-slate-900 dark:text-white">
                                        {{ $cart->items->sum('quantity') }} pcs
                                    </span>
                                </div>

                                <div class="flex justify-between gap-4">
                                    <span class="text-slate-500 dark:text-slate-400">
                                        Subtotal Produk
                                    </span>

                                    <span class="font-bold text-slate-900 dark:text-white">
                                        Rp {{ number_format($cart->items->sum(fn ($item) => $item->quantity * $item->price), 0, ',', '.') }}
                                    </span>
                                </div>

                            </div>

                            <div class="my-6 border-t border-slate-100 dark:border-slate-800"></div>

                            <div class="flex items-end justify-between gap-4">

                                <span class="text-sm font-semibold text-slate-500 dark:text-slate-400">
                                    Total Pembayaran
                                </span>

                                <span class="text-xl font-extrabold text-slate-900 dark:text-white">
                                    Rp {{ number_format($cart->items->sum(fn ($item) => $item->quantity * $item->price), 0, ',', '.') }}
                                </span>

                            </div>

                            <button
                                type="submit"
                                class="mt-6 flex w-full items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3.5 text-sm font-bold text-white shadow-md transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2"
                            >
                                ✦ Buat Pesanan Sekarang
                            </button>

                            <p class="mt-4 text-center text-xs leading-relaxed text-slate-400">
                                Lokasi pengambilan dapat disesuaikan kembali bersama seller setelah pesanan dikonfirmasi.
                            </p>

                        </div>

                    </div>

                </div>

            </form>

        @else

            <x-empty-state
                title="Tidak ada produk untuk checkout"
                message="Keranjang kamu masih kosong. Tambahkan produk terlebih dahulu."
                action="{{ route('products.index') }}"
                actionText="Lihat Produk"
            />

        @endif

    </div>

    @push('scripts')
        <script>
            function togglePaymentMethod(method) {
                const qrisBox = document.getElementById('qris-display-box');
                const labelCod = document.getElementById('label-cod');
                const labelQris = document.getElementById('label-qris');

                if (method === 'qris') {
                    qrisBox.classList.remove('hidden');
                    labelQris.classList.add('border-emerald-500', 'bg-emerald-50/40', 'dark:border-emerald-600', 'dark:bg-emerald-950/20');
                    labelQris.classList.remove('border-slate-200', 'dark:border-slate-700');

                    labelCod.classList.remove('border-emerald-500', 'bg-emerald-50/40', 'dark:border-emerald-600', 'dark:bg-emerald-950/20');
                    labelCod.classList.add('border-slate-200', 'dark:border-slate-700');
                } else {
                    qrisBox.classList.add('hidden');
                    labelCod.classList.add('border-emerald-500', 'bg-emerald-50/40', 'dark:border-emerald-600', 'dark:bg-emerald-950/20');
                    labelCod.classList.remove('border-slate-200', 'dark:border-slate-700');

                    labelQris.classList.remove('border-emerald-500', 'bg-emerald-50/40', 'dark:border-emerald-600', 'dark:bg-emerald-950/20');
                    labelQris.classList.add('border-slate-200', 'dark:border-slate-700');
                }
            }
        </script>
    @endpush

</x-layouts.buyer>