<x-layouts.admin title="Log Login System">

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-emerald-600"></i> Riwayat Log Login & Aktivitas System
                </h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Pantau riwayat login, logout, dan aktivitas keamanan akun pengguna dan administrator.
                </p>
            </div>
        </div>

        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            
            {{-- Total Logs --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400">
                        <i class="fa-solid fa-database text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Log System</p>
                        <p class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($stats['total_logs']) }}</p>
                    </div>
                </div>
            </div>

            {{-- Today Logins --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400">
                        <i class="fa-solid fa-right-to-bracket text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Login Hari Ini</p>
                        <p class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($stats['today_logins']) }}</p>
                    </div>
                </div>
            </div>

            {{-- Unique Active Users --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-purple-50 text-purple-600 dark:bg-purple-950/50 dark:text-purple-400">
                        <i class="fa-solid fa-users text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">User Aktif Hari Ini</p>
                        <p class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($stats['unique_users']) }}</p>
                    </div>
                </div>
            </div>

            {{-- Admin Actions --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400">
                        <i class="fa-solid fa-user-shield text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Aktivitas Admin</p>
                        <p class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($stats['admin_actions']) }}</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Filter & Search Form --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <form action="{{ route('admin.login-logs.index') }}" method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-4">
                
                {{-- Search Input --}}
                <div class="md:col-span-2">
                    <label for="search" class="mb-1 block text-xs font-bold text-slate-600 dark:text-slate-400">Cari Pengguna / IP / Deskripsi</label>
                    <div class="relative">
                        <input
                            type="text"
                            id="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari nama, email, NIS/NIP, IP, atau deskripsi..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-800 placeholder-slate-400 transition focus:border-emerald-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-600/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500"
                        />
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    </div>
                </div>

                {{-- Type Filter --}}
                <div>
                    <label for="type" class="mb-1 block text-xs font-bold text-slate-600 dark:text-slate-400">Tipe Akun</label>
                    <x-custom-select
                        name="type"
                        :options="[
                            '' => 'Semua Akun',
                            'user' => 'User (Siswa & Guru)',
                            'admin' => 'Admin Panel',
                        ]"
                        :selected="request('type')"
                        placeholder=""
                        :submitOnSelect="true"
                    />
                </div>

                {{-- Event Filter --}}
                <div>
                    <label for="event" class="mb-1 block text-xs font-bold text-slate-600 dark:text-slate-400">Jenis Event</label>
                    <div class="flex gap-2">
                        <div class="w-full">
                            <x-custom-select
                                name="event"
                                :options="array_merge(['' => 'Semua Event'], collect($events)->mapWithKeys(fn($ev) => [$ev => Str::headline($ev)])->all())"
                                :selected="request('event')"
                                placeholder=""
                                :submitOnSelect="true"
                            />
                        </div>

                        @if (request()->hasAny(['search', 'type', 'event']))
                            <a
                                href="{{ route('admin.login-logs.index') }}"
                                class="inline-flex items-center justify-center rounded-xl border border-red-200 bg-red-50 px-3 text-red-600 hover:bg-red-100 dark:border-red-900/50 dark:bg-red-950/40 dark:text-red-400"
                                title="Reset Filter"
                            >
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        @endif
                    </div>
                </div>

            </form>
        </div>

        {{-- Table Logs --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100 dark:bg-slate-800/50 dark:text-slate-400 dark:border-slate-800">
                        <tr>
                            <th scope="col" class="px-6 py-4">Waktu</th>
                            <th scope="col" class="px-6 py-4">Pengguna / Akun</th>
                            <th scope="col" class="px-6 py-4">Aktivitas / Event</th>
                            <th scope="col" class="px-6 py-4">Alamat IP & Perangkat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($logs as $log)
                            <tr class="transition hover:bg-slate-50/80 dark:hover:bg-slate-800/40">
                                
                                {{-- Waktu --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="font-bold text-slate-900 dark:text-white">
                                        {{ $log->created_at->translatedFormat('d M Y, H:i:s') }}
                                    </p>
                                    <p class="text-xs text-slate-400 font-medium">
                                        {{ $log->created_at->diffForHumans() }}
                                    </p>
                                </td>

                                {{-- Account --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($log->admin)
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-800 font-bold dark:bg-amber-950/80 dark:text-amber-300">
                                                <i class="fa-solid fa-user-shield text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                                    {{ $log->admin->name }}
                                                    <span class="inline-flex items-center rounded-md bg-amber-100 px-1.5 py-0.5 text-[10px] font-black text-amber-800 dark:bg-amber-950/80 dark:text-amber-300">
                                                        Admin
                                                    </span>
                                                </p>
                                                <p class="text-xs text-slate-400 font-medium">
                                                    @ {{ $log->admin->username }}
                                                </p>
                                            </div>
                                        </div>
                                    @elseif ($log->user)
                                        <div class="flex items-center gap-3">
                                            @if ($log->user->profile_photo)
                                                <img src="{{ asset('storage/' . $log->user->profile_photo) }}" alt="{{ $log->user->username }}" class="h-9 w-9 shrink-0 rounded-xl object-cover border border-slate-200 dark:border-slate-700">
                                            @else
                                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-800 font-black dark:bg-emerald-950/80 dark:text-emerald-300">
                                                    {{ strtoupper(substr($log->user->username, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <p class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                                    {{ $log->user->username }}
                                                    @if ($log->user->role === 'seller' || $log->user->seller)
                                                        <span class="inline-flex items-center rounded-md bg-blue-100 px-1.5 py-0.5 text-[10px] font-black text-blue-800 dark:bg-blue-950/80 dark:text-blue-300">
                                                            Penjual
                                                        </span>
                                                    @elseif ($log->user->role === 'teacher')
                                                        <span class="inline-flex items-center rounded-md bg-purple-100 px-1.5 py-0.5 text-[10px] font-black text-purple-800 dark:bg-purple-950/80 dark:text-purple-300">
                                                            Guru
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                            Siswa
                                                        </span>
                                                    @endif
                                                </p>
                                                <p class="text-xs text-slate-400 font-medium">
                                                    {{ $log->user->email ?? $log->user->nis_nip }}
                                                </p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2 text-slate-400 italic">
                                            <i class="fa-solid fa-circle-question"></i>
                                            <span>Sistem / Tamu</span>
                                        </div>
                                    @endif
                                </td>

                                {{-- Event / Description --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-start gap-2.5">
                                        <div class="mt-0.5">
                                            @if (str_contains($log->event, 'login'))
                                                <span class="inline-flex items-center justify-center h-6 w-6 rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-950/80 dark:text-emerald-400">
                                                    <i class="fa-solid fa-right-to-bracket text-xs"></i>
                                                </span>
                                            @elseif (str_contains($log->event, 'logout'))
                                                <span class="inline-flex items-center justify-center h-6 w-6 rounded-lg bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                                    <i class="fa-solid fa-right-from-bracket text-xs"></i>
                                                </span>
                                            @elseif (str_contains($log->event, 'password'))
                                                <span class="inline-flex items-center justify-center h-6 w-6 rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-950/80 dark:text-amber-400">
                                                    <i class="fa-solid fa-key text-xs"></i>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center justify-center h-6 w-6 rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-950/80 dark:text-blue-400">
                                                    <i class="fa-solid fa-circle-info text-xs"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800 dark:text-slate-200">
                                                {{ $log->description }}
                                            </p>
                                            <span class="inline-block mt-0.5 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                                                Event: {{ $log->event }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- IP & Device --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="font-mono text-xs font-bold text-slate-700 dark:text-slate-300">
                                        <i class="fa-solid fa-network-wired text-slate-400 mr-1"></i> {{ $log->ip_address }}
                                    </p>
                                    <p class="mt-0.5 text-xs text-slate-400 font-medium flex items-center gap-1">
                                        <i class="fa-solid fa-laptop text-[10px]"></i> {{ $log->device }}
                                    </p>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800">
                                        <i class="fa-solid fa-clock-rotate-left text-xl text-slate-400"></i>
                                    </div>
                                    <p class="mt-3 font-bold">Belum Ada Catatan Log</p>
                                    <p class="text-xs text-slate-400">Tidak ada riwayat aktivitas yang sesuai dengan kriteria pencarian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($logs->hasPages())
                <div class="border-t border-slate-100 px-6 py-4 dark:border-slate-800">
                    {{ $logs->links() }}
                </div>
            @endif

        </div>

    </div>

</x-layouts.admin>
