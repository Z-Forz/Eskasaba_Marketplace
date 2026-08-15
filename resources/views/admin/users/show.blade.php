<x-layouts.admin>

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <a
                    href="{{ route('admin.users.index') }}"
                    class="text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                >
                    ← Kembali ke pengguna
                </a>

                <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">
                    Detail Pengguna
                </h1>
            </div>

            <a
                href="{{ route('admin.users.edit', $user) }}"
                class="inline-flex justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white dark:bg-white dark:text-gray-900"
            >
                Edit Pengguna
            </a>

        </div>

        {{-- Profile Card --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-700 dark:bg-gray-900">

            <div class="flex flex-col gap-5 sm:flex-row sm:items-center">

                <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full bg-gray-100 text-2xl font-bold text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>

                <div class="min-w-0">

                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ $user->name }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ $user->email ?? 'Tidak ada email' }}
                    </p>

                    <div class="mt-3 flex flex-wrap gap-2">

                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                            {{ ucfirst($user->role) }}
                        </span>

                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                            {{ ucfirst($user->status ?? 'active') }}
                        </span>

                    </div>

                </div>

            </div>

        </div>

        {{-- Information --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            {{-- Account --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-700 dark:bg-gray-900">

                <h2 class="font-semibold text-gray-900 dark:text-white">
                    Informasi Akun
                </h2>

                <div class="mt-5 space-y-4">

                    <div>
                        <p class="text-xs text-gray-400">Nama</p>
                        <p class="mt-1 text-sm text-gray-800 dark:text-gray-200">
                            {{ $user->name }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400">Email</p>
                        <p class="mt-1 break-all text-sm text-gray-800 dark:text-gray-200">
                            {{ $user->email ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400">Role</p>
                        <p class="mt-1 text-sm text-gray-800 dark:text-gray-200">
                            {{ ucfirst($user->role) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400">Bergabung</p>
                        <p class="mt-1 text-sm text-gray-800 dark:text-gray-200">
                            {{ $user->created_at?->format('d F Y') ?? '-' }}
                        </p>
                    </div>

                </div>

            </section>

            {{-- School Profile --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-700 dark:bg-gray-900">

                <h2 class="font-semibold text-gray-900 dark:text-white">
                    Profil Sekolah
                </h2>

                @if ($user->school_number)

                    <div class="mt-5 space-y-4">

                        <div>
                            <p class="text-xs text-gray-400">Nama</p>
                            <p class="mt-1 text-sm text-gray-800 dark:text-gray-200">
                                {{ $user->name ?? $user->username }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-400">NIS / NIP</p>
                            <p class="mt-1 text-sm text-gray-800 dark:text-gray-200">
                                {{ $user->school_number }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-400">Tipe</p>
                            <p class="mt-1 text-sm text-gray-800 dark:text-gray-200">
                                {{ ucfirst($user->type ?? '-') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-400">Kelas / Jurusan</p>
                            <p class="mt-1 text-sm text-gray-800 dark:text-gray-200">
                                {{ $user->class ?? '-' }}
                                /
                                {{ $user->major ?? '-' }}
                            </p>
                        </div>

                    </div>

                @else

                    <div class="mt-5 rounded-xl bg-gray-50 p-5 text-sm text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                        Pengguna ini belum memiliki profil sekolah.
                    </div>

                @endif

            </section>

        </div>

    </div>

</x-layouts.admin>
