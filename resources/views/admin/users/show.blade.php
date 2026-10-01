<x-layouts.admin title="Detail Pengguna">

    <div class="mx-auto max-w-4xl space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <a
                    href="{{ route('admin.users.index', request()->query()) }}"
                    class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors"
                >
                    <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke kelola pengguna
                </a>

                <h1 class="mt-2 text-2xl font-black text-slate-900 dark:text-white">
                    Detail Pengguna
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Informasi lengkap akun dan data profil sekolah pengguna.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a
                    href="{{ route('admin.users.edit', array_merge(['user' => $user->id], request()->query())) }}"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white shadow-xs transition hover:bg-emerald-800 active:scale-95"
                >
                    <i class="fa-solid fa-pen-to-square"></i> Edit Pengguna
                </a>
            </div>

        </div>

        @if (session('success'))
            <x-alert type="success" :message="session('success')" class="mb-4" />
        @endif

        {{-- Profile Header Card --}}
        <div class="rounded-3xl border border-slate-200/80 bg-white p-6 dark:border-slate-800 dark:bg-slate-900 shadow-xs">

            <div class="flex flex-col gap-5 sm:flex-row sm:items-center">

                <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-3xl bg-emerald-950 text-2xl font-black text-white shadow-md border border-emerald-500/30">
                    {{ strtoupper(substr($user->username, 0, 1)) }}
                </div>

                <div class="min-w-0 flex-1">

                    <h2 class="text-xl font-black text-slate-900 dark:text-white truncate">
                        {{ $user->username }}
                    </h2>

                    <p class="mt-1 text-sm font-medium text-slate-600 dark:text-slate-300 flex items-center gap-2 truncate">
                        <i class="fa-solid fa-envelope text-emerald-600"></i> {{ $user->email ?? 'Belum ada email' }}
                    </p>

                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60">
                            <i class="{{ $user->role === 'teacher' ? 'fa-solid fa-chalkboard-user' : 'fa-solid fa-graduation-cap' }}"></i>
                            {{ $user->role === 'teacher' ? 'Guru' : 'Siswa' }}
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                            <i class="fa-solid fa-calendar-days text-emerald-600"></i>
                            Terdaftar: {{ $user->created_at?->format('d F Y') ?? '-' }}
                        </span>

                        @if($user->is_default_password)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/60" title="Kata Sandi Akun">
                                <i class="fa-solid fa-key text-amber-500"></i>
                                Kata Sandi: {{ $user->plain_password ?? 'password' }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-900 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60" title="Kata Sandi Akun">
                                <i class="fa-solid fa-lock text-emerald-600"></i>
                                Kata Sandi: {{ $user->plain_password ?? '••••••••' }}
                            </span>
                        @endif
                    </div>

                </div>

            </div>

        </div>

        {{-- Information Grid (2 balanced columns with 3 rows each) --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 items-stretch">

            {{-- Account --}}
            <section class="flex flex-col justify-between rounded-3xl border border-slate-200/80 bg-white p-6 dark:border-slate-800 dark:bg-slate-900 shadow-xs">

                <div>
                    <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex h-9 w-9 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400">
                            <i class="fa-solid fa-user-shield text-sm"></i>
                        </div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-base">
                            Informasi Akun
                        </h2>
                    </div>

                    <div class="mt-4 divide-y divide-slate-100 dark:divide-slate-800/80 text-sm">

                        <div class="flex items-center justify-between py-3.5">
                            <span class="inline-flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400">
                                <i class="fa-solid fa-at w-4 text-center text-slate-400"></i> Username
                            </span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $user->username }}</span>
                        </div>

                        <div class="flex items-center justify-between py-3.5">
                            <span class="inline-flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400">
                                <i class="fa-solid fa-envelope w-4 text-center text-slate-400"></i> Email
                            </span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $user->email ?? '-' }}</span>
                        </div>

                        <div class="flex items-center justify-between py-3.5">
                            <span class="inline-flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400">
                                <i class="fa-solid fa-user-gear w-4 text-center text-slate-400"></i> Peran (Role)
                            </span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $user->role === 'teacher' ? 'Guru' : 'Siswa' }}</span>
                        </div>

                    </div>
                </div>

            </section>

            {{-- School Profile & Contact --}}
            <section class="flex flex-col justify-between rounded-3xl border border-slate-200/80 bg-white p-6 dark:border-slate-800 dark:bg-slate-900 shadow-xs">

                <div>
                    <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex h-9 w-9 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400">
                            <i class="fa-solid fa-school text-sm"></i>
                        </div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-base">
                            Profil Sekolah & Telepon
                        </h2>
                    </div>

                    <div class="mt-4 divide-y divide-slate-100 dark:divide-slate-800/80 text-sm">

                        <div class="flex items-center justify-between py-3.5">
                            <span class="inline-flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400">
                                <i class="fa-solid fa-id-card w-4 text-center text-slate-400"></i> NIS / NIP
                            </span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $user->nis_nip ?? '-' }}</span>
                        </div>

                        <div class="flex items-center justify-between py-3.5">
                            <span class="inline-flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400">
                                <i class="fa-solid fa-chalkboard-user w-4 text-center text-slate-400"></i> Kelas / Rombel
                            </span>
                            <span class="font-bold text-emerald-700 dark:text-emerald-400">{{ $user->class_room ?? '-' }}</span>
                        </div>

                        <div class="flex items-center justify-between py-3.5">
                            <span class="inline-flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400">
                                <i class="fa-solid fa-phone w-4 text-center text-slate-400"></i> Nomor Telepon
                            </span>
                            @if($user->phone)
                                <a
                                    href="https://wa.me/{{ preg_replace('/[^0-9]/', '', str_starts_with($user->phone, '0') ? '62' . substr($user->phone, 1) : $user->phone) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1.5 font-bold text-emerald-700 dark:text-emerald-400 hover:underline"
                                    title="Hubungi via WhatsApp"
                                >
                                    <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i>
                                    {{ $user->phone }}
                                </a>
                            @else
                                <span class="font-semibold text-slate-400 dark:text-slate-500">-</span>
                            @endif
                        </div>

                    </div>
                </div>

            </section>

        </div>

    </div>

</x-layouts.admin>
