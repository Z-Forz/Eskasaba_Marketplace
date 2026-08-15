<x-layouts.seller>

    <div class="space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Chat
            </h1>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Kelola percakapan dengan pembeli.
            </p>
        </div>

        {{-- Search --}}
        <div class="relative">
            <input
                type="text"
                placeholder="Cari percakapan..."
                class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 pl-11 text-sm outline-none transition focus:border-gray-400 focus:ring-2 focus:ring-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
            >

            <svg
                class="absolute left-4 top-3.5 h-5 w-5 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="m21 21-4.35-4.35m2.1-5.4a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"
                />
            </svg>
        </div>

        {{-- Chat List --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">

            @forelse ($chats as $chat)

                <a
                    href="{{ route('seller.chats.show', $chat) }}"
                    class="flex gap-4 border-b border-gray-100 p-4 transition hover:bg-gray-50 last:border-b-0 dark:border-gray-800 dark:hover:bg-gray-800"
                >

                    {{-- Avatar --}}
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gray-100 font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                        {{ strtoupper(substr($chat->buyer->name ?? 'U', 0, 1)) }}
                    </div>

                    <div class="min-w-0 flex-1">

                        <div class="flex items-start justify-between gap-3">

                            <h2 class="truncate font-semibold text-gray-900 dark:text-white">
                                {{ $chat->buyer->name ?? 'Pembeli' }}
                            </h2>

                            @if ($chat->updated_at)
                                <span class="shrink-0 text-xs text-gray-400">
                                    {{ $chat->updated_at->diffForHumans() }}
                                </span>
                            @endif

                        </div>

                        @if ($chat->product)
                            <p class="mt-1 truncate text-xs font-medium text-gray-500 dark:text-gray-400">
                                Produk: {{ $chat->product->name }}
                            </p>
                        @endif

                        <p class="mt-1 truncate text-sm text-gray-500 dark:text-gray-400">
                            {{ $chat->last_message ?? 'Belum ada pesan.' }}
                        </p>

                    </div>

                </a>

            @empty

                <div class="p-10 text-center">

                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                        <svg
                            class="h-7 w-7 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8 10h.01M12 10h.01M16 10h.01M21 12a8.5 8.5 0 0 1-9 8.5A8.5 8.5 0 0 1 3 12a8.5 8.5 0 0 1 9-8.5A8.5 8.5 0 0 1 21 12Z"
                            />
                        </svg>
                    </div>

                    <h3 class="mt-4 font-semibold text-gray-900 dark:text-white">
                        Belum ada percakapan
                    </h3>

                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Percakapan dengan pembeli akan muncul di sini.
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
```
