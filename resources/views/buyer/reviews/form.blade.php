<x-layouts.buyer title="Beri Ulasan">

    <div class="mx-auto w-full max-w-2xl px-4 py-8 sm:px-6 lg:px-8">

        <div class="mb-8">

            <a
                href="{{ route('buyer.orders.index') }}"
                class="text-sm font-medium text-slate-500 hover:text-slate-900"
            >
                ← Kembali ke pesanan
            </a>

            <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                Beri Ulasan
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Bagikan pengalamanmu setelah membeli produk ini.
            </p>

        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

            <div class="flex gap-4">

                <div class="h-20 w-20 shrink-0 overflow-hidden rounded-2xl bg-slate-100">

                    @if ($orderItem->product?->images?->first())

                        <img
                            src="{{ Storage::url($orderItem->product->images->first()->image) }}"
                            alt="{{ $orderItem->product_name }}"
                            class="h-full w-full object-cover"
                        >

                    @endif

                </div>

                <div>
                    <h2 class="font-bold text-slate-900">
                        {{ $orderItem->product_name }}
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Pesanan #{{ $orderItem->order_id }}
                    </p>
                </div>

            </div>

            <form
                method="POST"
                action="{{ route('buyer.reviews.store') }}"
                enctype="multipart/form-data"
                class="mt-8 space-y-6"
            >
                @csrf

                <input
                    type="hidden"
                    name="order_item_id"
                    value="{{ $orderItem->id }}"
                >

                {{-- Rating --}}
                <div>

                    <label class="mb-3 block text-sm font-semibold text-slate-900">
                        Rating
                    </label>

                    <div class="flex flex-wrap gap-2">

                        @for ($i = 1; $i <= 5; $i++)

                            <label class="cursor-pointer">

                                <input
                                    type="radio"
                                    name="rating"
                                    value="{{ $i }}"
                                    class="peer sr-only"
                                    {{ old('rating') == $i ? 'checked' : '' }}
                                    required
                                >

                                <span class="flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 text-lg text-slate-400 transition peer-checked:border-slate-900 peer-checked:bg-slate-900 peer-checked:text-white hover:bg-slate-50">
                                    {{ $i }}
                                </span>

                            </label>

                        @endfor

                    </div>

                    @error('rating')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- Comment --}}
                <div>

                    <label
                        for="comment"
                        class="mb-2 block text-sm font-semibold text-slate-900"
                    >
                        Komentar
                    </label>

                    <textarea
                        id="comment"
                        name="comment"
                        rows="5"
                        required
                        placeholder="Bagaimana pengalamanmu dengan produk ini?"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                    >{{ old('comment') }}</textarea>

                    @error('comment')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- Photo --}}
                <div>

                    <label
                        for="image"
                        class="mb-2 block text-sm font-semibold text-slate-900"
                    >
                        Foto Produk
                        <span class="font-normal text-slate-400">
                            (opsional)
                        </span>
                    </label>

                    <input
                        id="image"
                        name="image"
                        type="file"
                        accept="image/*"
                        class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-500 file:mr-4 file:rounded-lg file:border-0 file:bg-slate-100 file:px-4 file:py-2 file:text-sm file:font-medium"
                    >

                    @error('image')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

                    <a
                        href="{{ route('buyer.orders.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Batal
                    </a>

                    <x-button type="submit">
                        Kirim Ulasan
                    </x-button>

                </div>

            </form>

        </div>

    </div>

</x-layouts.buyer>