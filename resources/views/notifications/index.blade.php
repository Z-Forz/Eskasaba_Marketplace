<x-layouts.buyer title="Notifikasi Saya">

    <div class="mx-auto w-full max-w-4xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-3xl">
                    Notifikasi Saya 🔔
                </h1>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Pemberitahuan pembaruan status akun, pengajuan seller, dan pesanan Anda.
                </p>
            </div>

            @if ($notifications->where('is_read', false)->count() > 0)
                <form action="{{ route('buyer.notifications.read-all') }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700 shadow-xs"
                    >
                        ✓ Tandai Semua Dibaca
                    </button>
                </form>
            @endif

        </div>

        @if ($notifications->count())

            <div class="divide-y divide-slate-100 rounded-3xl border border-slate-200 bg-white shadow-xs dark:divide-slate-800 dark:border-slate-800 dark:bg-slate-900 overflow-hidden">

                @foreach ($notifications as $notification)

                    <a
                        href="{{ route('buyer.notifications.read', $notification) }}"
                        class="flex gap-4 p-5 transition hover:bg-slate-50 dark:hover:bg-slate-800/60 {{ ! $notification->is_read ? 'bg-emerald-50/40 dark:bg-emerald-950/20' : '' }}"
                    >
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl {{ ! $notification->is_read ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }}">
                            @if(str_contains($notification->type, 'seller'))
                                🏪
                            @elseif(str_contains($notification->type, 'order'))
                                📦
                            @else
                                🔔
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    {{ $notification->title }}
                                    @if (! $notification->is_read)
                                        <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                                    @endif
                                </h3>

                                <span class="text-xs text-slate-400">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <p class="mt-1 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                                {{ $notification->message }}
                            </p>
                        </div>
                    </a>

                @endforeach

            </div>

            <div class="mt-6">
                {{ $notifications->links() }}
            </div>

        @else

            <x-empty-state
                title="Belum ada notifikasi"
                message="Pemberitahuan aktivitas pesanan dan akun Anda akan muncul di sini."
                action="{{ route('products.index') }}"
                actionText="Jelajahi Produk"
            />

        @endif

    </div>

</x-layouts.buyer>
