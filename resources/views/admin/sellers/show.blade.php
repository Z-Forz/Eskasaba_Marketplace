<x-layouts.admin>

    <div class="space-y-6">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <a
                    href="{{ route('admin.sellers.index') }}"
                    class="text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                >
                    ← Kembali ke seller
                </a>

                <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">
                    Detail Seller
                </h1>

            </div>

            <a
                href="{{ route('admin.sellers.edit', $seller) }}"
                class="rounded-xl bg-gray-900 px-5 py-3 text-center text-sm font-semibold text-white dark:bg-white dark:text-gray-900"
            >
                Kelola Seller
            </a>

        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- Profile --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 lg:col-span-1 dark:border-gray-700 dark:bg-gray-900">

                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-gray-100 text-2xl font-bold text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                    {{ strtoupper(substr($seller->user?->name ?? 'S', 0, 1)) }}
                </div>

                <h2 class="mt-5 text-xl font-bold text-gray-900 dark:text-white">
                    {{ $seller->user?->name ?? '-' }}
                </h2>

                <p class="mt-1 break-all text-sm text-gray-500 dark:text-gray-400">
                    {{ $seller->user?->email ?? '-' }}
                </p>

                <div class="mt-4">

                    <span class="rounded-full px-3 py-1 text-xs font-semibold
                        {{ $seller->status === 'approved'
                            ? 'bg-green-100 text-green-700 dark:bg-green-950/40 dark:text-green-400'
                            : ($seller->status === 'rejected'
                                ? 'bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-400'
                                : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-950/40 dark:text-yellow-400') }}"
                    >
                        {{ ucfirst($seller->status) }}
                    </span>

                </div>

            </section>

            {{-- Information --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 lg:col-span-2 dark:border-gray-700 dark:bg-gray-900">

                <h2 class="font-semibold text-gray-900 dark:text-white">
                    Informasi Seller
                </h2>

                <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">

                    <div>
                        <p class="text-xs text-gray-400">
                            Status
                        </p>

                        <p class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                            {{ ucfirst($seller->status) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400">
                            Terdaftar
                        </p>

                        <p class="mt-1 text-sm text-gray-800 dark:text-gray-200">
                            {{ $seller->created_at?->format('d F Y') ?? '-' }}
                        </p>
                    </div>

                    <div class="sm:col-span-2">

                        <p class="text-xs text-gray-400">
                            Deskripsi
                        </p>

                        <p class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300">
                            {{ $seller->description ?: 'Belum ada deskripsi seller.' }}
                        </p>

                    </div>

                    @if ($seller->approved_at)

                        <div>

                            <p class="text-xs text-gray-400">
                                Disetujui
                            </p>

                            <p class="mt-1 text-sm text-gray-800 dark:text-gray-200">
                                {{ $seller->approved_at->format('d F Y H:i') }}
                            </p>

                        </div>

                    @endif

                </div>

            </section>

        </div>

        {{-- School identity --}}
        @if ($seller->user?->school_number)

            <section class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-700 dark:bg-gray-900">

                <h2 class="font-semibold text-gray-900 dark:text-white">
                    Identitas Sekolah
                </h2>

                <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">

                    <div>
                        <p class="text-xs text-gray-400">Nama</p>
                        <p class="mt-1 text-sm font-medium dark:text-gray-200">
                            {{ $seller->user->name ?? $seller->user->username }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400">NIS / NIP</p>
                        <p class="mt-1 text-sm font-medium dark:text-gray-200">
                            {{ $seller->user->school_number }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400">Kelas</p>
                        <p class="mt-1 text-sm font-medium dark:text-gray-200">
                            {{ $seller->user->class ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400">Jurusan</p>
                        <p class="mt-1 text-sm font-medium dark:text-gray-200">
                            {{ $seller->user->major ?? '-' }}
                        </p>
                    </div>

                </div>

            </section>

        @endif

    </div>

</x-layouts.admin>