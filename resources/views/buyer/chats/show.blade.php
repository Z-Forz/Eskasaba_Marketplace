<x-layouts.buyer title="Percakapan Chat">

    <div class="mx-auto flex h-[calc(100vh-6rem)] w-full max-w-5xl flex-col px-4 py-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="shrink-0 rounded-t-3xl border border-slate-200/80 bg-white px-5 py-4 shadow-xs sm:px-6">

            <div class="flex items-center gap-4">

                <a
                    href="{{ route('buyer.chats.index') }}"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition hover:bg-slate-200"
                >
                    <i class="fa-solid fa-arrow-left"></i>
                </a>

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-slate-900 font-bold text-white shadow-xs">
                    {{ strtoupper(substr($chat->seller?->user?->username ?? 'S', 0, 1)) }}
                </div>

                <div class="min-w-0 flex-1">

                    <h1 class="truncate text-base font-bold text-slate-900">
                        {{ $chat->seller?->user?->username ?? 'Penjual Toko' }}
                    </h1>

                    @if ($chat->product)
                        <p class="truncate text-xs font-semibold text-emerald-700">
                            <i class="fa-solid fa-box mr-1"></i> {{ $chat->product->name }}
                        </p>
                    @endif

                </div>

            </div>

        </div>

        {{-- Messages List --}}
        <div
            id="chat-messages"
            class="min-h-0 flex-1 overflow-y-auto border-x border-slate-200/80 bg-slate-50 px-4 py-6 sm:px-6"
        >

            <div class="mx-auto flex max-w-3xl flex-col gap-3">

                @forelse ($chat->messages as $message)

                    @php
                        $isMine = $message->sender_id === auth()->id();
                    @endphp

                    <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">

                        <div
                            class="max-w-[85%] rounded-2xl px-4 py-3 text-sm shadow-xs sm:max-w-[70%] {{ $isMine
                                ? 'rounded-br-md bg-emerald-700 font-medium text-white'
                                : 'rounded-bl-md border border-slate-200 bg-white text-slate-800' }}"
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

                    <div class="flex min-h-64 items-center justify-center">

                        <div class="text-center">

                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white shadow-xs">
                                <i class="fa-solid fa-comments text-slate-400 text-2xl"></i>
                            </div>

                            <h2 class="mt-4 font-bold text-slate-900">
                                Mulai Percakapan
                            </h2>

                            <p class="mt-1 text-xs text-slate-500">
                                Tulis dan kirim pesan pertama kepada seller toko.
                            </p>

                        </div>

                    </div>

                @endforelse

            </div>

        </div>

        {{-- Message Form --}}
        <div class="shrink-0 rounded-b-3xl border border-t-0 border-slate-200/80 bg-white p-4 shadow-xs">

            <form
                method="POST"
                action="{{ route('buyer.chats.message', $chat) }}"
                class="flex items-end gap-3"
            >
                @csrf

                <textarea
                    name="message"
                    rows="1"
                    required
                    placeholder="Tulis pesan Anda..."
                    class="max-h-32 min-h-12 flex-1 resize-none rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                ></textarea>

                <button
                    type="submit"
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-700 text-white shadow-xs transition hover:bg-emerald-800"
                    aria-label="Kirim pesan"
                >
                    <i class="fa-solid fa-paper-plane"></i>
                </button>

            </form>

            @error('message')
                <p class="mt-2 text-xs font-semibold text-red-600">
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