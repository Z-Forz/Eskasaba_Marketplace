<x-layouts.seller title="Detail Request">

    <div class="mx-auto max-w-3xl space-y-6">

        {{-- Header Navigation --}}
        <div>
            <a
                href="{{ route('seller.seller-requests.index') }}"
                class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
            >
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Request
            </a>

            <div class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                        Detail Request Seller
                    </h1>
                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                        ID Request: #REQ-{{ str_pad($sellerRequest->id, 5, '0', STR_PAD_LEFT) }} • Dikirim {{ $sellerRequest->created_at->translatedFormat('d F Y, H:i') }} WIB
                    </p>
                </div>

                <div>
                    @if ($sellerRequest->isCompleted())
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3.5 py-1.5 text-xs font-bold text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300">
                            <i class="fa-solid fa-circle-check"></i> Disetujui & Selesai
                        </span>
                    @elseif ($sellerRequest->isRejected())
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-100 px-3.5 py-1.5 text-xs font-bold text-rose-800 dark:bg-rose-950/80 dark:text-rose-300">
                            <i class="fa-solid fa-circle-xmark"></i> Ditolak
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3.5 py-1.5 text-xs font-bold text-amber-800 dark:bg-amber-950/80 dark:text-amber-300">
                            <i class="fa-solid fa-clock"></i> Menunggu Tinjauan Admin
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Status Read Indicator Banner --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl {{ $sellerRequest->is_read ? 'bg-sky-100 text-sky-700 dark:bg-sky-950 dark:text-sky-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }}">
                        <i class="fa-solid {{ $sellerRequest->is_read ? 'fa-check-double text-lg' : 'fa-envelope text-base' }}"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Status Pembacaan Pesan</p>
                        @if ($sellerRequest->is_read)
                            <p class="text-sm font-extrabold text-sky-800 dark:text-sky-300">
                                Sudah Dibaca Admin
                            </p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                Dibaca pada: {{ $sellerRequest->read_at?->translatedFormat('d F Y, H:i') }} WIB
                            </p>
                        @else
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-300">
                                Belum Dibaca oleh Admin
                            </p>
                            <p class="text-[11px] text-slate-400">
                                Pesan sudah masuk ke antrean Admin dan akan segera dibaca.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Card --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900 sm:p-8 space-y-6">

            <div class="border-b border-slate-100 pb-5 dark:border-slate-800">
                <span class="rounded-lg bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    {{ $sellerRequest->typeLabel() }}
                </span>

                <h2 class="mt-3 text-xl font-black text-slate-900 dark:text-white">
                    {{ $sellerRequest->title }}
                </h2>
            </div>

            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Keterangan / Detail Request:</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-800 dark:text-slate-200 whitespace-pre-line bg-slate-50 p-4 rounded-2xl border border-slate-100 dark:bg-slate-800/50 dark:border-slate-800">
                    {{ $sellerRequest->description ?: '-' }}
                </p>
            </div>

            {{-- Response Admin Section --}}
            @if ($sellerRequest->admin_response)
                <div class="rounded-3xl border border-emerald-200 bg-emerald-50/70 p-6 dark:border-emerald-900/50 dark:bg-emerald-950/40">
                    <div class="flex items-center gap-2.5 text-emerald-800 dark:text-emerald-300 font-extrabold text-sm mb-2">
                        <i class="fa-solid fa-comments text-base"></i> Balasan & Konfirmasi dari Admin
                    </div>

                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">
                        Diproses pada {{ $sellerRequest->completed_at?->translatedFormat('d F Y, H:i') ?? $sellerRequest->updated_at->translatedFormat('d F Y, H:i') }} WIB
                    </p>

                    <div class="rounded-2xl bg-white p-4 text-sm leading-relaxed text-slate-800 dark:bg-slate-900 dark:text-slate-100 border border-emerald-100 dark:border-emerald-900/40 shadow-2xs font-medium">
                        "{{ $sellerRequest->admin_response }}"
                    </div>
                </div>
            @endif

        </div>

    </div>

</x-layouts.seller>
