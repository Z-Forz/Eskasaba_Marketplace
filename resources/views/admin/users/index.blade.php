<x-layouts.admin title="Kelola Pengguna">

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Kelola Pengguna
                </h1>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Kelola daftar pengguna terdaftar di Eskasaba Marketplace.
                </p>
            </div>

            <a
                href="{{ route('admin.users.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:opacity-90 dark:bg-white dark:text-gray-900"
            >
                + Tambah Pengguna
            </a>

        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-900/50 dark:bg-green-950/30 dark:text-green-400">
                {{ session('success') }}
            </div>
        @endif

        {{-- Search --}}
        <form
            method="GET"
            action="{{ route('admin.users.index') }}"
            class="flex flex-col gap-2 sm:flex-row"
        >

            <input
                type="search"
                name="search"
                value="{{ $search ?? request('search') }}"
                placeholder="Cari username, NIS/NIP, email, kelas, jurusan..."
                class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
            >

            <button
                type="submit"
                class="rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white dark:bg-white dark:text-gray-900"
            >
                Cari
            </button>

        </form>

        {{-- Desktop Table --}}
        <div class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 md:block">

            <div class="overflow-x-auto">

                <table class="w-full text-left text-sm">

                    <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">

                        <tr>
                            <th class="px-5 py-4 font-semibold text-gray-600 dark:text-gray-300">
                                Username / Email
                            </th>

                            <th class="px-5 py-4 font-semibold text-gray-600 dark:text-gray-300">
                                NIS / NIP
                            </th>

                            <th class="px-5 py-4 font-semibold text-gray-600 dark:text-gray-300">
                                Kelas & Jurusan
                            </th>

                            <th class="px-5 py-4 font-semibold text-gray-600 dark:text-gray-300">
                                Peran (Role)
                            </th>

                            <th class="px-5 py-4 text-right font-semibold text-gray-600 dark:text-gray-300">
                                Aksi
                            </th>
                        </tr>

                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">

                        @forelse ($users as $user)

                            <tr class="transition hover:bg-gray-50 dark:hover:bg-gray-800">

                                <td class="px-5 py-4">

                                    <div class="flex items-center gap-3">

                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-800 font-semibold text-white shadow-xs">
                                            {{ strtoupper(substr($user->username, 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">

                                            <p class="font-bold text-gray-900 dark:text-white">
                                                {{ $user->username }}
                                            </p>

                                            <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                                                {{ $user->email ?? '-' }}
                                            </p>

                                        </div>

                                    </div>

                                </td>

                                <td class="px-5 py-4 font-medium text-gray-700 dark:text-gray-300">
                                    {{ $user->nis_nip ?? '-' }}
                                </td>

                                <td class="px-5 py-4 text-gray-600 dark:text-gray-300">
                                    @if ($user->class || $user->major)
                                        {{ $user->class ?? '' }} {{ $user->major ? '• ' . $user->major : '' }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="px-5 py-4">

                                    <span class="rounded-full px-3 py-1 text-xs font-semibold
                                        {{ $user->role === 'teacher' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}"
                                    >
                                        {{ $user->role === 'teacher' ? 'Guru' : 'Siswa' }}
                                    </span>

                                </td>

                                <td class="px-5 py-4 text-right">

                                    <div class="flex justify-end gap-2">
                                        <a
                                            href="{{ route('admin.users.show', $user) }}"
                                            class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300"
                                        >
                                            Detail
                                        </a>

                                        <a
                                            href="{{ route('admin.users.edit', $user) }}"
                                            class="rounded-lg bg-gray-900 px-3 py-1.5 text-xs font-medium text-white hover:bg-slate-800 dark:bg-white dark:text-gray-900"
                                        >
                                            Edit
                                        </a>
                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="5"
                                    class="px-5 py-12 text-center text-sm text-gray-500 dark:text-gray-400"
                                >
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

                <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900 shadow-xs">

                    <div class="flex items-center gap-3">

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-emerald-800 font-semibold text-white shadow-xs">
                            {{ strtoupper(substr($user->username, 0, 1)) }}
                        </div>

                        <div class="min-w-0 flex-1">

                            <div class="flex items-start justify-between gap-3">

                                <h2 class="truncate font-bold text-gray-900 dark:text-white">
                                    {{ $user->username }}
                                </h2>

                                <span class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-semibold
                                    {{ $user->role === 'teacher' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}"
                                >
                                    {{ $user->role === 'teacher' ? 'Guru' : 'Siswa' }}
                                </span>

                            </div>

                            <p class="mt-1 truncate text-xs text-gray-500 dark:text-gray-400">
                                {{ $user->email ?? '-' }}
                            </p>

                        </div>

                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-3 border-t border-gray-100 pt-3 dark:border-gray-800 text-xs">

                        <div>
                            <p class="text-gray-400">NIS / NIP</p>
                            <p class="mt-0.5 font-medium text-gray-800 dark:text-gray-200">
                                {{ $user->nis_nip ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-400">Kelas / Jurusan</p>
                            <p class="mt-0.5 font-medium text-gray-800 dark:text-gray-200">
                                {{ $user->class ?? '-' }} {{ $user->major ? '• ' . $user->major : '' }}
                            </p>
                        </div>

                    </div>

                    <div class="mt-4 flex gap-2 border-t border-gray-100 pt-3 dark:border-gray-800">
                        <a
                            href="{{ route('admin.users.show', $user) }}"
                            class="flex-1 rounded-xl border border-gray-200 py-2 text-center text-xs font-semibold text-gray-700 dark:border-gray-700 dark:text-gray-300"
                        >
                            Detail
                        </a>
                        <a
                            href="{{ route('admin.users.edit', $user) }}"
                            class="flex-1 rounded-xl bg-gray-900 py-2 text-center text-xs font-semibold text-white dark:bg-white dark:text-gray-900"
                        >
                            Edit
                        </a>
                    </div>

                </div>

            @empty

                <div class="rounded-2xl border border-gray-200 bg-white p-10 text-center text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-900">
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
