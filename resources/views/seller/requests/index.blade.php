<x-layouts.seller title="Request Kategori">

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-folder-plus text-emerald-600"></i> Request Kategori Produk
                </h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Ajukan kebutuhan kategori produk baru langsung ke Admin Eskasaba Marketplace.
                </p>
            </div>

            <a
                href="{{ route('seller.seller-requests.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-5 py-3 text-sm font-bold text-white shadow-xs transition hover:bg-emerald-800 shrink-0"
            >
                <i class="fa-solid fa-plus"></i> Kirim Request Baru
            </a>
        </div>

        {{-- Info Box --}}
        <div class="rounded-3xl border border-emerald-200 bg-emerald-50/70 p-5 dark:border-emerald-900/50 dark:bg-emerald-950/30">
            <div class="flex items-start gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-emerald-700 text-white shadow-xs">
                    <i class="fa-solid fa-lightbulb text-sm"></i>
                </div>
                <div class="space-y-1 text-xs text-emerald-900 dark:text-emerald-300">
                    <p class="font-bold text-sm">Butuh Kategori Baru untuk Produk Jualanmu?</p>
                    <p class="leading-relaxed opacity-90">
                        Jika kategori produk yang ingin kamu jual belum tersedia di marketplace, kamu cukup memasukkan Nama Kategori yang Dibutuhkan lewat halaman ini. Admin akan meninjau dan menambahkan kategori tersebut untuk toko kamu.
                    </p>
                </div>
            </div>
        </div>

        {{-- Existing Categories Box --}}
        <div x-data="{ search: '' }" class="rounded-3xl border border-slate-200 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900 space-y-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-tags text-emerald-600"></i> Kategori Produk Saat Ini
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Daftar kategori yang sudah aktif. Kategori baru ditandai dengan badge <span class="font-bold text-emerald-600 dark:text-emerald-400">BARU</span> selama 7 hari sejak dibuat.
                    </p>
                </div>

                <div class="relative min-w-[200px]">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                    <input
                        type="text"
                        x-model="search"
                        placeholder="Cari kategori..."
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 pl-9 pr-3 py-2 text-xs font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-emerald-500"
                    >
                </div>
            </div>

            @if (isset($categories) && $categories->isNotEmpty())
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                    @foreach ($categories as $category)
                        <div
                            x-show="!search || '{{ strtolower(addslashes($category->name)) }}'.includes(search.toLowerCase())"
                            class="flex flex-col justify-between rounded-2xl border border-slate-100 bg-slate-50/80 p-3.5 transition hover:border-emerald-300 hover:bg-emerald-50/30 dark:border-slate-800 dark:bg-slate-800/50 dark:hover:border-emerald-700 dark:hover:bg-slate-800"
                        >
                            <div>
                                <div class="flex items-center justify-between gap-1 mb-2">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                        <i class="{{ $category->icon ?: 'fa-solid fa-layer-group' }} text-xs"></i>
                                    </div>

                                    @if ($category->is_new)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-700 px-2 py-0.5 text-[9px] font-black uppercase text-white shadow-2xs tracking-wid" title="Ditambahkan pada {{ $category->created_at?->translatedFormat('d M Y') }}">
                                            <i class="fa-solid fa-sparkles text-[8px]"></i> BARU
                                        </span>
                                    @endif
                                </div>

                                <h4 class="text-xs font-bold text-slate-900 dark:text-white line-clamp-1">
                                    {{ $category->name }}
                                </h4>
                            </div>

                            <div class="mt-2 pt-2 border-t border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between text-[10px] text-slate-400 dark:text-slate-500">
                                <span>{{ $category->products_count ?? 0 }} Produk</span>
                                @if($category->is_new)
                                    <span class="text-emerald-700 font-semibold dark:text-emerald-400">7 Hari</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-4 text-center text-xs text-slate-500 dark:text-slate-400">
                    Belum ada kategori yang tersedia di marketplace.
                </div>
            @endif
        </div>

        {{-- Filter Status Tabs --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
            <a
                href="{{ route('seller.seller-requests.index') }}"
                class="rounded-xl px-4 py-2 text-xs font-bold transition shrink-0 {{ !request('status') ? 'bg-emerald-800 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}"
            >
                Semua Request
            </a>
            <a
                href="{{ route('seller.seller-requests.index', ['status' => 'pending']) }}"
                class="rounded-xl px-4 py-2 text-xs font-bold transition shrink-0 {{ request('status') === 'pending' ? 'bg-emerald-800 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}"
            >
                Menunggu Tinjauan
            </a>
            <a
                href="{{ route('seller.seller-requests.index', ['status' => 'completed']) }}"
                class="rounded-xl px-4 py-2 text-xs font-bold transition shrink-0 {{ request('status') === 'completed' ? 'bg-emerald-800 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}"
            >
                Selesai / Disetujui
            </a>
            <a
                href="{{ route('seller.seller-requests.index', ['status' => 'rejected']) }}"
                class="rounded-xl px-4 py-2 text-xs font-bold transition shrink-0 {{ request('status') === 'rejected' ? 'bg-emerald-800 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}"
            >
                Ditolak
            </a>
        </div>

        {{-- List Requests --}}
        @if ($requests->isEmpty())
            <div class="rounded-3xl border border-dashed border-slate-200 bg-white p-12 text-center dark:border-slate-800 dark:bg-slate-900">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500">
                    <i class="fa-solid fa-inbox text-2xl"></i>
                </div>
                <h3 class="mt-4 text-base font-bold text-slate-900 dark:text-white">Belum Ada Request</h3>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">
                    Anda belum pernah mengirimkan request kategori atau saran ke Admin.
                </p>
                <a
                    href="{{ route('seller.seller-requests.create') }}"
                    class="mt-5 inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-emerald-800"
                >
                    <i class="fa-solid fa-plus"></i> Kirim Request Sekarang
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($requests as $item)
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-xs transition hover:border-slate-300 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-slate-700">
                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div class="space-y-2 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                        {{ $item->typeLabel() }}
                                    </span>

                                    {{-- Status Badge --}}
                                    @if ($item->isCompleted())
                                        <span class="rounded-lg bg-emerald-100 px-2.5 py-1 text-[11px] font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                            <i class="fa-solid fa-circle-check mr-1"></i> Disetujui & Selesai
                                        </span>
                                    @elseif ($item->isRejected())
                                        <span class="rounded-lg bg-rose-100 px-2.5 py-1 text-[11px] font-bold text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">
                                            <i class="fa-solid fa-circle-xmark mr-1"></i> Ditolak
                                        </span>
                                    @else
                                        <span class="rounded-lg bg-amber-100 px-2.5 py-1 text-[11px] font-bold text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                            <i class="fa-solid fa-clock mr-1"></i> Menunggu Tinjauan
                                        </span>
                                    @endif

                                    {{-- Read Receipt Badge --}}
                                    @if ($item->is_read)
                                        <span class="inline-flex items-center gap-1 rounded-lg bg-sky-100 px-2.5 py-1 text-[11px] font-bold text-sky-800 dark:bg-sky-950/60 dark:text-sky-300" title="Dibaca Admin pada {{ $item->read_at?->translatedFormat('d M Y H:i') }}">
                                            <i class="fa-solid fa-check-double text-sky-600 dark:text-sky-400"></i> Sudah Dibaca Admin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1 text-[11px] font-medium text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                            <i class="fa-solid fa-envelope"></i> Belum Dibaca
                                        </span>
                                    @endif
                                </div>

                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
                                    {{ $item->title }}
                                </h3>

                                <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2">
                                    {{ $item->description }}
                                </p>

                                {{-- Balasan Admin Preview jika ada --}}
                                @if ($item->admin_response)
                                    <div class="mt-3 rounded-2xl border border-slate-100 bg-slate-50 p-3 text-xs text-slate-700 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-300">
                                        <p class="font-bold text-emerald-700 dark:text-emerald-400 flex items-center gap-1.5 mb-1">
                                            <i class="fa-solid fa-reply"></i> Balasan Admin:
                                        </p>
                                        <p class="italic">"{{ $item->admin_response }}"</p>
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center justify-between gap-4 border-t border-slate-100 pt-3 md:border-t-0 md:pt-0 shrink-0">
                                <div class="text-right text-[11px] text-slate-400 dark:text-slate-500 hidden sm:block">
                                    <p>Dikirim:</p>
                                    <p class="font-semibold text-slate-600 dark:text-slate-300">{{ $item->created_at->translatedFormat('d M Y, H:i') }}</p>
                                </div>

                                <a
                                    href="{{ route('seller.seller-requests.show', $item) }}"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 shadow-2xs hover:bg-slate-50 hover:text-emerald-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                                >
                                    <span>Detail Request</span> <i class="fa-solid fa-chevron-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="pt-4">
                    {{ $requests->links() }}
                </div>
            </div>
        @endif

    </div>

</x-layouts.seller>
