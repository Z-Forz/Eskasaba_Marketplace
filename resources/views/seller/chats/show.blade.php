<x-layouts.seller>

    <div class="flex min-h-[calc(100vh-10rem)] flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">

        {{-- Header --}}
        <div class="flex items-center gap-3 border-b border-gray-200 p-4 dark:border-gray-800">

            <a
                href="{{ route('seller.chats.index') }}"
                class="flex h-9 w-9 items-center justify-center rounded-xl text-gray-500 transition hover:bg-gray-100 dark:hover:bg-gray-800"
            >
                ←
            </a>

            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-100 font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                {{ strtoupper(substr($chat->buyer->name ?? 'U', 0, 1)) }}
            </div>

            <div class="min-w-0 flex-1">
                <h1 class="truncate font-semibold text-gray-900 dark:text-white">
                    {{ $chat->buyer->name ?? 'Pembeli' }}
                </h1>

                @if ($chat->product)
                    <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                        {{ $chat->product->name }}
                    </p>
                @endif
            </div>

        </div>

        {{-- Messages --}}
        <div class="flex-1 space-y-4 overflow-y-auto p-4 sm:p-6">

            @forelse ($messages as $message)

                @php
                    $isMine = $message->user_id === auth()->id();
                @endphp

                <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">

                    <div
                        class="max-w-[85%] rounded-2xl px-4 py-3 text-sm sm:max-w-[70%] {{ $isMine
                            ? 'rounded-br-md bg-gray-900 text-white dark:bg-white dark:text-gray-900'
                            : 'rounded-bl-md bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200' }}"
                    >

                        <p class="whitespace-pre-wrap wrap-break-word">
                            {{ $message->message }}
                        </p>

                        @if ($message->created_at)
                            <p class="mt-1 text-[10px] opacity-60">
                                {{ $message->created_at->format('H:i') }}
                            </p>
                        @endif

                    </div>

                </div>

            @empty

                <div class="flex h-full min-h-64 items-center justify-center text-center">

                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">
                            Belum ada pesan
                        </h3>

                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Mulai percakapan dengan pembeli.
                        </p>
                    </div>

                </div>

            @endforelse

        </div>

        {{-- Send Message --}}
        <div class="border-t border-gray-200 p-3 dark:border-gray-800 sm:p-4">

            <form
                action="{{ route('seller.chats.store') }}"
                method="POST"
                class="flex items-end gap-2"
            >

                @csrf

                <input
                    type="hidden"
                    name="chat_id"
                    value="{{ $chat->id }}"
                >

                <textarea
                    name="message"
                    rows="1"
                    required
                    placeholder="Tulis pesan..."
                    class="max-h-32 min-h-11 flex-1 resize-none rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                ></textarea>

                <button
                    type="submit"
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-gray-900 text-white transition hover:scale-105 dark:bg-white dark:text-gray-900"
                    aria-label="Kirim pesan"
                >
                    ↑
                </button>

            </form>

        </div>

    </div>

</x-layouts.seller>
```
