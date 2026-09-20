<x-layouts.admin title="Kelola Request Seller">

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-folder-plus text-emerald-600"></i> Request Seller (Kategori)
                </h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Kelola dan tanggapi permohonan kategori baru atau usulan fitur dari para penjual.
                </p>
            </div>

            <a
                href="{{ route('admin.categories.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-5 py-3 text-sm font-bold text-white shadow-xs transition hover:bg-emerald-800 shrink-0"
            >
                <i class="fa-solid fa-plus"></i> Tambah Kategori Baru
            </a>
        </div>

        {{-- Filter Tabs --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
            <a
                href="{{ route('admin.seller-requests.index') }}"
                class="rounded-xl px-4 py-2 text-xs font-bold transition shrink-0 {{ !request('unread') && !request('status') ? 'bg-emerald-800 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}"
            >
                Semua Request
            </a>
            <a
                href="{{ route('admin.seller-requests.index', ['unread' => '1']) }}"
                class="flex items-center gap-1.5 rounded-xl px-4 py-2 text-xs font-bold transition shrink-0 {{ request('unread') === '1' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}"
            >
                <span>Belum Dibaca</span>
                @if ($unreadCount > 0)
                    <span class="rounded-full bg-amber-500 px-2 py-0.2 text-[10px] text-white font-black">
                        {{ $unreadCount }}
                    </span>
                @endif
            </a>
            <a
                href="{{ route('admin.seller-requests.index', ['status' => 'pending']) }}"
                class="rounded-xl px-4 py-2 text-xs font-bold transition shrink-0 {{ request('status') === 'pending' ? 'bg-emerald-800 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}"
            >
                Menunggu Konfirmasi ({{ $pendingCount }})
            </a>
            <a
                href="{{ route('admin.seller-requests.index', ['status' => 'completed']) }}"
                class="rounded-xl px-4 py-2 text-xs font-bold transition shrink-0 {{ request('status') === 'completed' ? 'bg-emerald-800 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}"
            >
                Selesai
            </a>
            <a
                href="{{ route('admin.seller-requests.index', ['status' => 'rejected']) }}"
                class="rounded-xl px-4 py-2 text-xs font-bold transition shrink-0 {{ request('status') === 'rejected' ? 'bg-emerald-800 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}"
            >
                Ditolak
            </a>
        </div>

        {{-- Table Container --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white dark:border-slate-800 dark:bg-slate-900 shadow-xs">

            @if ($requests->count())

                {{-- DESKTOP TABLE --}}
                <div class="hidden overflow-x-auto md:block">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-slate-100 bg-slate-50/80 text-xs font-bold uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:bg-slate-800/80 dark:text-slate-400">
                            <tr>
                                <th class="px-6 py-4">Seller Pendaftar</th>
                                <th class="px-6 py-4">Nama Kategori / Request</th>
                                <th class="px-6 py-4">Status & Keterbacaan</th>
                                <th class="px-6 py-4">Tanggal Kirim</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($requests as $item)
                                <tr class="transition hover:bg-slate-50/60 dark:hover:bg-slate-800/50 {{ !$item->is_read ? 'bg-amber-50/40 dark:bg-amber-950/10' : '' }}">
                                    {{-- Seller User --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-emerald-800 text-xs font-bold text-white shadow-xs">
                                                {{ strtoupper(substr($item->seller?->user?->username ?? 'S', 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-extrabold text-slate-900 dark:text-white truncate">
                                                    {{ $item->seller?->user?->username ?? '-' }}
                                                </div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                                    WA: {{ $item->seller?->whatsapp_number ?: ($item->seller?->user?->phone ?: '-') }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Request Title & Type --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                {{ $item->typeLabel() }}
                                            </span>
                                        </div>
                                        <div class="font-bold text-slate-900 dark:text-white">
                                            {{ $item->title }}
                                        </div>
                                        @if ($item->description && $item->description !== '-')
                                            <div class="text-xs text-slate-400 truncate max-w-xs mt-0.5">
                                                {{ $item->description }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Combined Status & Read Receipt --}}
                                    <td class="px-6 py-4">
                                        <div class="space-y-1">
                                            <div>
                                                @if ($item->isCompleted())
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-bold text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300">
                                                        <i class="fa-solid fa-circle-check text-[10px]"></i> Disetujui
                                                    </span>
                                                @elseif ($item->isRejected())
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-2.5 py-0.5 text-[11px] font-bold text-rose-800 dark:bg-rose-950/80 dark:text-rose-300">
                                                        <i class="fa-solid fa-circle-xmark text-[10px]"></i> Ditolak
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-[11px] font-bold text-amber-800 dark:bg-amber-950/80 dark:text-amber-300">
                                                        <i class="fa-solid fa-clock text-[10px]"></i> Pending
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="text-[11px]">
                                                @if ($item->is_read)
                                                    <span class="text-sky-700 dark:text-sky-400 font-semibold inline-flex items-center gap-1" title="Dibaca pada {{ $item->read_at?->translatedFormat('d M Y, H:i') }}">
                                                        <i class="fa-solid fa-check-double text-[10px]"></i> Dibaca Admin
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 rounded-md bg-amber-500 px-2 py-0.5 text-[10px] font-extrabold text-white animate-pulse">
                                                        <i class="fa-solid fa-envelope text-[9px]"></i> Belum Dibaca
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Created At --}}
                                    <td class="px-6 py-4 text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                        {{ $item->created_at->translatedFormat('d M Y, H:i') }}
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-6 py-4 text-right">
                                        <a
                                            href="{{ route('admin.seller-requests.show', array_merge(['sellerRequest' => $item->id], request()->query())) }}"
                                            class="inline-flex items-center gap-1.5 rounded-xl bg-slate-100 px-3.5 py-2 text-xs font-bold text-slate-700 transition hover:bg-emerald-700 hover:text-white dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-emerald-700"
                                        >
                                            <i class="fa-solid fa-eye"></i> Tinjau
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE CARD LIST --}}
                <div class="divide-y divide-slate-100 dark:divide-slate-800 md:hidden">
                    @foreach ($requests as $item)
                        <div class="p-4 space-y-3 {{ !$item->is_read ? 'bg-amber-50/40 dark:bg-amber-950/10' : '' }}">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-emerald-800 text-xs font-bold text-white">
                                        {{ strtoupper(substr($item->seller?->user?->username ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-extrabold text-slate-900 dark:text-white">
                                            {{ $item->seller?->user?->username ?? '-' }}
                                        </p>
                                        <p class="text-[10px] text-slate-400">
                                            WA: {{ $item->seller?->whatsapp_number ?: ($item->seller?->user?->phone ?: '-') }}
                                        </p>
                                    </div>
                                </div>

                                <div>
                                    @if ($item->isCompleted())
                                        <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-bold text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300">
                                            Disetujui
                                        </span>
                                    @elseif ($item->isRejected())
                                        <span class="rounded-full bg-rose-100 px-2.5 py-0.5 text-[10px] font-bold text-rose-800 dark:bg-rose-950/80 dark:text-rose-300">
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-[10px] font-bold text-amber-800 dark:bg-amber-950/80 dark:text-amber-300">
                                            Pending
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="space-y-1">
                                <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                    {{ $item->typeLabel() }}
                                </span>
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white">
                                    {{ $item->title }}
                                </h4>
                                @if ($item->description && $item->description !== '-')
                                    <p class="text-xs text-slate-500 line-clamp-2">
                                        {{ $item->description }}
                                    </p>
                                @endif
                            </div>

                            <div class="flex items-center justify-between border-t border-slate-100 pt-2.5 dark:border-slate-800 text-[11px]">
                                <div>
                                    @if ($item->is_read)
                                        <span class="text-sky-700 font-semibold flex items-center gap-1">
                                            <i class="fa-solid fa-check-double"></i> Dibaca Admin
                                        </span>
                                    @else
                                        <span class="rounded-md bg-amber-500 px-2 py-0.5 text-[10px] font-extrabold text-white animate-pulse">
                                            Belum Dibaca
                                        </span>
                                    @endif
                                </div>

                                <a
                                    href="{{ route('admin.seller-requests.show', array_merge(['sellerRequest' => $item->id], request()->query())) }}"
                                    class="inline-flex items-center gap-1 rounded-xl bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-emerald-700 hover:text-white dark:bg-slate-800 dark:text-slate-200"
                                >
                                    <span>Tinjau</span> <i class="fa-solid fa-chevron-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-slate-100 p-4 dark:border-slate-800">
                    {{ $requests->links() }}
                </div>

            @else

                <div class="p-12 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-slate-800">
                        <i class="fa-solid fa-inbox text-2xl"></i>
                    </div>
                    <p class="mt-4 text-sm font-bold text-slate-700 dark:text-slate-300">Belum Ada Request dari Seller</p>
                    <p class="text-xs text-slate-400 mt-1">Request baru dari seller akan tampil di halaman ini.</p>
                </div>

            @endif

        </div>

    </div>

</x-layouts.admin>
