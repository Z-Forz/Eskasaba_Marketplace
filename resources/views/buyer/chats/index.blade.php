<x-layouts.buyer title="Chat">

    <div class="mx-auto w-full max-w-5xl px-4 py-8 sm:px-6 lg:px-8">

        <div class="mb-8">

            <p class="text-sm font-medium text-slate-500">
                Komunikasi
            </p>

            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                Chat
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Hubungi seller mengenai produk atau pesananmu.
            </p>

        </div>

        @if ($chats->count())

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                <div class="divide-y divide-slate-100">

                    @foreach ($chats as $chat)

                        <a
                            href="{{ route('buyer.chats.show', $chat) }}"
                            class="flex gap-4 p-5 transition hover:bg-slate-50 sm:p-6"
                        >

                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-slate-100 font-bold text-slate-600">

                                {{ strtoupper(substr($chat->seller->user->name ?? 'S', 0, 1)) }}

                            </div>

                            <div class="min-w-0 flex-1">

                                <div class="flex items-start justify-between gap-4">

                                    <div class="min-w-0">

                                        <h2 class="truncate font-semibold text-slate-900">
                                            {{ $chat->seller->user->name ?? 'Seller' }}
                                        </h2>

                                        @if ($chat->product)
                                            <p class="mt-1 truncate text-xs text-slate-400">
                                                {{ $chat->product->name }}
                                            </p>
                                        @endif

                                    </div>

                                    @if ($chat->updated_at)
                                        <time class="shrink-0 text-xs text-slate-400">
                                            {{ $chat->updated_at->diffForHumans() }}
                                        </time>
                                    @endif

                                </div>

                                <p class="mt-2 truncate text-sm text-slate-500">
                                    {{ $chat->latestMessage->message ?? 'Belum ada pesan.' }}
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
                message="Percakapan dengan seller akan muncul di sini."
                action="{{ route('products.index') }}"
                actionText="Cari Produk"
            />

        @endif

    </div>

</x-layouts.buyer>