<x-layouts.admin>

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Pengguna
                </h1>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Kelola pengguna marketplace.
                </p>
            </div>

            <a
                href="{{ route('admin.users.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:opacity-90 dark:bg-white dark:text-gray-900"
            >
                + Tambah Pengguna
            </a>

        </div>

        {{-- Search --}}
        <form
            method="GET"
            action="{{ route('admin.users.index') }}"
            class="flex flex-col gap-2 sm:flex-row"
        >

            <input
                type="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama, email, atau nomor sekolah..."
                class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
            >

            <button
                type="submit"
                class="rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white dark:bg-white dark:text-gray-900"
            >
                Cari
            </button>

        </form>

        {{-- Desktop --}}
        <div class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 md:block">

            <div class="overflow-x-auto">

                <table class="w-full text-left text-sm">

                    <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">

                        <tr>
                            <th class="px-5 py-4 font-semibold text-gray-600 dark:text-gray-300">
                                Pengguna
                            </th>

                            <th class="px-5 py-4 font-semibold text-gray-600 dark:text-gray-300">
                                Nomor Sekolah
                            </th>

                            <th class="px-5 py-4 font-semibold text-gray-600 dark:text-gray-300">
                                Role
                            </th>

                            <th class="px-5 py-4 font-semibold text-gray-600 dark:text-gray-300">
                                Status
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

                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-100 font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">

                                            <p class="font-medium text-gray-900 dark:text-white">
                                                {{ $user->name }}
                                            </p>

                                            <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                                                {{ $user->email ?? '-' }}
                                            </p>

                                        </div>

                                    </div>

                                </td>

                                <td class="px-5 py-4 text-gray-600 dark:text-gray-300">
                                    {{ $user->school_number ?? '-' }}
                                </td>

                                <td class="px-5 py-4">

                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                        {{ ucfirst($user->role) }}
                                    </span>

                                </td>

                                <td class="px-5 py-4">

                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                        {{ ucfirst($user->status ?? 'active') }}
                                    </span>

                                </td>

                                <td class="px-5 py-4 text-right">

                                    <a
                                        href="{{ route('admin.users.show', $user) }}"
                                        class="font-medium text-gray-700 hover:underline dark:text-gray-300"
                                    >
                                        Detail
                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="5"
                                    class="px-5 py-12 text-center text-sm text-gray-500 dark:text-gray-400"
                                >
                                    Belum ada pengguna.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        {{-- Mobile --}}
        <div class="space-y-3 md:hidden">

            @forelse ($users as $user)

                <a
                    href="{{ route('admin.users.show', $user) }}"
                    class="block rounded-2xl border border-gray-200 bg-white p-4 transition hover:shadow-sm dark:border-gray-700 dark:bg-gray-900"
                >

                    <div class="flex items-center gap-3">

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-gray-100 font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>

                        <div class="min-w-0 flex-1">

                            <div class="flex items-start justify-between gap-3">

                                <h2 class="truncate font-semibold text-gray-900 dark:text-white">
                                    {{ $user->name }}
                                </h2>

                                <span class="shrink-0 rounded-full bg-gray-100 px-2.5 py-1 text-[11px] font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                    {{ ucfirst($user->role) }}
                                </span>

                            </div>

                            <p class="mt-1 truncate text-xs text-gray-500 dark:text-gray-400">
                                {{ $user->email ?? '-' }}
                            </p>

                        </div>

                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-3 border-t border-gray-100 pt-3 dark:border-gray-800">

                        <div>
                            <p class="text-[11px] text-gray-400">
                                Nomor Sekolah
                            </p>

                            <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">
                                {{ $user->school_number ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-[11px] text-gray-400">
                                Status
                            </p>

                            <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">
                                {{ ucfirst($user->status ?? 'active') }}
                            </p>
                        </div>

                    </div>

                </a>

            @empty

                <div class="rounded-2xl border border-gray-200 bg-white p-10 text-center text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-900">
                    Belum ada pengguna.
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
