<x-layouts.admin title="Detail Pengguna">

    <div class="space-y-6 max-w-4xl mx-auto">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <a
                    href="{{ route('admin.users.index') }}"
                    class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
                >
                    <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke pengguna
                </a>

                <h1 class="mt-3 text-2xl font-black text-slate-900 dark:text-white">
                    Detail Pengguna
                </h1>
            </div>

            <div class="flex items-center gap-3">
                <a
                    href="{{ route('admin.users.edit', $user) }}"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white shadow-xs transition hover:bg-emerald-800"
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

                    <h2 class="text-xl font-black text-slate-900 dark:text-white">
                        {{ $user->username }}
                    </h2>

                    <p class="mt-1 text-sm font-semibold text-slate-600 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-envelope text-emerald-600"></i> {{ $user->email ?? 'Belum ada email' }}
                    </p>

                    <div class="mt-3 flex items-center justify-center gap-2">
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            <i class="{{ $user->role === 'teacher' ? 'fa-solid fa-chalkboard-user' : 'fa-solid fa-graduation-cap' }} mr-1"></i>
                            {{ $user->role === 'teacher' ? 'Guru' : 'Siswa' }}
                        </span>
                    </div>

                </div>

            </div>

        </div>

        {{-- Information Grid --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            {{-- Account --}}
            <section class="rounded-3xl border border-slate-200/80 bg-white p-6 dark:border-slate-800 dark:bg-slate-900 shadow-xs">

                <h2 class="font-bold text-slate-900 dark:text-white text-base">
                    Informasi Akun
                </h2>

                <div class="mt-5 space-y-4 text-sm">

                    <div class="flex justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                        <span class="text-xs font-medium text-slate-400">Username</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $user->username }}</span>
                    </div>

                    <div class="flex justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                        <span class="text-xs font-medium text-slate-400">Email</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $user->email ?? '-' }}</span>
                    </div>

                    <div class="flex justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                        <span class="text-xs font-medium text-slate-400">Peran (Role)</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $user->role === 'teacher' ? 'Guru' : 'Siswa' }}</span>
                    </div>

                    <div class="flex justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                        <span class="text-xs font-medium text-slate-400">Status Password</span>
                        <span class="font-bold text-xs {{ $user->is_default_password ? 'text-amber-600' : 'text-emerald-600' }}">
                            {{ $user->is_default_password ? 'Belum diganti (Default)' : 'Sudah diganti kustom' }}
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-xs font-medium text-slate-400">Tanggal Bergabung</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $user->created_at?->format('d F Y') ?? '-' }}</span>
                    </div>

                </div>

            </section>

            {{-- School Profile --}}
            <section class="rounded-3xl border border-slate-200/80 bg-white p-6 dark:border-slate-800 dark:bg-slate-900 shadow-xs">

                <h2 class="font-bold text-slate-900 dark:text-white text-base">
                    Profil Sekolah & Telepon
                </h2>

                <div class="mt-5 space-y-4 text-sm">

                    <div class="flex justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                        <span class="text-xs font-medium text-slate-400">NIS / NIP</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $user->nis_nip ?? '-' }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-xs font-medium text-slate-400">Nomor Telepon</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $user->phone ?? '-' }}</span>
                    </div>

                </div>

            </section>

        </div>

        {{-- Admin Password Reset Form Box --}}
        <div class="rounded-3xl border border-amber-200 bg-amber-50/60 p-6 shadow-xs dark:border-amber-900/50 dark:bg-amber-950/20">

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="font-bold text-amber-900 dark:text-amber-300 text-base flex items-center gap-2">
                        <i class="fa-solid fa-key"></i> Lupa Kata Sandi? Reset Password User
                    </h3>

                    <p class="mt-1 text-xs text-amber-800/80 dark:text-amber-400">
                        Jika siswa/guru lupa password, Admin dapat mereset password pengguna ini secara instan di sini.
                    </p>
                </div>

                <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="flex items-center gap-2">
                    @csrf
                    @method('PUT')

                    <input
                        type="text"
                        name="new_password"
                        placeholder="Password baru..."
                        value="12345678"
                        required
                        class="rounded-xl border border-amber-300 bg-white px-3 py-2 text-xs font-bold text-slate-900 outline-none focus:ring-2 focus:ring-amber-200"
                    >

                    <button
                        type="submit"
                        class="rounded-xl bg-amber-700 px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-amber-800 shrink-0"
                    >
                        Reset Password Now
                    </button>
                </form>
            </div>

        </div>

    </div>

</x-layouts.admin>
