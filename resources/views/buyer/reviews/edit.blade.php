<x-layouts.buyer title="Edit Ulasan Produk">

    <div class="mx-auto w-full max-w-3xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <a
                href="{{ route('buyer.reviews.index') }}"
                class="text-xs font-bold text-emerald-700 hover:text-emerald-800 transition flex items-center gap-1"
            >
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Ulasan
            </a>

            <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-900 dark:text-white sm:text-3xl flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-emerald-600"></i> Edit Ulasan Produk
            </h1>

            <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">
                Ubah rating, komentar, atau foto ulasan produk kamu.
            </p>
        </div>

        @if(session('error'))
            <x-alert type="error" :message="session('error')" class="mb-6" />
        @endif

        <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">

            {{-- Product Info Header --}}
            <div class="flex items-center gap-4 border-b border-slate-100 pb-4 dark:border-slate-800">
                <div class="h-16 w-16 shrink-0 overflow-hidden rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700">
                    @if($review->product?->images?->first())
                        <img
                            src="{{ Storage::url($review->product->images->first()->image) }}"
                            alt="{{ $review->product->name }}"
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
                        {{ $review->product?->name }}
                    </h3>

                    @if($review->order)
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                            Invoice: <strong class="font-semibold text-slate-700 dark:text-slate-300">{{ $review->order->invoice_number }}</strong>
                        </p>
                    @endif
                </div>
            </div>

            <form
                action="{{ route('buyer.reviews.update', $review) }}"
                method="POST"
                enctype="multipart/form-data"
                class="mt-5 space-y-5"
                x-data="{
                    currentRating: {{ old('rating', $review->rating) }},
                    previewUrl: null,
                    fileName: null,
                    removeExisting: false,
                    handleFileChange(event) {
                        const file = event.target.files[0];
                        if (file) {
                            this.previewUrl = URL.createObjectURL(file);
                            this.fileName = file.name;
                            this.removeExisting = false;
                        }
                    }
                }"
            >
                @csrf
                @method('PUT')
                <input type="hidden" name="order_id" value="{{ $review->order_id }}">
                <input type="hidden" name="product_id" value="{{ $review->product_id }}">
                <input type="hidden" name="rating" x-model="currentRating">
                <input type="hidden" name="remove_image" :value="removeExisting ? '1' : '0'">

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
                            {{ $review->rating }} / 5 Bintang
                        </span>
                    </div>
                    @error('rating')
                        <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Comment Input --}}
                <div>
                    <label for="comment" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Ulasan & Catatan Pengalaman
                    </label>

                    <textarea
                        id="comment"
                        name="comment"
                        rows="4"
                        placeholder="Ceritakan kualitas produk, rasa, serta keramahan seller..."
                        class="w-full rounded-2xl border border-slate-200 bg-white p-4 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    >{{ old('comment', $review->comment) }}</textarea>
                    @error('comment')
                        <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Photo Input & Existing Photo --}}
                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Foto Ulasan (Opsional)
                    </label>

                    @if($review->image)
                        <div class="mb-3 flex items-start gap-4 p-3 rounded-2xl border border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-800/50" x-show="!removeExisting && !previewUrl">
                            <img
                                src="{{ Storage::url($review->image) }}"
                                alt="Foto Ulasan Saat Ini"
                                class="h-20 w-20 object-cover rounded-xl border border-slate-200 dark:border-slate-700 shadow-2xs"
                            >
                            <div>
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Foto Ulasan Saat Ini</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">Kamu dapat mengunggah foto baru untuk mengganti foto ini, atau menghapusnya.</p>
                                <button
                                    type="button"
                                    @click="removeExisting = true"
                                    class="mt-2 inline-flex items-center gap-1 text-xs font-bold text-red-600 hover:text-red-700 dark:text-red-400 cursor-pointer"
                                >
                                    <i class="fa-solid fa-trash-can text-[11px]"></i> Hapus Foto Ini
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- Live Preview for New Image --}}
                    <div x-show="previewUrl" class="mb-3 flex items-start gap-4 p-3 rounded-2xl border border-emerald-200 bg-emerald-50 dark:border-emerald-900/60 dark:bg-emerald-950/40">
                        <img
                            :src="previewUrl"
                            alt="Preview Foto Baru"
                            class="h-20 w-20 object-cover rounded-xl border border-emerald-300 dark:border-emerald-700 shadow-2xs"
                        >
                        <div>
                            <p class="text-xs font-bold text-emerald-800 dark:text-emerald-300 flex items-center gap-1">
                                <i class="fa-solid fa-circle-check text-emerald-600"></i> Foto Baru Dipilih
                            </p>
                            <p class="text-[11px] text-emerald-700 dark:text-emerald-400 mt-0.5 truncate max-w-xs" x-text="fileName"></p>
                            <button
                                type="button"
                                @click="previewUrl = null; fileName = null; $refs.fileInput.value = ''"
                                class="mt-2 inline-flex items-center gap-1 text-xs font-bold text-slate-600 hover:text-slate-800 dark:text-slate-400 cursor-pointer"
                            >
                                <i class="fa-solid fa-xmark text-[11px]"></i> Batalkan Foto Baru
                            </button>
                        </div>
                    </div>

                    <label class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50/50 p-4 text-xs font-bold text-slate-600 transition hover:border-emerald-500 hover:bg-emerald-50/30 dark:border-slate-700 dark:bg-slate-800/40 dark:text-slate-300">
                        <i class="fa-solid fa-camera text-base text-emerald-600"></i>
                        <span x-text="fileName ? 'Foto Dipilih: ' + fileName : (removeExisting ? 'Pilih Foto Baru...' : '{{ $review->image ? 'Ganti Foto Ulasan...' : 'Unggah Foto Ulasan...' }}')">
                            {{ $review->image ? 'Ganti Foto Ulasan...' : 'Unggah Foto Ulasan...' }}
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
                    @error('image')
                        <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <a
                        href="{{ route('buyer.reviews.index') }}"
                        class="rounded-2xl border border-slate-200 px-5 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 transition"
                    >
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-2xl bg-emerald-700 px-6 py-2.5 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-800 cursor-pointer"
                    >
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>

    </div>

</x-layouts.buyer>
