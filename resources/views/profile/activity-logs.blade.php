<x-layouts.app title="Riwayat Login & Aktivitas Akun">
    <div class="mx-auto w-full max-w-4xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <a
                href="{{ route('profile.index') }}"
                class="inline-flex items-center text-sm font-semibold text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
            >
                <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Profil
            </a>

            <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white sm:text-3xl flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved text-emerald-600"></i> Riwayat Login & Keamanan Akun
                    </h1>

                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Pantau seluruh aktivitas masuk (login) dan perubahan keamanan pada akun Anda.
                    </p>
                </div>
            </div>
        </div>

        {{-- Log List Card --}}
        <div class="rounded-3xl border border-slate-200/80 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900 overflow-hidden">

            <div class="p-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    Log Aktivitas Terdaftar ({{ $logs->total() }})
                </span>
                <span class="text-xs text-slate-400">
                    Sistem otomatis mencatat IP & Perangkat
                </span>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800">

                @forelse ($logs as $log)
                    <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition hover:bg-slate-50/60 dark:hover:bg-slate-800/40">

                        <div class="flex items-start gap-4">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-lg dark:bg-slate-800">
                                <i class="{{ $log->icon }}"></i>
                            </div>

                            <div>
                                <h3 class="font-bold text-slate-900 dark:text-white text-sm">
                                    {{ $log->description }}
                                </h3>

                                <div class="mt-1 flex flex-wrap items-center gap-3 text-xs text-slate-500 dark:text-slate-400">
                                    <span class="inline-flex items-center gap-1 font-medium">
                                        <i class="fa-solid fa-laptop text-slate-400"></i> {{ $log->device }}
                                    </span>

                                    <span class="inline-flex items-center gap-1 font-medium">
                                        <i class="fa-solid fa-network-wired text-slate-400"></i> IP: {{ $log->ip_address ?? '127.0.0.1' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="text-left sm:text-right shrink-0 text-xs">
                            <p class="font-bold text-slate-800 dark:text-slate-200">
                                {{ $log->created_at->format('d M Y, H:i') }} WIB
                            </p>
                            <p class="mt-0.5 text-slate-400">
                                {{ $log->created_at->diffForHumans() }}
                            </p>
                        </div>

                    </div>
                @empty
                    <div class="p-12 text-center text-sm text-slate-500">
                        Belum ada riwayat aktivitas yang tercatat.
                    </div>
                @endforelse

            </div>

            @if ($logs->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $logs->links() }}
                </div>
            @endif

        </div>

        {{-- Security Tip Banner --}}
        <div class="mt-6 rounded-3xl border border-blue-200 bg-blue-50/60 p-5 dark:border-blue-900/50 dark:bg-blue-950/20 text-xs text-blue-800 dark:text-blue-300 leading-relaxed flex items-start gap-3">
            <i class="fa-solid fa-circle-info text-base text-blue-600 shrink-0 mt-0.5"></i>
            <div>
                <p class="font-bold text-sm">Tips Keamanan Akun</p>
                <p class="mt-1">
                    Jika Anda menemukan lokasi IP atau waktu login asing yang tidak Anda kenali, segera lakukan <a href="{{ route('password.change') }}" class="font-bold underline">Ubah Password</a> atau laporkan ke pihak admin sekolah agar akun Anda tetap aman.
                </p>
            </div>
        </div>

    </div>
</x-layouts.app>
