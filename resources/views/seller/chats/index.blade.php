<x-layouts.seller title="Pesan Chat Masuk">

    <div class="space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-comments text-emerald-600"></i> Pesan Chat Pembeli
            </h1>

            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Kelola percakapan dan pertanyaan mengenai produk dari calon pembeli.
            </p>
        </div>

        {{-- Chat List --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900">

            @forelse ($chats as $chat)

                <a
                    href="{{ route('seller.chats.show', $chat) }}"
                    class="flex items-center gap-4 border-b border-slate-100 p-5 transition hover:bg-slate-50 last:border-b-0 dark:border-slate-800 dark:hover:bg-slate-800/60"
                >

                    {{-- Avatar --}}
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-slate-900 font-bold text-white shadow-xs">
                        {{ strtoupper(substr($chat->buyer?->username ?? 'U', 0, 1)) }}
                    </div>

                    <div class="min-w-0 flex-1">

                        <div class="flex items-start justify-between gap-3">
                            <h2 class="truncate font-bold text-slate-900 dark:text-white text-base">
                                <i class="fa-solid fa-user text-slate-400 mr-1 text-xs"></i> {{ $chat->buyer?->username ?? 'Pembeli' }}
                            </h2>

                            @if ($chat->last_message_at ?? $chat->updated_at)
                                <span class="shrink-0 text-xs text-slate-400">
                                    {{ ($chat->last_message_at ?? $chat->updated_at)->diffForHumans() }}
                                </span>
                            @endif
                        </div>

                        @if ($chat->product)
                            <p class="mt-0.5 truncate text-xs font-semibold text-emerald-700 dark:text-emerald-400">
                                <i class="fa-solid fa-box mr-1"></i> Produk: {{ $chat->product->name }}
                            </p>
                        @endif

                        <p class="mt-1.5 truncate text-sm text-slate-600 dark:text-slate-300">
                            {{ $chat->last_message ?? 'Belum ada pesan.' }}
                        </p>

                    </div>

                </a>

            @empty

                <div class="p-10 text-center">

                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800">
                        <i class="fa-solid fa-comments text-slate-400 text-2xl"></i>
                    </div>

                    <h3 class="mt-4 font-bold text-slate-900 dark:text-white">
                        Belum ada percakapan
                    </h3>

                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Pesan pertanyaan dari calon pembeli akan muncul di halaman ini.
                    </p>

                </div>

            @endforelse

        </div>

        @if (method_exists($chats, 'links'))
            <div>
                {{ $chats->links() }}
            </div>
        @endif

    </div>

</x-layouts.seller>
