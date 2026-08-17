<x-layouts.buyer title="Chat Seller">

    <div class="mx-auto w-full max-w-5xl px-4 py-8 sm:px-6 lg:px-8">

        <div class="mb-8">
            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700">
                Komunikasi & Diskusi
            </p>

            <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-900 sm:text-3xl flex items-center gap-2">
                <i class="fa-solid fa-comments text-emerald-600"></i> Chat dengan Penjual Toko
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Hubungi penjual mengenai ketersediaan barang atau informasi pesanan Anda.
            </p>
        </div>

        @if ($chats->count())

            <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-xs">

                <div class="divide-y divide-slate-100">

                    @foreach ($chats as $chat)

                        <a
                            href="{{ route('buyer.chats.show', $chat) }}"
                            class="flex items-center gap-4 p-5 transition hover:bg-slate-50/80 sm:p-6"
                        >

                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-slate-900 text-base font-bold text-white shadow-xs">
                                {{ strtoupper(substr($chat->seller?->user?->username ?? 'S', 0, 1)) }}
                            </div>

                            <div class="min-w-0 flex-1">

                                <div class="flex items-start justify-between gap-4">

                                    <div class="min-w-0">
                                        <h2 class="truncate font-bold text-slate-900 text-base">
                                            {{ $chat->seller?->user?->username ?? 'Seller' }}
                                        </h2>

                                        @if ($chat->product)
                                            <p class="mt-0.5 truncate text-xs font-semibold text-emerald-700">
                                                <i class="fa-solid fa-box mr-1"></i> Produk: {{ $chat->product->name }}
                                            </p>
                                        @endif
                                    </div>

                                    @if ($chat->last_message_at ?? $chat->updated_at)
                                        <time class="shrink-0 text-xs font-medium text-slate-400">
                                            {{ ($chat->last_message_at ?? $chat->updated_at)->diffForHumans() }}
                                        </time>
                                    @endif

                                </div>

                                <p class="mt-1.5 truncate text-sm text-slate-600">
                                    {{ $chat->last_message ?? 'Belum ada pesan.' }}
                                </p>

                            </div>

                        </a>

                    @endforeach

                </div>

            </div>

            @if ($chats->hasPages())
                <div class="mt-8">
                    {{ $chats->links() }}
                </div>
            @endif

        @else

            <x-empty-state
                title="Belum ada percakapan"
                description="Percakapan Anda dengan penjual toko akan muncul di halaman ini."
                action="{{ route('products.index') }}"
                actionText="Cari Produk Sekolah"
            />

        @endif

    </div>

</x-layouts.buyer>