<x-layouts.buyer title="Ulasan Saya">

    <div class="mx-auto w-full max-w-5xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white sm:text-3xl flex items-center gap-2.5">
                    <i class="fa-solid fa-star text-amber-400"></i> Ulasan Saya
                </h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Daftar ulasan dan rating produk yang telah kamu berikan.
                </p>
            </div>
            <a
                href="{{ route('buyer.orders.index') }}"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-50 px-4 py-2.5 text-xs font-bold text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:text-emerald-300 transition"
            >
                <i class="fa-solid fa-box-open"></i> Liha Pesanan Saya
            </a>
        </div>

        @if(session('success'))
            <x-alert type="success" :message="session('success')" class="mb-6" />
        @endif

        @if($reviews->isEmpty())
            <div class="rounded-3xl border border-slate-200/80 bg-white p-12 text-center shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-amber-50 text-amber-500 dark:bg-amber-950/40 dark:text-amber-400">
                    <i class="fa-solid fa-star-half-stroke text-3xl"></i>
                </div>
                <h3 class="mt-4 text-base font-extrabold text-slate-900 dark:text-white">Belum Ada Ulasan</h3>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                    Kamu belum menulis ulasan untuk produk yang sudah kamu beli. Selesaikan pesananmu lalu bagikan pengalamannya!
                </p>
                <div class="mt-6">
                    <a
                        href="{{ route('buyer.orders.index') }}"
                        class="inline-flex items-center gap-2 rounded-2xl bg-emerald-700 px-6 py-3 text-xs font-bold text-white shadow-xs hover:bg-emerald-800 transition"
                    >
                        <i class="fa-solid fa-shopping-bag"></i> Beli & Review Produk
                    </a>
                </div>
            </div>
        @else
            <div class="space-y-4">
                @foreach($reviews as $review)
                    <div
                        x-data="{ showDeleteModal: false, showImageModal: false }"
                        class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs transition hover:border-slate-300 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-slate-700"
                    >
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">

                            {{-- Product & Review Header --}}
                            <div class="flex items-start gap-4 flex-1">
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

                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a
                                            href="{{ route('products.show', $review->product) }}"
                                            class="font-extrabold text-slate-900 hover:text-emerald-700 dark:text-white dark:hover:text-emerald-400 text-base transition truncate"
                                        >
                                            {{ $review->product?->name }}
                                        </a>
                                    </div>

                                    <div class="mt-1 flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400">
                                        <span>Invoice: <strong class="font-semibold text-slate-700 dark:text-slate-300">{{ $review->order?->invoice_number ?? '-' }}</strong></span>
                                        <span>&bull;</span>
                                        <span>{{ $review->created_at?->translatedFormat('d M Y, H:i') }}</span>
                                    </div>

                                    {{-- Star Rating --}}
                                    <div class="mt-2.5 flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fa-solid fa-star text-sm {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-200 dark:text-slate-700' }}"></i>
                                        @endfor
                                        <span class="ml-2 text-xs font-bold text-amber-600 dark:text-amber-400">
                                            {{ $review->rating }}/5
                                        </span>
                                    </div>

                                    {{-- Comment --}}
                                    @if($review->comment)
                                        <div class="mt-3 rounded-2xl bg-slate-50 p-3.5 border border-slate-100 text-xs text-slate-700 dark:bg-slate-800/60 dark:border-slate-800 dark:text-slate-300">
                                            "{{ $review->comment }}"
                                        </div>
                                    @endif

                                    {{-- Review Photo Thumbnail --}}
                                    @if($review->image)
                                        <div class="mt-3">
                                            <button
                                                type="button"
                                                @click="showImageModal = true"
                                                class="group relative inline-block overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xs transition hover:opacity-90 cursor-pointer"
                                            >
                                                <img
                                                    src="{{ Storage::url($review->image) }}"
                                                    alt="Foto Ulasan"
                                                    class="h-20 w-20 object-cover"
                                                >
                                                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-bold">
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
                                                        src="{{ Storage::url($review->image) }}"
                                                        alt="Foto Ulasan Full"
                                                        class="max-h-[80vh] w-auto max-w-full rounded-2xl object-contain shadow-2xl border border-white/20"
                                                    >
                                                </div>
                                            </div>
                                        </template>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center sm:flex-col gap-2 shrink-0 border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100 dark:border-slate-800">
                                <a
                                    href="{{ route('buyer.reviews.edit', $review) }}"
                                    class="flex-1 sm:w-full inline-flex items-center justify-center gap-1.5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-800 hover:bg-emerald-100 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300 transition"
                                >
                                    <i class="fa-solid fa-pen-to-square text-emerald-600"></i> Edit
                                </a>

                                <button
                                    type="button"
                                    @click="showDeleteModal = true"
                                    class="flex-1 sm:w-full inline-flex items-center justify-center gap-1.5 rounded-2xl border border-red-200 bg-red-50 px-4 py-2 text-xs font-bold text-red-700 hover:bg-red-100 dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-300 transition cursor-pointer"
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
                                                Apakah kamu yakin ingin menghapus ulasan untuk produk <strong>{{ $review->product?->name }}</strong>? Tindakan ini tidak dapat dibatalkan.
                                            </p>
                                            <div class="mt-6 flex items-center justify-end gap-3">
                                                <button
                                                    type="button"
                                                    @click="showDeleteModal = false"
                                                    class="rounded-2xl border border-slate-200 px-5 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 transition"
                                                >
                                                    Batal
                                                </button>
                                                <form action="{{ route('buyer.reviews.destroy', $review) }}" method="POST">
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
                @endforeach
            </div>

            <div class="mt-6">
                {{ $reviews->links() }}
            </div>
        @endif

    </div>

</x-layouts.buyer>
