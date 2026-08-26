<x-layouts.admin title="Verifikasi Seller">

    <div class="space-y-6">

        {{-- Header Section --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-user-check text-emerald-600"></i> Verifikasi Pengajuan Seller
                </h1>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Tinjau dan verifikasi pengajuan pendaftaran seller dari siswa dan guru.
                </p>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-sm font-medium text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-300">
                ✓ {{ session('success') }}
            </div>
        @endif

        {{-- Filter Tabs Bar --}}
        <div class="flex flex-wrap gap-2 rounded-2xl border border-slate-200 bg-white p-2 shadow-xs dark:border-gray-700 dark:bg-gray-900">
            @php
                $tabs = [
                    'pending'  => ['label' => 'Menunggu Verifikasi', 'color' => 'blue'],
                    'revision' => ['label' => 'Perlu Revisi',        'color' => 'amber'],
                    'rejected' => ['label' => 'Ditolak',             'color' => 'red'],
                    'all'      => ['label' => 'Semua Pengajuan',     'color' => 'slate'],
                ];
            @endphp

            @foreach ($tabs as $key => $tab)
                @php
                    $isActive = $status === $key;
                    $count = $counts[$key] ?? 0;
                    
                    if ($isActive) {
                        $activeClass = match ($tab['color']) {
                            'blue'    => 'bg-blue-600 text-white font-bold shadow-xs',
                            'amber'   => 'bg-amber-600 text-white font-bold shadow-xs',
                            'red'     => 'bg-red-600 text-white font-bold shadow-xs',
                            default   => 'bg-slate-900 text-white font-bold shadow-xs dark:bg-white dark:text-slate-900',
                        };
                    } else {
                        $activeClass = 'bg-slate-50 text-slate-600 hover:bg-slate-100 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 font-medium';
                    }
                @endphp

                <a
                    href="{{ route('admin.sellers.verifications', ['status' => $key]) }}"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm transition {{ $activeClass }}"
                >
                    <span>{{ $tab['label'] }}</span>

                    <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700 dark:bg-gray-700 dark:text-gray-300' }}">
                        {{ number_format($count) }}
                    </span>
                </a>
            @endforeach
        </div>

        {{-- List Container --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xs dark:border-gray-700 dark:bg-gray-900">

            @if ($sellers->count())

                <div class="hidden overflow-x-auto md:block">

                    <table class="w-full text-left text-sm">

                        <thead class="border-b border-slate-100 bg-slate-50/80 text-xs uppercase tracking-wider text-slate-500 dark:border-gray-800 dark:bg-gray-800/80 dark:text-gray-400">

                            <tr>
                                <th class="px-6 py-4">Pemohon</th>
                                <th class="px-6 py-4">Identitas Sekolah</th>
                                <th class="px-6 py-4">No. WA Pendaftar</th>
                                <th class="px-6 py-4">Status Pengajuan</th>
                                <th class="px-6 py-4">Tanggal Pengajuan</th>
                                <th class="px-6 py-4 text-right">Aksi Verifikasi</th>
                            </tr>

                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-gray-800">

                            @foreach ($sellers as $seller)

                                <tr class="transition hover:bg-slate-50/60 dark:hover:bg-gray-800/50">

                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-800 text-sm font-bold text-white shadow-xs">
                                                {{ strtoupper(substr($seller->user?->username ?? 'S', 0, 1)) }}
                                            </div>

                                            <div class="min-w-0">
                                                <div class="font-bold text-slate-900 dark:text-white">
                                                    {{ $seller->user?->username ?? '-' }}
                                                </div>

                                                <div class="text-xs text-slate-500 dark:text-gray-400">
                                                    {{ $seller->user?->email ?? '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-xs">
                                        @if ($seller->user?->nis_nip)
                                            <p class="font-semibold text-slate-800 dark:text-gray-200">
                                                NIS/NIP: {{ $seller->user->nis_nip }}
                                            </p>
                                            <p class="text-slate-500">
                                                {{ $seller->user->role === 'teacher' ? 'Guru Sekolah' : 'Siswa Sekolah' }}
                                            </p>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-xs">
                                        @php
                                            $waNum = $seller->whatsapp_number ?: $seller->user?->phone;
                                        @endphp
                                        @if ($waNum)
                                            <a
                                                href="https://wa.me/{{ preg_replace('/\D/', '', $waNum) }}"
                                                target="_blank"
                                                class="inline-flex items-center gap-1.5 font-bold text-emerald-700 hover:underline dark:text-emerald-400"
                                            >
                                                <i class="fa-brands fa-whatsapp text-sm text-emerald-600"></i> {{ $waNum }}
                                            </a>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold
                                            {{ $seller->status === 'approved'
                                                ? 'bg-green-100 text-green-700'
                                                : ($seller->status === 'rejected'
                                                    ? 'bg-red-100 text-red-700'
                                                    : ($seller->status === 'revision'
                                                        ? 'bg-amber-100 text-amber-700'
                                                        : 'bg-blue-100 text-blue-700')) }}"
                                        >
                                            {{ $seller->statusLabel() }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-xs text-slate-500 dark:text-gray-400">
                                        {{ $seller->created_at?->format('d M Y H:i') ?? '-' }}
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        <a
                                            href="{{ route('admin.sellers.show', $seller) }}"
                                            class="inline-flex items-center gap-1 rounded-xl bg-emerald-700 px-4 py-2 text-xs font-semibold text-white transition hover:bg-emerald-800 shadow-xs"
                                        >
                                            Tinjau & Verifikasi <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                {{-- Mobile View --}}
                <div class="divide-y divide-slate-100 md:hidden dark:divide-gray-800">
                    @foreach ($sellers as $seller)
                        <div class="space-y-3 p-5">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-800 text-sm font-bold text-white shadow-xs">
                                        {{ strtoupper(substr($seller->user?->username ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <h2 class="font-bold text-slate-900 dark:text-white">
                                            {{ $seller->user?->username ?? '-' }}
                                        </h2>
                                        <p class="text-xs text-slate-500">
                                            {{ $seller->user?->email ?? '-' }}
                                        </p>
                                    </div>
                                </div>

                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold bg-blue-100 text-blue-700">
                                    {{ $seller->statusLabel() }}
                                </span>
                            </div>

                            <a
                                href="{{ route('admin.sellers.show', $seller) }}"
                                class="block w-full rounded-xl bg-emerald-700 py-2.5 text-center text-xs font-semibold text-white shadow-xs"
                            >
                                Tinjau & Verifikasi <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    @endforeach
                </div>

            @else

                <div class="p-12 text-center">
                    <div class="text-5xl">✅</div>
                    <h2 class="mt-4 text-lg font-bold text-slate-900 dark:text-white">
                        Tidak Ada Pengajuan Menunggu
                    </h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Seluruh pengajuan seller telah selesai diverifikasi.
                    </p>
                </div>

            @endif

        </div>

        @if (method_exists($sellers, 'links'))
            {{ $sellers->links() }}
        @endif

    </div>

</x-layouts.admin>
