<x-layouts.admin>

    <div class="space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Seller
            </h1>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Kelola pendaftaran dan status seller marketplace.
            </p>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-900/50 dark:bg-green-950/30 dark:text-green-400">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">

            @if ($sellers->count())

                <div class="hidden overflow-x-auto md:block">

                    <table class="w-full text-left text-sm">

                        <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">

                            <tr>
                                <th class="px-6 py-4">Pengguna</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">

                            @foreach ($sellers as $seller)

                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">

                                    <td class="px-6 py-4">

                                        <div class="font-semibold text-gray-900 dark:text-white">
                                            {{ $seller->user->name ?? '-' }}
                                        </div>

                                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            {{ $seller->user->email ?? '-' }}
                                        </div>

                                    </td>

                                    <td class="px-6 py-4">

                                        @php
                                            $status = $seller->status;
                                        @endphp

                                        <span class="rounded-full px-3 py-1 text-xs font-semibold
                                            {{ $status === 'approved'
                                                ? 'bg-green-100 text-green-700 dark:bg-green-950/40 dark:text-green-400'
                                                : ($status === 'rejected'
                                                    ? 'bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-400'
                                                    : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-950/40 dark:text-yellow-400') }}"
                                        >
                                            {{ ucfirst($status) }}
                                        </span>

                                    </td>

                                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                        {{ $seller->created_at?->format('d M Y') ?? '-' }}
                                    </td>

                                    <td class="px-6 py-4">

                                        <div class="flex justify-end gap-2">

                                            <a
                                                href="{{ route('admin.sellers.show', $seller) }}"
                                                class="rounded-lg px-3 py-2 text-xs font-semibold hover:bg-gray-100 dark:hover:bg-gray-800"
                                            >
                                                Detail
                                            </a>

                                            <a
                                                href="{{ route('admin.sellers.edit', $seller) }}"
                                                class="rounded-lg px-3 py-2 text-xs font-semibold hover:bg-gray-100 dark:hover:bg-gray-800"
                                            >
                                                Kelola
                                            </a>

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                {{-- Mobile --}}
                <div class="divide-y divide-gray-100 md:hidden dark:divide-gray-800">

                    @foreach ($sellers as $seller)

                        <div class="space-y-4 p-5">

                            <div>

                                <h2 class="font-semibold text-gray-900 dark:text-white">
                                    {{ $seller->user->name ?? '-' }}
                                </h2>

                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $seller->user->email ?? '-' }}
                                </p>

                            </div>

                            <div class="flex items-center justify-between">

                                <span class="rounded-full px-3 py-1 text-xs font-semibold
                                    {{ $seller->status === 'approved'
                                        ? 'bg-green-100 text-green-700'
                                        : ($seller->status === 'rejected'
                                            ? 'bg-red-100 text-red-700'
                                            : 'bg-yellow-100 text-yellow-700') }}"
                                >
                                    {{ ucfirst($seller->status) }}
                                </span>

                                <span class="text-xs text-gray-500">
                                    {{ $seller->created_at?->format('d M Y') ?? '-' }}
                                </span>

                            </div>

                            <div class="flex gap-2">

                                <a
                                    href="{{ route('admin.sellers.show', $seller) }}"
                                    class="rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold dark:border-gray-700"
                                >
                                    Detail
                                </a>

                                <a
                                    href="{{ route('admin.sellers.edit', $seller) }}"
                                    class="rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold dark:border-gray-700"
                                >
                                    Kelola
                                </a>

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="p-10 text-center">

                    <div class="text-4xl">🛍️</div>

                    <h2 class="mt-4 font-semibold text-gray-900 dark:text-white">
                        Belum ada pendaftaran seller
                    </h2>

                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Pendaftaran seller yang masuk akan muncul di sini.
                    </p>

                </div>

            @endif

        </div>

        @if (method_exists($sellers, 'links'))
            {{ $sellers->links() }}
        @endif

    </div>

</x-layouts.admin>
```
