<x-layouts.admin title="Kelola Pengguna">

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-users text-emerald-600"></i> Kelola Pengguna Sekolah
                </h1>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Kelola daftar pengguna terdaftar (Siswa & Guru) di Eskasaba Marketplace.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <form action="{{ route('admin.users.sync') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mensinkronkan data pengguna dengan SiPintu Identity & API Gateway Sekolah?')">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl border border-emerald-600 bg-emerald-50 px-5 py-3 text-sm font-bold text-emerald-800 shadow-xs transition hover:bg-emerald-100 dark:border-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 dark:hover:bg-emerald-900/60 cursor-pointer"
                    >
                        <i class="fa-solid fa-rotate text-emerald-600"></i> Sinkronisasi SiPintu Gateway
                    </button>
                </form>

                <a
                    href="{{ route('admin.users.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-5 py-3 text-sm font-bold text-white shadow-xs transition hover:bg-emerald-800"
                >
                    <i class="fa-solid fa-user-plus"></i> Tambah User Baru
                </a>
            </div>

        </div>

        @if (session('success'))
            <x-alert type="success" :message="session('success')" class="mb-4" />
        @endif

        {{-- Quick Role Filter Tabs --}}
        <div class="flex flex-wrap items-center gap-2">
            <a
                href="{{ route('admin.users.index', array_filter(['search' => request('search')])) }}"
                class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 text-xs font-bold transition shadow-2xs {{ empty(request('role')) ? 'bg-emerald-700 text-white shadow-emerald-700/20 ring-2 ring-emerald-700' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/80 dark:bg-slate-900 dark:text-slate-200 dark:border-slate-800' }}"
            >
                <i class="fa-solid fa-users text-[11px]"></i> Semua Pengguna
                <span class="rounded-full px-2 py-0.5 text-[10px] {{ empty(request('role')) ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">
                    {{ $roleCounts['all'] ?? 0 }}
                </span>
            </a>

            <a
                href="{{ route('admin.users.index', array_filter(['role' => 'student', 'search' => request('search')])) }}"
                class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 text-xs font-bold transition shadow-2xs {{ request('role') === 'student' ? 'bg-emerald-700 text-white shadow-emerald-700/20 ring-2 ring-emerald-700' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/80 dark:bg-slate-900 dark:text-slate-200 dark:border-slate-800' }}"
            >
                <i class="fa-solid fa-graduation-cap text-[11px]"></i> Siswa Aktif
                <span class="rounded-full px-2 py-0.5 text-[10px] {{ request('role') === 'student' ? 'bg-white/20 text-white' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' }}">
                    {{ $roleCounts['student'] ?? 0 }}
                </span>
            </a>

            <a
                href="{{ route('admin.users.index', array_filter(['role' => 'teacher', 'search' => request('search')])) }}"
                class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 text-xs font-bold transition shadow-2xs {{ request('role') === 'teacher' ? 'bg-amber-600 text-white shadow-amber-600/20 ring-2 ring-amber-600' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/80 dark:bg-slate-900 dark:text-slate-200 dark:border-slate-800' }}"
            >
                <i class="fa-solid fa-chalkboard-user text-[11px]"></i> Dewan Guru
                <span class="rounded-full px-2 py-0.5 text-[10px] {{ request('role') === 'teacher' ? 'bg-white/20 text-white' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300' }}">
                    {{ $roleCounts['teacher'] ?? 0 }}
                </span>
            </a>
        </div>

        {{-- Search & Filter Bar --}}
        <div class="rounded-3xl border border-slate-200/80 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <form
                method="GET"
                action="{{ route('admin.users.index') }}"
                class="flex flex-col gap-3 sm:flex-row sm:items-center"
            >
                <div class="relative flex-1">
                    <input
                        type="search"
                        name="search"
                        value="{{ $search ?? request('search') }}"
                        placeholder="Cari nama, NIS/NIP, email, kelas..."
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 pl-10 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    >
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-xs text-slate-400"></i>
                </div>

                <div class="w-full sm:w-48">
                    <select
                        name="role"
                        onchange="this.form.submit()"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white cursor-pointer"
                    >
                        <option value="">Semua Peran (Role)</option>
                        <option value="student" @selected(request('role') === 'student')>🎓 Siswa</option>
                        <option value="teacher" @selected(request('role') === 'teacher')>👨‍🏫 Guru</option>
                    </select>
                </div>

                <button
                    type="submit"
                    class="rounded-2xl bg-emerald-700 px-6 py-2.5 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-800 flex items-center justify-center gap-1.5 cursor-pointer"
                >
                    <i class="fa-solid fa-magnifying-glass"></i> Filter
                </button>

                @if(request()->hasAny(['search', 'role']))
                    <a
                        href="{{ route('admin.users.index') }}"
                        class="rounded-2xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 flex items-center justify-center gap-1"
                    >
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Desktop Table --}}
        <div class="hidden overflow-hidden rounded-3xl border border-slate-200/80 bg-white dark:border-slate-800 dark:bg-slate-900 md:block shadow-xs">

            <div class="overflow-x-auto">

                <table class="w-full text-left text-sm">

                    <thead class="border-b border-slate-100 bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:bg-slate-800/50">

                        <tr>
                            <th class="px-6 py-4">Pengguna</th>
                            <th class="px-6 py-4">Alamat Email</th>
                            <th class="px-6 py-4">NIS / NIP</th>
                            <th class="px-6 py-4">Kelas</th>
                            <th class="px-6 py-4">Peran (Role)</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">

                        @forelse ($users as $user)

                            <tr class="transition hover:bg-slate-50/80 dark:hover:bg-slate-800/50">

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-950 font-bold text-white shadow-xs border border-emerald-500/30">
                                            {{ strtoupper(substr($user->username, 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-900 dark:text-white">
                                                {{ $user->username }}
                                            </p>
                                            <p class="text-xs text-slate-400">
                                                ID API: {{ $user->api_id ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-semibold text-slate-800 dark:text-slate-200">
                                    @if($user->email)
                                        <span class="inline-flex items-center gap-1.5 text-xs text-slate-700 dark:text-slate-300">
                                            <i class="fa-solid fa-envelope text-emerald-600"></i> {{ $user->email }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs italic">Belum diisi</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 font-bold text-slate-700 dark:text-slate-300">
                                    {{ $user->nis_nip ?? '-' }}
                                </td>

                                <td class="px-6 py-4 font-bold text-emerald-700 dark:text-emerald-400 text-xs">
                                    {{ $user->class_room ?? '-' }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-bold
                                        {{ $user->role === 'teacher' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' }}"
                                    >
                                        @if($user->role === 'teacher')
                                            <i class="fa-solid fa-chalkboard-user mr-1"></i> Guru
                                        @else
                                            <i class="fa-solid fa-graduation-cap mr-1"></i> Siswa
                                        @endif
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end items-center gap-2">
                                        <a
                                            href="{{ route('admin.users.show', $user) }}"
                                            class="inline-flex items-center gap-1 rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-bold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                                        >
                                            <i class="fa-solid fa-eye"></i> Detail
                                        </a>

                                        <a
                                            href="{{ route('admin.users.edit', $user) }}"
                                            class="inline-flex items-center gap-1 rounded-xl bg-slate-900 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-slate-800 dark:bg-emerald-700 dark:hover:bg-emerald-800"
                                        >
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </a>
                                    </div>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">
                                    Belum ada data pengguna yang ditemukan.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        {{-- Mobile View --}}
        <div class="space-y-3 md:hidden">

            @forelse ($users as $user)

                <div class="rounded-3xl border border-slate-200/80 bg-white p-5 dark:border-slate-800 dark:bg-slate-900 shadow-xs">

                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-950 font-bold text-white shadow-xs">
                            {{ strtoupper(substr($user->username, 0, 1)) }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <h2 class="truncate font-bold text-slate-900 dark:text-white">
                                    {{ $user->username }}
                                </h2>

                                <span class="shrink-0 rounded-full px-2.5 py-0.5 text-[11px] font-bold
                                    {{ $user->role === 'teacher' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}"
                                >
                                    {{ $user->role === 'teacher' ? 'Guru' : 'Siswa' }}
                                </span>
                            </div>

                            <p class="mt-0.5 truncate text-xs text-slate-600 dark:text-slate-300 font-semibold">
                                <i class="fa-solid fa-envelope text-emerald-600 mr-1"></i> {{ $user->email ?? 'Belum diisi' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-3 border-t border-slate-100 pt-3 dark:border-slate-800 text-xs">
                        <div>
                            <p class="text-slate-400 font-semibold">NIS / NIP</p>
                            <p class="mt-0.5 font-bold text-slate-800 dark:text-slate-200">
                                {{ $user->nis_nip ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-slate-400 font-semibold">Kelas / Rombel</p>
                            <p class="mt-0.5 font-bold text-emerald-700 dark:text-emerald-400">
                                {{ $user->class_room ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 flex gap-2 border-t border-slate-100 pt-3 dark:border-slate-800">
                        <a
                            href="{{ route('admin.users.show', $user) }}"
                            class="flex-1 rounded-2xl border border-slate-200 py-2 text-center text-xs font-bold text-slate-700 dark:border-slate-700 dark:text-slate-300 flex items-center justify-center gap-1"
                        >
                            <i class="fa-solid fa-eye"></i> Detail
                        </a>
                        <a
                            href="{{ route('admin.users.edit', $user) }}"
                            class="flex-1 rounded-2xl bg-emerald-700 py-2 text-center text-xs font-bold text-white flex items-center justify-center gap-1"
                        >
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </a>
                    </div>

                </div>

            @empty

                <div class="rounded-3xl border border-slate-200/80 bg-white p-10 text-center text-sm text-slate-500 dark:border-slate-800 dark:bg-slate-900">
                    Belum ada data pengguna yang ditemukan.
                </div>

            @endforelse

        </div>

        {{-- Pagination --}}
        @if (method_exists($users, 'links'))
            <div>
                {{ $users->withQueryString()->links() }}
            </div>
        @endif

    </div>

</x-layouts.admin>
