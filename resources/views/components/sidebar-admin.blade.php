@php
    $adminMenus = [
        'Data Master & Kelola' => [
            ['route' => 'admin.users.index',            'pattern' => 'admin.users.*',            'label' => 'Kelola User',         'icon' => 'fa-solid fa-users'],
            ['route' => 'admin.sellers.index',          'pattern' => 'admin.sellers.index',      'label' => 'Seller Aktif',        'icon' => 'fa-solid fa-store'],
            ['route' => 'admin.sellers.verifications',  'pattern' => 'admin.sellers.verifications', 'label' => 'Verifikasi Seller', 'icon' => 'fa-solid fa-user-check'],
            ['route' => 'admin.categories.index',       'pattern' => 'admin.categories.*',       'label' => 'Kelola Kategori',     'icon' => 'fa-solid fa-layer-group'],
            ['route' => 'admin.orders.index',           'pattern' => 'admin.orders.*',           'label' => 'Kelola Pesanan',      'icon' => 'fa-solid fa-receipt'],
            ['route' => 'admin.payments.index',         'pattern' => 'admin.payments.*',         'label' => 'Kelola Pembayaran',   'icon' => 'fa-solid fa-credit-card'],
        ],
        'Laporan & Analistik' => [
            ['route' => 'admin.reports.products',       'pattern' => 'admin.reports.products',   'label' => 'Laporan Produk',      'icon' => 'fa-solid fa-boxes-stacked'],
            ['route' => 'admin.reports.sales',          'pattern' => 'admin.reports.sales',      'label' => 'Laporan Penjualan',   'icon' => 'fa-solid fa-chart-line'],
        ],
        'Pengaturan' => [
            ['route' => 'admin.website-settings.index', 'pattern' => 'admin.website-settings.*', 'label' => 'Pengaturan Website',  'icon' => 'fa-solid fa-gear'],
        ],
    ];
@endphp

{{-- DESKTOP SIDEBAR ADMIN --}}
<aside class="fixed inset-y-0 left-0 z-50 hidden w-56 flex-col border-r border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 lg:flex">

    {{-- Logo Header --}}
    <div class="flex h-20 items-center border-b border-slate-100 px-5 dark:border-slate-800">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-800 text-white shadow-xs">
                <i class="fa-solid fa-user-shield text-lg"></i>
            </div>
            <div>
                <p class="text-sm font-black text-slate-900 dark:text-white">ESKASABA</p>
                <p class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">Admin Panel</p>
            </div>
        </a>
    </div>

    {{-- Navigation Links --}}
    <nav class="flex-1 overflow-y-auto px-3 py-5 scrollbar-none">

        {{-- Dashboard Link --}}
        <a
            href="{{ route('admin.dashboard') }}"
            class="mb-2 flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-bold transition
                {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-800 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-700 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-emerald-400' }}"
        >
            <i class="fa-solid fa-gauge-high text-base w-5 text-center"></i>
            <span>Dashboard</span>
        </a>

        @foreach ($adminMenus as $groupLabel => $items)

            <p class="mb-1.5 mt-5 px-4 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                {{ $groupLabel }}
            </p>

            @foreach ($items as $item)
                <a
                    href="{{ route($item['route']) }}"
                    class="mb-1 flex items-center justify-between rounded-xl px-4 py-2.5 text-sm transition
                        {{ request()->routeIs($item['pattern']) ? 'bg-emerald-50 font-bold text-emerald-900 border-l-4 border-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300 dark:border-emerald-500' : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-700 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-emerald-400' }}"
                >
                    <span class="flex items-center gap-3">
                        <i class="{{ $item['icon'] }} text-sm w-5 text-center"></i>
                        <span>{{ $item['label'] }}</span>
                    </span>
                    <span class="text-slate-300 text-xs">›</span>
                </a>
            @endforeach

        @endforeach

    </nav>

    {{-- Badge Banner --}}
    <div class="p-3">
        <div class="rounded-2xl bg-emerald-800 p-4 text-white shadow-xs">
            <p class="text-xs font-black uppercase tracking-wider">Panel Administrator</p>
            <p class="mt-1 text-[11px] leading-relaxed opacity-90 font-medium">Kelola marketplace sekolah dengan mudah & aman.</p>
        </div>
    </div>

    {{-- Logout --}}
    <div class="border-t border-slate-100 p-3 dark:border-slate-800">
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button
                type="submit"
                class="flex w-full items-center gap-3 rounded-xl px-4 py-2.5 text-xs font-bold text-red-600 transition hover:bg-red-50 dark:hover:bg-red-950/40"
            >
                <i class="fa-solid fa-right-from-bracket text-sm w-5 text-center"></i>
                <span>Logout Admin</span>
            </button>
        </form>
    </div>

</aside>


{{-- MOBILE SIDEBAR DRAWER ADMIN --}}
<div
    id="admin-mobile-sidebar-drawer"
    class="fixed inset-0 z-50 hidden lg:hidden"
>
    <div
        id="admin-mobile-backdrop"
        class="absolute inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity"
    ></div>

    <aside class="relative z-10 flex h-full w-[17.5rem] flex-col border-r border-slate-200/80 bg-white shadow-2xl dark:border-slate-800 dark:bg-slate-900">

        <div class="flex h-16 items-center justify-between border-b border-slate-100 px-5 dark:border-slate-800">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-800 text-white shadow-xs">
                    <i class="fa-solid fa-user-shield text-base"></i>
                </div>
                <div>
                    <p class="text-sm font-black text-slate-900 dark:text-white">ESKASABA</p>
                    <p class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">Admin Panel</p>
                </div>
            </a>

            <button
                type="button"
                id="admin-mobile-close-btn"
                class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-400"
            >
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 scrollbar-none">

            <a
                href="{{ route('admin.dashboard') }}"
                class="mb-2 flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-bold transition
                    {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-800 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-700 dark:text-slate-300 dark:hover:bg-slate-800' }}"
            >
                <i class="fa-solid fa-gauge-high text-base w-5 text-center"></i>
                <span>Dashboard</span>
            </a>

            @foreach ($adminMenus as $groupLabel => $items)

                <p class="mb-1.5 mt-4 px-4 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    {{ $groupLabel }}
                </p>

                @foreach ($items as $item)
                    <a
                        href="{{ route($item['route']) }}"
                        class="mb-1 flex items-center justify-between rounded-xl px-4 py-2.5 text-sm transition
                            {{ request()->routeIs($item['pattern']) ? 'bg-emerald-50 font-bold text-emerald-900 border-l-4 border-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300 dark:border-emerald-500' : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-700 dark:text-slate-300 dark:hover:bg-slate-800' }}"
                    >
                        <span class="flex items-center gap-3">
                            <i class="{{ $item['icon'] }} text-sm w-5 text-center"></i>
                            <span>{{ $item['label'] }}</span>
                        </span>
                        <span class="text-slate-300 text-xs">›</span>
                    </a>
                @endforeach

            @endforeach

        </nav>

        <div class="border-t border-slate-100 p-4 dark:border-slate-800">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button
                    type="submit"
                    class="flex w-full items-center gap-3 rounded-xl px-4 py-2.5 text-xs font-bold text-red-600 transition hover:bg-red-50 dark:hover:bg-red-950/40"
                >
                    <i class="fa-solid fa-right-from-bracket text-sm w-5 text-center"></i>
                    <span>Logout Admin</span>
                </button>
            </form>
        </div>

    </aside>
</div>

<script>
    (function () {
        function initAdminMobileSidebar() {
            const toggleBtns = document.querySelectorAll('.admin-mobile-sidebar-toggle-btn');
            const drawer = document.getElementById('admin-mobile-sidebar-drawer');
            const closeBtn = document.getElementById('admin-mobile-close-btn');
            const backdrop = document.getElementById('admin-mobile-backdrop');

            if (!drawer) return;

            function openSidebar() {
                drawer.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeSidebar() {
                drawer.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            toggleBtns.forEach(btn => {
                btn.onclick = function (e) {
                    e.stopPropagation();
                    openSidebar();
                };
            });

            if (closeBtn) closeBtn.onclick = closeSidebar;
            if (backdrop) backdrop.onclick = closeSidebar;
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initAdminMobileSidebar);
        } else {
            initAdminMobileSidebar();
        }
    })();
</script>
