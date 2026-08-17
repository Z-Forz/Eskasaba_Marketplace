<x-layouts.admin title="Detail Pengguna">

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <a
                    href="{{ route('admin.users.index') }}"
                    class="text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                >
                    ← Kembali ke pengguna
                </a>

                <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">
                    Detail Pengguna
                </h1>
            </div>

            <a
                href="{{ route('admin.users.edit', $user) }}"
                class="inline-flex justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white dark:bg-white dark:text-gray-900 shadow-xs"
            >
                Edit Pengguna
            </a>

        </div>

        {{-- Profile Card --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-700 dark:bg-gray-900 shadow-xs">

            <div class="flex flex-col gap-5 sm:flex-row sm:items-center">

                <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full bg-emerald-800 text-2xl font-bold text-white shadow-xs">
                    {{ strtoupper(substr($user->username, 0, 1)) }}
                </div>

                <div class="min-w-0">

                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ $user->username }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ $user->email ?? 'Tidak ada email' }}
                    </p>

                    <div class="mt-3 flex flex-wrap gap-2">

                        <span class="rounded-full px-3 py-1 text-xs font-semibold
                            {{ $user->role === 'teacher' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}"
                        >
                            {{ $user->role === 'teacher' ? 'Guru' : 'Siswa' }}
                        </span>

                    </div>

                </div>

            </div>

        </div>

        {{-- Information --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            {{-- Account --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-700 dark:bg-gray-900 shadow-xs">

                <h2 class="font-bold text-gray-900 dark:text-white text-base">
                    Informasi Akun
                </h2>

                <div class="mt-5 space-y-4">

                    <div>
                        <p class="text-xs text-gray-400">Username / Nama Pengguna</p>
                        <p class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                            {{ $user->username }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400">Email</p>
                        <p class="mt-1 break-all text-sm font-medium text-gray-800 dark:text-gray-200">
                            {{ $user->email ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400">Peran (Role)</p>
                        <p class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                            {{ $user->role === 'teacher' ? 'Guru' : 'Siswa' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400">Tanggal Bergabung</p>
                        <p class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                            {{ $user->created_at?->format('d F Y') ?? '-' }}
                        </p>
                    </div>

                </div>

            </section>

            {{-- School Profile --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-700 dark:bg-gray-900 shadow-xs">

                <h2 class="font-bold text-gray-900 dark:text-white text-base">
                    Profil Sekolah
                </h2>

                <div class="mt-5 space-y-4">

                    <div>
                        <p class="text-xs text-gray-400">NIS / NIP</p>
                        <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-200">
                            {{ $user->nis_nip ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400">Nomor Telepon</p>
                        <p class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                            {{ $user->phone ?? '-' }}
                        </p>
                    </div>

                    @if ($user->role === 'teacher')
                        <div>
                            <p class="text-xs text-gray-400">Mata Pelajaran yang Diajarkan (Mapel)</p>
                            <p class="mt-1 text-sm font-semibold text-amber-700 dark:text-amber-400">
                                📚 {{ $user->subject_taught ?? $user->major ?? 'Guru Mata Pelajaran' }}
                            </p>
                        </div>
                    @else
                        <div>
                            <p class="text-xs text-gray-400">Kelas / Jurusan</p>
                            <p class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                                {{ $user->class ?? '-' }} {{ $user->major ? '• ' . $user->major : '' }}
                            </p>
                        </div>
                    @endif

                </div>

            </section>

        </div>

    </div>

</x-layouts.admin>
