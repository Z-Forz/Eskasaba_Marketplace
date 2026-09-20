<x-layouts.admin title="Tinjau Request Seller">

    <div class="mx-auto max-w-4xl space-y-6">

        {{-- Navigation Header --}}
        <div>
            <a
                href="{{ route('admin.seller-requests.index', request()->query()) }}"
                class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
            >
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Request Seller
            </a>

            <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-folder-plus text-emerald-600"></i> Tinjau Request Seller
                    </h1>
                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                        ID Request: #REQ-{{ str_pad($sellerRequest->id, 5, '0', STR_PAD_LEFT) }} • Masuk {{ $sellerRequest->created_at->translatedFormat('d F Y, H:i') }} WIB
                    </p>
                </div>

                <div>
                    @if ($sellerRequest->isCompleted())
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-4 py-1.5 text-xs font-bold text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300">
                            <i class="fa-solid fa-circle-check"></i> Selesai / Disetujui
                        </span>
                    @elseif ($sellerRequest->isRejected())
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-100 px-4 py-1.5 text-xs font-bold text-rose-800 dark:bg-rose-950/80 dark:text-rose-300">
                            <i class="fa-solid fa-circle-xmark"></i> Ditolak
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-4 py-1.5 text-xs font-bold text-amber-800 dark:bg-amber-950/80 dark:text-amber-300">
                            <i class="fa-solid fa-clock"></i> Pending Konfirmasi
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-3">

            {{-- Detail Seller Card --}}
            <div class="md:col-span-1 space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900 space-y-4">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Informasi Seller</h3>

                    <div>
                        <p class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-store text-emerald-600"></i> {{ $sellerRequest->seller?->user?->username ?? 'Unknown' }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            NIS/NIP: {{ $sellerRequest->seller?->user?->nis_nip ?? '-' }}
                        </p>
                    </div>

                    <div class="border-t border-slate-100 pt-3 dark:border-slate-800 text-xs space-y-2">
                        <div>
                            <span class="text-slate-400 block">Nomor WhatsApp Toko:</span>
                            <a
                                href="https://wa.me/{{ preg_replace('/[^0-9]/', '', \App\Services\WhatsAppService::formatPhoneNumber($sellerRequest->seller?->whatsapp_number ?: $sellerRequest->seller?->user?->phone)) }}"
                                target="_blank"
                                class="font-bold text-emerald-700 hover:underline dark:text-emerald-400 inline-flex items-center gap-1"
                            >
                                <i class="fa-brands fa-whatsapp text-emerald-600"></i> {{ $sellerRequest->seller?->whatsapp_number ?: ($sellerRequest->seller?->user?->phone ?: '-') }}
                            </a>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-3 dark:border-slate-800 text-xs">
                        <span class="text-slate-400 block">Status Pembacaan Pesan:</span>
                        <span class="font-bold text-sky-700 dark:text-sky-300 inline-flex items-center gap-1 mt-1">
                            <i class="fa-solid fa-check-double text-sky-600"></i> Sudah Dibaca pada {{ $sellerRequest->read_at?->translatedFormat('d M, H:i') }} WIB
                        </span>
                    </div>
                </div>

                {{-- Quick Link to Add Category if it's a category request --}}
                @if ($sellerRequest->type === 'category')
                    <div class="rounded-3xl border border-emerald-200 bg-emerald-50/80 p-5 dark:border-emerald-900/50 dark:bg-emerald-950/30 text-xs text-emerald-900 dark:text-emerald-300 space-y-3">
                        <p class="font-extrabold text-sm flex items-center gap-1.5">
                            <i class="fa-solid fa-layer-group text-emerald-600"></i> Tambah Kategori Baru
                        </p>
                        <p class="leading-relaxed opacity-90">
                            Klik tombol di bawah ini untuk langsung menuju halaman penambahan kategori baru di sistem.
                        </p>
                        <a
                            href="{{ route('admin.categories.create', ['name' => $sellerRequest->title]) }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-4 py-2.5 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-800"
                        >
                            <i class="fa-solid fa-plus-circle"></i> Buka Form Tambah Kategori
                        </a>
                    </div>
                @endif
            </div>

            {{-- Request Content & Action Form --}}
            <div class="md:col-span-2 space-y-6">

                {{-- Detail Content --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="rounded-md bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            {{ $sellerRequest->typeLabel() }}
                        </span>
                    </div>

                    <div>
                        <h2 class="text-xl font-black text-slate-900 dark:text-white">
                            {{ $sellerRequest->title }}
                        </h2>
                    </div>

                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Keterangan / Detail Request:</h4>
                        <p class="mt-2 text-sm leading-relaxed text-slate-800 dark:text-slate-200 whitespace-pre-line bg-slate-50 p-4 rounded-2xl border border-slate-100 dark:bg-slate-800/50 dark:border-slate-800">
                            {{ $sellerRequest->description ?: '-' }}
                        </p>
                    </div>

                    @if ($sellerRequest->admin_response)
                        <div class="border-t border-slate-100 pt-4 dark:border-slate-800">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Catatan Balasan Admin Saat Ini:</h4>
                            <div class="mt-2 rounded-2xl bg-emerald-50 p-4 text-xs font-medium text-emerald-900 border border-emerald-100 dark:bg-emerald-950/40 dark:border-emerald-900/40 dark:text-emerald-200">
                                "{{ $sellerRequest->admin_response }}"
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Admin Action Form --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900 space-y-6">
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-reply text-emerald-600"></i> Tanggapi & Update Status Request
                    </h3>

                    {{-- Form Konfirmasi / Setujui --}}
                    <form
                        method="POST"
                        action="{{ route('admin.seller-requests.confirm', $sellerRequest) }}"
                        x-data="{ isLoading: false }"
                        @submit="if (isLoading) { $event.preventDefault(); return false; } isLoading = true;"
                        :class="{ 'pointer-events-none opacity-80': isLoading }"
                        class="space-y-4"
                    >
                        @csrf
                        <div>
                            <label for="admin_response_confirm" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                Pesan Balasan / Catatan Konfirmasi (Disetujui)
                            </label>
                            <textarea
                                id="admin_response_confirm"
                                name="admin_response"
                                rows="3"
                                placeholder="Tuliskan catatan konfirmasi untuk seller..."
                                class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-900 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >{{ old('admin_response', $sellerRequest->admin_response ?: ($sellerRequest->type === 'category' ? "Kategori \"{$sellerRequest->title}\" telah ditambahkan ke sistem. Silakan gunakan kategori tersebut saat mengunggah atau mengedit produk Anda." : "Request Anda telah diproses dan disetujui oleh Admin.")) }}</textarea>
                        </div>

                        <button
                            type="submit"
                            :disabled="isLoading"
                            :class="{ 'opacity-70 cursor-not-allowed pointer-events-none': isLoading }"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-5 py-3 text-xs font-extrabold text-white shadow-xs transition hover:bg-emerald-800 cursor-pointer"
                        >
                            <span x-show="!isLoading" class="inline-flex items-center gap-2">
                                <i class="fa-solid fa-circle-check"></i> Konfirmasi & Tandai Selesai
                            </span>
                            <span x-show="isLoading" style="display: none;" class="inline-flex items-center gap-2">
                                <i class="fa-solid fa-circle-notch fa-spin"></i> Memproses...
                            </span>
                        </button>
                    </form>

                    <div class="border-t border-slate-100 dark:border-slate-800 pt-4">
                        <form
                            method="POST"
                            action="{{ route('admin.seller-requests.reject', $sellerRequest) }}"
                            x-data="{ isLoading: false }"
                            @submit="if (isLoading) { $event.preventDefault(); return false; } isLoading = true;"
                            :class="{ 'pointer-events-none opacity-80': isLoading }"
                            class="space-y-3"
                        >
                            @csrf
                            <div>
                                <label for="admin_response_reject" class="block text-xs font-bold text-rose-700 dark:text-rose-400">
                                    Atau Tolak Request dengan Alasan:
                                </label>
                                <input
                                    type="text"
                                    id="admin_response_reject"
                                    name="admin_response"
                                    placeholder="Contoh: Kategori tersebut sudah terwakili oleh kategori X / Kategori tidak diperbolehkan."
                                    class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs text-slate-900 outline-none focus:border-rose-500 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                />
                            </div>

                            <button
                                type="submit"
                                :disabled="isLoading"
                                :class="{ 'opacity-70 cursor-not-allowed pointer-events-none': isLoading }"
                                class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100 dark:border-rose-900/50 dark:bg-rose-950/40 dark:text-rose-300 dark:hover:bg-rose-900/60 cursor-pointer"
                            >
                                <span x-show="!isLoading" class="inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-xmark"></i> Tolak Request Ini
                                </span>
                                <span x-show="isLoading" style="display: none;" class="inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-notch fa-spin"></i> Memproses...
                                </span>
                            </button>
                        </form>
                    </div>
                </div>

            </div>

        </div>

    </div>

</x-layouts.admin>
