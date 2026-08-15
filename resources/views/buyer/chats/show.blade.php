<x-layouts.buyer title="Percakapan">

    <div class="mx-auto flex h-[calc(100vh-5rem)] w-full max-w-5xl flex-col px-4 py-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="shrink-0 rounded-t-3xl border border-slate-200 bg-white px-5 py-4 shadow-sm sm:px-6">

            <div class="flex items-center gap-4">

                <a
                    href="{{ route('buyer.chats.index') }}"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-900"
                >
                    ←
                </a>

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-slate-100 font-bold text-slate-600">
                    {{ strtoupper(substr($chat->seller->user->name ?? 'S', 0, 1)) }}
                </div>

                <div class="min-w-0 flex-1">

                    <h1 class="truncate font-bold text-slate-900">
                        {{ $chat->seller->user->name ?? 'Seller' }}
                    </h1>

                    @if ($chat->product)
                        <p class="truncate text-xs text-slate-500">
                            {{ $chat->product->name }}
                        </p>
                    @endif

                </div>

            </div>

        </div>

        {{-- Messages --}}
        <div
            id="chat-messages"
            class="min-h-0 flex-1 overflow-y-auto border-x border-slate-200 bg-slate-50 px-4 py-6 sm:px-6"
        >

            <div class="mx-auto flex max-w-3xl flex-col gap-3">

                @forelse ($chat->messages as $message)

                    @php
                        $isMine = $message->user_id === auth()->id();
                    @endphp

                    <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">

                        <div
                            class="max-w-[85%] rounded-2xl px-4 py-3 text-sm shadow-sm sm:max-w-[70%] {{ $isMine
                                ? 'rounded-br-md bg-slate-900 text-white'
                                : 'rounded-bl-md border border-slate-200 bg-white text-slate-800' }}"
                        >

                            <p class="whitespace-pre-wrap wrap-break-word">
                                {{ $message->message }}
                            </p>

                            <div class="mt-1 text-right text-[10px] {{ $isMine ? 'text-slate-300' : 'text-slate-400' }}">
                                {{ $message->created_at->format('H:i') }}
                            </div>

                        </div>

                    </div>

                @empty

                    <div class="flex min-h-75 items-center justify-center">

                        <div class="text-center">

                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white shadow-sm">
                                💬
                            </div>

                            <h2 class="mt-4 font-semibold text-slate-900">
                                Mulai percakapan
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Kirim pesan pertama kepada seller.
                            </p>

                        </div>

                    </div>

                @endforelse

            </div>

        </div>

        {{-- Message Form --}}
        <div class="shrink-0 rounded-b-3xl border border-t-0 border-slate-200 bg-white p-4 shadow-sm">

            <form
                method="POST"
                action="{{ route('buyer.chats.store') }}"
                class="flex items-end gap-3"
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
                    class="max-h-32 min-h-12 flex-1 resize-none rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                ></textarea>

                <button
                    type="submit"
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-slate-900 text-white transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-300"
                    aria-label="Kirim pesan"
                >
                    ↑
                </button>

            </form>

            @error('message')
                <p class="mt-2 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror

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

</x-layouts.buyer>