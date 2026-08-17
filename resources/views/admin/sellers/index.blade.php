<x-layouts.admin title="Kelola Seller">

    <div class="space-y-6">

        {{-- Header Section --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    @if ($status === 'approved')
                        <i class="fa-solid fa-store text-emerald-600"></i> Daftar Seller Aktif
                    @elseif ($status === 'pending')
                        <i class="fa-solid fa-hourglass-half text-blue-600"></i> Verifikasi Pengajuan Seller
                    @elseif ($status === 'revision')
                        <i class="fa-solid fa-rotate-left text-amber-600"></i> Seller Perlu Revisi
                    @elseif ($status === 'rejected')
                        <i class="fa-solid fa-ban text-red-600"></i> Seller Ditolak
                    @else
                        <i class="fa-solid fa-users-gear text-slate-700"></i> Seluruh Data Seller
                    @endif
                </h1>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    @if ($status === 'approved')
                        Menampilkan seluruh penjual terverifikasi yang sedang aktif di Eskasaba Marketplace.
                    @elseif ($status === 'pending')
                        Daftar pengajuan seller baru yang menunggu verifikasi dari admin.
                    @else
                        Kelola data seller dan status verifikasi marketplace.
                    @endif
                </p>
            </div>

            <a
                href="{{ route('admin.sellers.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-5 py-3 text-sm font-bold text-white shadow-xs transition hover:bg-emerald-800"
            >
                <i class="fa-solid fa-plus"></i> Tambah Seller Manual
            </a>
        </div>

        @if (session('success'))
            <x-alert type="success" :message="session('success')" class="mb-4" />
        @endif



        {{-- Table Container --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900">

            @if ($sellers->count())

                <div class="hidden overflow-x-auto md:block">

                    <table class="w-full text-left text-sm">

                        <thead class="border-b border-slate-100 bg-slate-50/80 text-xs font-bold uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:bg-slate-800/80 dark:text-slate-400">

                            <tr>
                                <th class="px-6 py-4">Seller / Pengguna</th>
                                <th class="px-6 py-4">Identitas Sekolah</th>
                                <th class="px-6 py-4">WhatsApp Toko</th>
                                <th class="px-6 py-4">Jumlah Produk</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>

                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">

                            @foreach ($sellers as $seller)

                                <tr class="transition hover:bg-slate-50/60 dark:hover:bg-slate-800/50">

                                    {{-- Seller User --}}
                                    <td class="px-6 py-4">

                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-800 text-sm font-bold text-white shadow-xs">
                                                {{ strtoupper(substr($seller->user?->username ?? 'S', 0, 1)) }}
                                            </div>

                                            <div class="min-w-0">
                                                <div class="font-bold text-slate-900 dark:text-white">
                                                    {{ $seller->user?->username ?? '-' }}
                                                </div>

                                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                                    {{ $seller->user?->email ?? '-' }}
                                                </div>
                                            </div>
                                        </div>

                                    </td>

                                    {{-- School Identity --}}
                                    <td class="px-6 py-4 text-xs">
                                        @if ($seller->user?->nis_nip)
                                            <p class="font-bold text-slate-800 dark:text-slate-200">
                                                NIS/NIP: {{ $seller->user->nis_nip }}
                                            </p>
                                            <p class="text-slate-500">
                                                {{ $seller->user->class ?? '-' }} {{ $seller->user->major ? '• ' . $seller->user->major : '' }}
                                            </p>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>

                                    {{-- WhatsApp --}}
                                    <td class="px-6 py-4 text-xs">
                                        @if ($seller->whatsapp_number)
                                            <a
                                                href="https://wa.me/{{ preg_replace('/\D/', '', $seller->whatsapp_number) }}"
                                                target="_blank"
                                                class="inline-flex items-center gap-1.5 font-bold text-emerald-700 hover:underline dark:text-emerald-400"
                                            >
                                                <i class="fa-brands fa-whatsapp text-sm"></i> {{ $seller->whatsapp_number }}
                                            </a>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>

                                    {{-- Product Count --}}
                                    <td class="px-6 py-4 text-xs">
                                        <span class="inline-flex items-center gap-1 rounded-xl bg-slate-100 px-3 py-1 font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                            <i class="fa-solid fa-boxes-stacked text-xs text-slate-500"></i> {{ number_format($seller->products_count ?? 0) }} Produk
                                        </span>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-4">

                                        <span class="rounded-full px-3 py-1 text-xs font-bold
                                            {{ $seller->status === 'approved'
                                                ? 'bg-green-100 text-green-700 dark:bg-green-950/40 dark:text-green-400'
                                                : ($seller->status === 'rejected'
                                                    ? 'bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-400'
                                                    : ($seller->status === 'revision'
                                                        ? 'bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400'
                                                        : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-950/40 dark:text-yellow-400')) }}"
                                        >
                                            {{ $seller->statusLabel() }}
                                        </span>

                                    </td>

                                    {{-- Action --}}
                                    <td class="px-6 py-4 text-right">

                                        <a
                                            href="{{ route('admin.sellers.show', $seller) }}"
                                            class="inline-flex items-center gap-1.5 rounded-2xl bg-slate-900 px-4 py-2 text-xs font-bold text-white transition hover:bg-slate-800 dark:bg-emerald-700 dark:hover:bg-emerald-800 shadow-xs"
                                        >
                                            <i class="fa-solid fa-user-check"></i> Detail & Verifikasi
                                        </a>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                {{-- Mobile List --}}
                <div class="divide-y divide-slate-100 md:hidden dark:divide-slate-800">

                    @foreach ($sellers as $seller)

                        <div class="space-y-4 p-5">

                            <div class="flex items-center justify-between gap-3">

                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-800 text-sm font-bold text-white shadow-xs">
                                        {{ strtoupper(substr($seller->user?->username ?? 'S', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h2 class="font-bold text-slate-900 dark:text-white">
                                            {{ $seller->user?->username ?? '-' }}
                                        </h2>
                                        <p class="truncate text-xs text-slate-500 dark:text-slate-400">
                                            {{ $seller->user?->email ?? '-' }}
                                        </p>
                                    </div>
                                </div>

                                <span class="shrink-0 rounded-full px-3 py-1 text-xs font-bold
                                    {{ $seller->status === 'approved'
                                        ? 'bg-green-100 text-green-700'
                                        : ($seller->status === 'rejected'
                                            ? 'bg-red-100 text-red-700'
                                            : ($seller->status === 'revision'
                                                ? 'bg-amber-100 text-amber-700'
                                                : 'bg-yellow-100 text-yellow-700')) }}"
                                >
                                    {{ $seller->statusLabel() }}
                                </span>

                            </div>

                            <div class="grid grid-cols-2 gap-2 text-xs text-slate-500">
                                <div>
                                    <span class="text-slate-400">WA:</span> {{ $seller->whatsapp_number ?: '-' }}
                                </div>
                                <div>
                                    <span class="text-slate-400">Produk:</span> {{ number_format($seller->products_count ?? 0) }} Item
                                </div>
                            </div>

                            <div>
                                <a
                                    href="{{ route('admin.sellers.show', $seller) }}"
                                    class="flex items-center justify-center gap-2 rounded-2xl bg-slate-900 py-2.5 text-xs font-bold text-white shadow-xs dark:bg-emerald-700"
                                >
                                    <i class="fa-solid fa-user-check"></i> Detail & Verifikasi
                                </a>
                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="p-12 text-center">

                    <div class="text-5xl text-slate-300 mb-3"><i class="fa-solid fa-store"></i></div>

                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                        @if ($status === 'approved')
                            Belum Ada Seller Aktif
                        @elseif ($status === 'pending')
                            Tidak Ada Pengajuan Seller Pending
                        @else
                            Data Seller Kosong
                        @endif
                    </h2>

                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        @if ($status === 'approved')
                            Seller yang disetujui admin akan tampil secara otomatis di sini.
                        @else
                            Tidak ada seller dalam kategori filter ini.
                        @endif
                    </p>

                </div>

            @endif

        </div>

        @if (method_exists($sellers, 'links'))
            {{ $sellers->links() }}
        @endif

    </div>

</x-layouts.admin>
