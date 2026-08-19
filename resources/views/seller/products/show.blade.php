<x-layouts.seller title="Detail Produk">

    <div class="mx-auto w-full max-w-6xl px-4 py-6 sm:px-6 lg:px-8">

        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <a
                    href="{{ route('seller.products.index') }}"
                    class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-slate-900"
                >
                    <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke produk
                </a>

                <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    Detail Produk
                </h1>

            </div>

            <a
                href="{{ route('seller.products.edit', $product) }}"
                class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800"
            >
                Edit Produk
            </a>

        </div>


        <div class="grid gap-6 lg:grid-cols-2">

            {{-- Images --}}
            <div class="space-y-4">

                <div class="aspect-square overflow-hidden rounded-3xl border border-slate-200 bg-slate-100">

                    @if ($product->images->first())

                        <img
                            src="{{ Storage::url($product->images->first()->image) }}"
                            alt="{{ $product->name }}"
                            class="h-full w-full object-cover"
                        >

                    @else

                        <div class="flex h-full items-center justify-center text-sm text-slate-400">
                            Tidak ada foto
                        </div>

                    @endif

                </div>


                @if ($product->images->count() > 1)

                    <div class="grid grid-cols-4 gap-3">

                        @foreach ($product->images as $image)

                            <div class="aspect-square overflow-hidden rounded-xl border border-slate-200 bg-slate-100">

                                <img
                                    src="{{ Storage::url($image->image) }}"
                                    alt="{{ $product->name }}"
                                    class="h-full w-full object-cover"
                                >

                            </div>

                        @endforeach

                    </div>

                @endif

            </div>


            {{-- Information --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

                <div class="flex items-start justify-between gap-4">

                    <div>

                        <p class="text-sm text-slate-500">
                            {{ $product->category->name ?? 'Tanpa Kategori' }}
                        </p>

                        <h2 class="mt-2 text-2xl font-bold text-slate-900">
                            {{ $product->name }}
                        </h2>

                    </div>

                    <x-badge :type="$product->status">
                        {{ ucfirst(str_replace('_', ' ', $product->status)) }}
                    </x-badge>

                </div>


                <div class="mt-6">

                    <p class="text-3xl font-bold text-slate-900">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </p>

                </div>


                <div class="mt-6 grid grid-cols-2 gap-3">

                    <div class="rounded-2xl bg-slate-50 p-4">

                        <p class="text-xs text-slate-500">
                            Stok
                        </p>

                        <p class="mt-1 font-bold text-slate-900">
                            {{ $product->stock }}
                        </p>

                    </div>

                    <div class="rounded-2xl bg-slate-50 p-4">

                        <p class="text-xs text-slate-500">
                            Kondisi
                        </p>

                        <p class="mt-1 font-bold capitalize text-slate-900">
                            {{ $product->condition }}
                        </p>

                    </div>

                    <div class="rounded-2xl bg-slate-50 p-4">

                        <p class="text-xs text-slate-500">
                            Berat
                        </p>

                        <p class="mt-1 font-bold text-slate-900">
                            {{ $product->weight ?? 0 }} gram
                        </p>

                    </div>

                    <div class="rounded-2xl bg-slate-50 p-4">

                        <p class="text-xs text-slate-500">
                            Rating
                        </p>

                        <p class="mt-1 font-bold text-slate-900">
                            {{ number_format($product->reviews_avg_rating ?? 0, 1) }}
                        </p>

                    </div>

                </div>


                <div class="mt-8 border-t border-slate-100 pt-6">

                    <h3 class="font-bold text-slate-900">
                        Deskripsi
                    </h3>

                    <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-600">
                        {{ $product->description }}
                    </p>

                </div>

            </div>

        </div>

    </div>

</x-layouts.seller>