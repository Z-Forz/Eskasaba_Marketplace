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

        {{-- Order Items Review Forms --}}
        <div class="space-y-6">

            @foreach($order->items as $item)

                @php
                    $alreadyReviewed = \App\Models\Review::where('order_id', $order->id)
                        ->where('product_id', $item->product_id)
                        ->where('user_id', auth()->id())
                        ->exists();
                @endphp

                <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">

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

                    @if($alreadyReviewed)

                        <div class="mt-4 rounded-2xl bg-emerald-50 p-4 border border-emerald-200 text-center dark:bg-emerald-950/40 dark:border-emerald-900/60">
                            <p class="text-xs font-extrabold text-emerald-800 dark:text-emerald-300 flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-circle-check text-emerald-600"></i> Ulasan untuk produk ini sudah berhasil dikirim. Terima kasih!
                            </p>
                        </div>

                    @else

                        <form
                            action="{{ route('buyer.reviews.store') }}"
                            method="POST"
                            class="mt-5 space-y-5"
                            x-data="{ currentRating: 5 }"
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
