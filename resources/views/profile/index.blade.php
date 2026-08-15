<x-layouts.app
    title="Profil & Dashboard"
>
    <div class="mx-auto w-full max-w-6xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Header & Action Buttons --}}
        <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <p class="text-sm font-medium text-slate-500">
                    Akun Saya
                </p>

                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    Halo, {{ auth()->user()->username }} 👋
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Kelola profil, lihat statistik belanja, dan pantau pesanan Anda.
                </p>
            </div>

            {{-- Action Buttons: Edit Profil & Logout --}}
            <div class="flex items-center gap-3">
                <a
                    href="{{ route('profile.edit') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    Edit Profil
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-5 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-100 hover:text-red-700"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H2.25" />
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>

        {{-- Top Section: Identity + Stats --}}
        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Profile Card --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col items-center text-center">
                    <div class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-full bg-slate-100 text-3xl font-bold text-slate-700">
                        @if (auth()->user()->avatar)
                            <img
                                src="{{ Storage::url(auth()->user()->avatar) }}"
                                alt="{{ auth()->user()->username }}"
                                class="h-full w-full object-cover"
                            >
                        @else
                            {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                        @endif
                    </div>

                    <h2 class="mt-4 text-xl font-bold text-slate-900">
                        {{ auth()->user()->username }}
                    </h2>

                    <p class="mt-0.5 text-sm font-medium text-slate-500">
                        NIS/NIP: {{ auth()->user()->nis_nip ?? '-' }}
                    </p>

                    <div class="mt-3 flex items-center gap-2">
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                            {{ auth()->user()->role === 'teacher' ? 'Guru' : 'Siswa' }}
                        </span>
                        @if(auth()->user()->class)
                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                {{ auth()->user()->class }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Detail Identitas Ringkas --}}
                <div class="mt-6 border-t border-slate-100 pt-5 space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Email</span>
                        <span class="font-medium text-slate-800">{{ auth()->user()->email ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Jurusan</span>
                        <span class="font-medium text-slate-800">{{ auth()->user()->major ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">No. Telp</span>
                        <span class="font-medium text-slate-800">{{ auth()->user()->phone ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- 4 Stat Cards --}}
            <div class="lg:col-span-2 grid grid-cols-2 gap-4">
                {{-- Total Orders --}}
                <div class="flex flex-col justify-between rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-xl">
                        🛍️
                    </div>
                    <div class="mt-4">
                        <p class="text-sm font-medium text-slate-500">Total Pesanan</p>
                        <p class="mt-1 text-3xl font-bold text-slate-900">{{ $totalOrders ?? 0 }}</p>
                    </div>
                </div>

                {{-- Pending Orders --}}
                <div class="flex flex-col justify-between rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-xl">
                        ⏳
                    </div>
                    <div class="mt-4">
                        <p class="text-sm font-medium text-slate-500">Menunggu Verifikasi</p>
                        <p class="mt-1 text-3xl font-bold text-slate-900">{{ $pendingOrders ?? 0 }}</p>
                    </div>
                </div>

                {{-- Completed Orders --}}
                <div class="flex flex-col justify-between rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-xl">
                        ✅
                    </div>
                    <div class="mt-4">
                        <p class="text-sm font-medium text-slate-500">Pesanan Selesai</p>
                        <p class="mt-1 text-3xl font-bold text-slate-900">{{ $completedOrders ?? 0 }}</p>
                    </div>
                </div>

                {{-- Cart Count --}}
                <div class="flex flex-col justify-between rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-xl">
                        🛒
                    </div>
                    <div class="mt-4">
                        <p class="text-sm font-medium text-slate-500">Item Keranjang</p>
                        <p class="mt-1 text-3xl font-bold text-slate-900">{{ $cartCount ?? 0 }}</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Lower Section: Recent Orders + Seller Status --}}
        <div class="mt-8 grid gap-6 lg:grid-cols-3">

            {{-- Recent Orders (Col 2) --}}
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm lg:col-span-2">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                    <div>
                        <h2 class="font-bold text-slate-900">Pesanan Terbaru</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Aktivitas belanja terakhir Anda.</p>
                    </div>

                    <a
                        href="{{ route('buyer.orders.index') }}"
                        class="text-sm font-semibold text-slate-700 transition hover:text-slate-900"
                    >
                        Lihat semua →
                    </a>
                </div>

                @if (isset($recentOrders) && $recentOrders->count())
                    <div class="divide-y divide-slate-100">
                        @foreach ($recentOrders as $order)
                            <div class="flex items-center justify-between p-5 sm:px-6">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-lg">
                                        📦
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">#{{ $order->order_number ?? $order->id }}</p>
                                        <p class="text-xs text-slate-400">{{ $order->created_at->format('d M Y, H:i') }}</p>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <p class="font-bold text-slate-900">Rp {{ number_format($order->total_amount ?? 0, 0, ',', '.') }}</p>
                                    <span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold
                                        {{ match($order->status) {
                                            'completed' => 'bg-emerald-100 text-emerald-700',
                                            'pending'   => 'bg-amber-100 text-amber-700',
                                            'cancelled' => 'bg-red-100 text-red-700',
                                            default     => 'bg-slate-100 text-slate-700'
                                        } }}">
                                        {{ ucfirst($order->status ?? 'pending') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center">
                        <p class="text-3xl">🛍️</p>
                        <p class="mt-2 text-sm font-medium text-slate-700">Belum ada pesanan</p>
                        <p class="mt-1 text-xs text-slate-400">Pesanan yang Anda buat akan tampil di sini.</p>
                        <a
                            href="{{ route('products.index') }}"
                            class="mt-4 inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white transition hover:bg-slate-800"
                        >
                            Mulai Belanja
                        </a>
                    </div>
                @endif
            </div>

            {{-- Quick Access & Seller Box (Col 1) --}}
            <div class="space-y-6">

                {{-- Status Seller --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Toko Saya</p>
                    <h3 class="mt-1 text-lg font-bold text-slate-900">Status Seller</h3>

                    @if (! auth()->user()->seller)
                        <div class="mt-4 rounded-2xl bg-emerald-50 p-4 text-center">
                            <p class="text-2xl">🏪</p>
                            <p class="mt-1 text-sm font-medium text-emerald-900">Belum Jadi Seller</p>
                            <p class="mt-1 text-xs text-emerald-600">Mulai jual produk buatanmu ke teman-teman sekolah!</p>
                            <a
                                href="{{ route('buyer.apply-seller') }}"
                                class="mt-3 block w-full rounded-xl bg-emerald-600 py-2 text-center text-xs font-semibold text-white transition hover:bg-emerald-700"
                            >
                                ✦ Ajukan Jadi Seller
                            </a>
                        </div>
                    @elseif (auth()->user()->seller->isApproved())
                        <div class="mt-4 rounded-2xl bg-slate-50 p-4 text-center">
                            <p class="text-2xl">✅</p>
                            <p class="mt-1 text-sm font-medium text-slate-900">Seller Aktif</p>
                            <a
                                href="{{ route('seller.dashboard') }}"
                                class="mt-3 block w-full rounded-xl bg-slate-900 py-2 text-center text-xs font-semibold text-white transition hover:bg-slate-800"
                            >
                                Buka Seller Panel →
                            </a>
                        </div>
                    @else
                        @php $seller = auth()->user()->seller; @endphp
                        <div class="mt-4 rounded-2xl bg-amber-50 p-4 text-center">
                            <p class="text-sm font-semibold text-amber-800">Status: {{ $seller->statusLabel() }}</p>
                            @if($seller->needsRevision())
                                <a
                                    href="{{ route('buyer.apply-seller') }}"
                                    class="mt-3 block w-full rounded-xl bg-amber-600 py-2 text-center text-xs font-semibold text-white transition hover:bg-amber-700"
                                >
                                    Perbaiki Pengajuan
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Quick Access Menu --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="font-bold text-slate-900">Akses Cepat</h3>

                    <div class="mt-4 space-y-2 text-sm font-medium">
                        <a href="{{ route('products.index') }}" class="flex items-center justify-between rounded-xl p-3 text-slate-700 transition hover:bg-slate-50">
                            <span class="flex items-center gap-3">🛍️ Katalog Produk</span>
                            <span class="text-slate-400">→</span>
                        </a>
                        <a href="{{ route('buyer.cart.index') }}" class="flex items-center justify-between rounded-xl p-3 text-slate-700 transition hover:bg-slate-50">
                            <span class="flex items-center gap-3">🛒 Keranjang Belanja</span>
                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600">{{ $cartCount ?? 0 }}</span>
                        </a>
                        <a href="{{ route('buyer.orders.index') }}" class="flex items-center justify-between rounded-xl p-3 text-slate-700 transition hover:bg-slate-50">
                            <span class="flex items-center gap-3">📦 Riwayat Pesanan</span>
                            <span class="text-slate-400">→</span>
                        </a>
                        <a href="{{ route('buyer.chats.index') }}" class="flex items-center justify-between rounded-xl p-3 text-slate-700 transition hover:bg-slate-50">
                            <span class="flex items-center gap-3">💬 Chat Seller</span>
                            <span class="text-slate-400">→</span>
                        </a>
                    </div>
                </div>

            </div>

        </div>

    </div>
</x-layouts.app>