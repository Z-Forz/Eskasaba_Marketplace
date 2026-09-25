<x-layouts.buyer title="Beri Ulasan Produk">

    <div class="mx-auto w-full max-w-3xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <a
                href="{{ route('buyer.orders.show', $order) }}"
                class="text-xs font-bold text-emerald-700 hover:text-emerald-800 transition flex items-center gap-1"
            >
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Detail Pesanan
            </a>

            <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-900 dark:text-white sm:text-3xl flex items-center gap-2">
                <i class="fa-solid fa-star text-amber-400"></i> Beri Ulasan & Rating Produk
            </h1>

            <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">
                Bagikan pengalaman kamu mengenai produk pesanan <strong>{{ $order->invoice_number }}</strong>.
            </p>
        </div>

        @if(session('error'))
            <x-alert type="error" :message="session('error')" class="mb-6" />
        @endif

        @if(session('success'))
            <x-alert type="success" :message="session('success')" class="mb-6" />
        @endif

        {{-- Order Items Review Forms --}}
        <div class="space-y-6">

            @foreach($order->items as $item)

                @php
                    $existingReview = \App\Models\Review::where('order_id', $order->id)
                        ->where('product_id', $item->product_id)
                        ->where('user_id', auth()->id())
                        ->first();
                @endphp

                <div
                    x-data="{ showDeleteModal: false, showImageModal: false }"
                    class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900"
                >

                    {{-- Product Info Header --}}
                    <div class="flex items-center gap-4 border-b border-slate-100 pb-4 dark:border-slate-800">
                        <div class="h-16 w-16 shrink-0 overflow-hidden rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700">
                            @if($item->product?->images?->first())
                                <img
                                    src="{{ Storage::url($item->product->images->first()->image) }}"
                                    alt="{{ $item->product_name }}"
                                    class="h-full w-full object-cover"
                                >
                            @else
                                <div class="flex h-full w-full items-center justify-center text-slate-400">
                                    <i class="fa-solid fa-store text-xl opacity-40 text-emerald-600"></i>
                                </div>
                            @endif
                        </div>

                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-base">
                                {{ $item->product_name ?? $item->product?->name }}
                            </h3>

                            @if($item->note)
                                <p class="mt-0.5 inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                    <i class="fa-solid fa-tag text-[10px]"></i> Varian: {{ $item->note }}
                                </p>
                            @endif
                        </div>
                    </div>

                    @if($existingReview)

                        <div class="mt-4 rounded-2xl bg-slate-50 p-4 border border-slate-200 dark:bg-slate-800/60 dark:border-slate-800">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-1.5 text-xs font-extrabold text-emerald-700 dark:text-emerald-400 mb-2">
                                        <i class="fa-solid fa-circle-check text-emerald-600"></i> Ulasan Sudah Dikirim
                                    </div>
                                    <div class="flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fa-solid fa-star text-sm {{ $i <= $existingReview->rating ? 'text-amber-400' : 'text-slate-200 dark:text-slate-700' }}"></i>
                                        @endfor
                                        <span class="ml-1.5 text-xs font-bold text-amber-600 dark:text-amber-400">
                                            {{ $existingReview->rating }}/5
                                        </span>
                                    </div>
                                    @if($existingReview->comment)
                                        <p class="mt-2 text-xs text-slate-600 dark:text-slate-300 italic">
                                            "{{ $existingReview->comment }}"
                                        </p>
                                    @endif

                                    @if($existingReview->image)
                                        <div class="mt-3">
                                            <button
                                                type="button"
                                                @click="showImageModal = true"
                                                class="group relative inline-block overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700 shadow-2xs transition hover:opacity-90 cursor-pointer"
                                            >
                                                <img
                                                    src="{{ Storage::url($existingReview->image) }}"
                                                    alt="Foto Ulasan"
                                                    class="h-16 w-16 object-cover"
                                                >
                                                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs">
                                                    <i class="fa-solid fa-magnifying-glass-plus"></i>
                                                </div>
                                            </button>
                                        </div>

                                        {{-- Lightbox Modal --}}
                                        <template x-teleport="body">
                                            <div
                                                x-show="showImageModal"
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0"
                                                x-transition:enter-end="opacity-100"
                                                x-transition:leave="transition ease-in duration-150"
                                                x-transition:leave-start="opacity-100"
                                                x-transition:leave-end="opacity-0"
                                                class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-xs"
                                                @keydown.escape.window="showImageModal = false"
                                            >
                                                <div class="relative max-w-3xl w-full max-h-[90vh] flex flex-col items-center">
                                                    <button
                                                        type="button"
                                                        @click="showImageModal = false"
                                                        class="absolute -top-12 right-0 flex h-10 w-10 items-center justify-center rounded-full bg-white/20 text-white hover:bg-white/30 transition cursor-pointer"
                                                    >
                                                        <i class="fa-solid fa-xmark text-lg"></i>
                                                    </button>
                                                    <img
                                                        src="{{ Storage::url($existingReview->image) }}"
                                                        alt="Foto Ulasan Full"
                                                        class="max-h-[80vh] w-auto max-w-full rounded-2xl object-contain shadow-2xl border border-white/20"
                                                    >
                                                </div>
                                            </div>
                                        </template>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-200 dark:border-slate-700">
                                    <a
                                        href="{{ route('buyer.reviews.edit', $existingReview) }}"
                                        class="inline-flex items-center gap-1.5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-800 hover:bg-emerald-100 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300 transition"
                                    >
                                        <i class="fa-solid fa-pen-to-square text-emerald-600"></i> Edit
                                    </a>

                                    <button
                                        type="button"
                                        @click="showDeleteModal = true"
                                        class="inline-flex items-center gap-1.5 rounded-2xl border border-red-200 bg-red-50 px-4 py-2 text-xs font-bold text-red-700 hover:bg-red-100 dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-300 transition cursor-pointer"
                                    >
                                        <i class="fa-solid fa-trash-can text-red-600"></i> Hapus
                                    </button>

                                    {{-- Delete Confirmation Modal --}}
                                    <template x-teleport="body">
                                        <div
                                            x-show="showDeleteModal"
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0"
                                            x-transition:enter-end="opacity-100"
                                            x-transition:leave="transition ease-in duration-150"
                                            x-transition:leave-start="opacity-100"
                                            x-transition:leave-end="opacity-0"
                                            class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs"
                                            @keydown.escape.window="showDeleteModal = false"
                                        >
                                            <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-red-100 text-red-600 dark:bg-red-950/60 dark:text-red-400">
                                                    <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                                                </div>
                                                <h3 class="mt-4 text-center text-lg font-black text-slate-900 dark:text-white">
                                                    Hapus Ulasan?
                                                </h3>
                                                <p class="mt-2 text-center text-xs text-slate-500 dark:text-slate-400">
                                                    Apakah kamu yakin ingin menghapus ulasan ini? Tindakan ini tidak dapat dibatalkan.
                                                </p>
                                                <div class="mt-6 flex items-center justify-end gap-3">
                                                    <button
                                                        type="button"
                                                        @click="showDeleteModal = false"
                                                        class="rounded-2xl border border-slate-200 px-5 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 transition"
                                                    >
                                                        Batal
                                                    </button>
                                                    <form action="{{ route('buyer.reviews.destroy', $existingReview) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button
                                                            type="submit"
                                                            class="rounded-2xl bg-red-600 px-5 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-red-700 transition cursor-pointer"
                                                        >
                                                            Hapus Ulasan
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                    @else

                        <form
                            action="{{ route('buyer.reviews.store') }}"
                            method="POST"
                            enctype="multipart/form-data"
                            class="mt-5 space-y-5"
                            x-data="{
                                currentRating: 5,
                                previewUrl: null,
                                fileName: null,
                                handleFileChange(event) {
                                    const file = event.target.files[0];
                                    if (file) {
                                        this.previewUrl = URL.createObjectURL(file);
                                        this.fileName = file.name;
                                    }
                                }
                            }"
                        >
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                            <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                            <input type="hidden" name="rating" x-model="currentRating">

                            {{-- Star Rating Selection --}}
                            <div>
                                <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Rating Kepuasan (1 - 5 Bintang)
                                </label>

                                <div class="flex items-center gap-2">
                                    <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                        <button
                                            type="button"
                                            @click="currentRating = star"
                                            class="flex h-11 w-11 items-center justify-center rounded-2xl border transition cursor-pointer"
                                            :class="currentRating >= star
                                                ? 'border-amber-400 bg-amber-50 text-amber-500 font-black dark:bg-amber-950/50'
                                                : 'border-slate-200 bg-slate-50 text-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-600'"
                                        >
                                            <i class="fa-solid fa-star text-lg"></i>
                                        </button>
                                    </template>

                                    <span class="ml-2 text-xs font-extrabold text-amber-600 dark:text-amber-400" x-text="currentRating + ' / 5 Bintang'">
                                        5 / 5 Bintang
                                    </span>
                                </div>
                            </div>

                            {{-- Comment Input --}}
                            <div>
                                <label for="comment-{{ $item->id }}" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Ulasan & Catatan Pengalaman (Opsional)
                                </label>

                                <textarea
                                    id="comment-{{ $item->id }}"
                                    name="comment"
                                    rows="3"
                                    placeholder="Ceritakan kualitas produk, rasa, serta keramahan seller..."
                                    class="w-full rounded-2xl border border-slate-200 bg-white p-4 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                ></textarea>
                            </div>

                            {{-- Photo Input --}}
                            <div>
                                <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Tambah Foto Ulasan (Opsional)
                                </label>

                                <div x-show="previewUrl" class="mb-3 flex items-start gap-4 p-3 rounded-2xl border border-emerald-200 bg-emerald-50 dark:border-emerald-900/60 dark:bg-emerald-950/40">
                                    <img
                                        :src="previewUrl"
                                        alt="Preview Foto Ulasan"
                                        class="h-20 w-20 object-cover rounded-xl border border-emerald-300 dark:border-emerald-700 shadow-2xs"
                                    >
                                    <div>
                                        <p class="text-xs font-bold text-emerald-800 dark:text-emerald-300 flex items-center gap-1">
                                            <i class="fa-solid fa-circle-check text-emerald-600"></i> Foto Ulasan Dipilih
                                        </p>
                                        <p class="text-[11px] text-emerald-700 dark:text-emerald-400 mt-0.5 truncate max-w-xs" x-text="fileName"></p>
                                        <button
                                            type="button"
                                            @click="previewUrl = null; fileName = null; $refs.fileInput.value = ''"
                                            class="mt-2 inline-flex items-center gap-1 text-xs font-bold text-slate-600 hover:text-slate-800 dark:text-slate-400 cursor-pointer"
                                        >
                                            <i class="fa-solid fa-xmark text-[11px]"></i> Hapus Foto
                                        </button>
                                    </div>
                                </div>

                                <label class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50/50 p-4 text-xs font-bold text-slate-600 transition hover:border-emerald-500 hover:bg-emerald-50/30 dark:border-slate-700 dark:bg-slate-800/40 dark:text-slate-300">
                                    <i class="fa-solid fa-camera text-base text-emerald-600"></i>
                                    <span x-text="fileName ? 'Foto Dipilih: ' + fileName : 'Unggah Foto Produk Ulasan...'">
                                        Unggah Foto Produk Ulasan...
                                    </span>
                                    <input
                                        type="file"
                                        name="image"
                                        accept="image/jpeg,image/png,image/jpg,image/webp"
                                        x-ref="fileInput"
                                        @change="handleFileChange($event)"
                                        class="hidden"
                                    >
                                </label>
                            </div>

                            {{-- Submit Button --}}
                            <div class="flex justify-end pt-2">
                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-2 rounded-2xl bg-emerald-700 px-6 py-3 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-800 cursor-pointer"
                                >
                                    <i class="fa-solid fa-paper-plane"></i> Kirim Ulasan
                                </button>
                            </div>

                        </form>

                    @endif

                </div>

            @endforeach

        </div>

    </div>

</x-layouts.buyer>
