<x-layouts.seller title="Detail Chat Pembeli">

    <div class="flex min-h-[calc(100vh-10rem)] flex-col overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900">

        {{-- Header --}}
        <div class="flex items-center gap-3 border-b border-slate-100 p-4 dark:border-slate-800">

            <a
                href="{{ route('seller.chats.index') }}"
                class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300"
            >
                <i class="fa-solid fa-arrow-left"></i>
            </a>

            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-slate-900 font-bold text-white shadow-xs">
                {{ strtoupper(substr($chat->buyer?->username ?? 'U', 0, 1)) }}
            </div>

            <div class="min-w-0 flex-1">
                <h1 class="truncate font-bold text-slate-900 dark:text-white text-base">
                    <i class="fa-solid fa-user mr-1 text-slate-400 text-xs"></i> Pembeli: {{ $chat->buyer?->username ?? 'Pembeli' }}
                </h1>

                @if ($chat->product)
                    <p class="truncate text-xs font-semibold text-emerald-700 dark:text-emerald-400">
                        <i class="fa-solid fa-box mr-1"></i> Produk: {{ $chat->product->name }}
                    </p>
                @endif
            </div>

        </div>

        {{-- Messages --}}
        <div id="chat-messages" class="flex-1 space-y-4 overflow-y-auto bg-slate-50/60 p-4 dark:bg-slate-950/40 sm:p-6">

            @forelse ($chat->messages as $message)

                @php
                    $isMine = $message->sender_id === auth()->id();
                @endphp

                <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">

                    <div
                        class="max-w-[85%] rounded-2xl px-4 py-3 text-sm shadow-xs sm:max-w-[70%] {{ $isMine
                            ? 'rounded-br-md bg-emerald-700 font-medium text-white'
                            : 'rounded-bl-md border border-slate-200 bg-white text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-white' }}"
                    >

                        <p class="whitespace-pre-wrap wrap-break-word">
                            {{ $message->message }}
                        </p>

                        <div class="mt-1 text-right text-[10px] {{ $isMine ? 'text-emerald-200' : 'text-slate-400' }}">
                            {{ $message->created_at?->format('H:i') }}
                        </div>

                    </div>

                </div>

            @empty

                <div class="flex h-full min-h-64 items-center justify-center text-center">

                    <div>
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white shadow-xs dark:bg-slate-800">
                            <i class="fa-solid fa-comments text-slate-400 text-2xl"></i>
                        </div>
                        <h3 class="mt-4 font-bold text-slate-900 dark:text-white">
                            Belum Ada Pesan
                        </h3>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                            Mulai percakapan dengan pembeli.
                        </p>
                    </div>

                </div>

            @endforelse

        </div>

        {{-- Send Message Form --}}
        <div class="border-t border-slate-100 p-3 dark:border-slate-800 sm:p-4">

            <form
                action="{{ route('seller.chats.message', $chat) }}"
                method="POST"
                class="flex items-end gap-3"
            >
                @csrf

                <textarea
                    name="message"
                    rows="1"
                    required
                    placeholder="Tulis balasan pesan..."
                    class="max-h-32 min-h-12 flex-1 resize-none rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                ></textarea>

                <button
                    type="submit"
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-700 text-white shadow-xs transition hover:bg-emerald-800"
                    aria-label="Kirim pesan"
                >
                    <i class="fa-solid fa-paper-plane"></i>
                </button>

            </form>

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const messages = document.getElementById('chat-messages');
            if (messages) {
                messages.scrollTop = messages.scrollHeight;
            }
        });
    </script>

</x-layouts.seller>
