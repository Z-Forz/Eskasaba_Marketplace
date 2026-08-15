{{-- resources/views/components/sidebar.blade.php --}}

@props([
    'type' => 'admin', {{-- 'admin' | 'seller' --}}
])

@php
    $isAdmin  = $type === 'admin';

    $accentBg     = $isAdmin ? 'bg-blue-600'  : 'bg-emerald-700';
    $accentActive = $isAdmin ? 'bg-blue-50 font-semibold text-blue-600'    : 'bg-emerald-50 font-semibold text-emerald-700';
    $accentHover  = $isAdmin ? 'hover:bg-slate-50 hover:text-blue-600'     : 'hover:bg-slate-50 hover:text-emerald-700';

    $dashboardRoute = $isAdmin ? 'admin.dashboard'  : 'seller.dashboard';
    $logoutRoute    = $isAdmin ? 'admin.logout'     : 'logout';
    $badgeText      = $isAdmin ? 'Panel Admin'      : 'Panel Seller';
    $badgeDesc      = $isAdmin ? 'Kelola marketplace dengan mudah dan aman.' : 'Kelola toko dan produk Anda.';

    $adminMenus = [
        'Data Master' => [
            ['route' => 'admin.users.index',            'pattern' => 'admin.users.*',            'label' => 'Kelola User',         'icon' => 'users'],
            ['route' => 'admin.sellers.index',          'pattern' => 'admin.sellers.*',          'label' => 'Verifikasi Seller',   'icon' => 'store'],
            ['route' => 'admin.categories.index',       'pattern' => 'admin.categories.*',       'label' => 'Kelola Kategori',     'icon' => 'tag'],
            ['route' => 'admin.orders.index',           'pattern' => 'admin.orders.*',           'label' => 'Kelola Pesanan',      'icon' => 'cart'],
            ['route' => 'admin.payments.index',         'pattern' => 'admin.payments.*',         'label' => 'Kelola Pembayaran',   'icon' => 'money'],
        ],
        'Pengaturan' => [
            ['route' => 'admin.website-settings.index', 'pattern' => 'admin.website-settings.*', 'label' => 'Pengaturan Website',  'icon' => 'settings'],
            ['route' => 'profile.index',                'pattern' => 'profile.*',                'label' => 'Pengaturan Akun',     'icon' => 'account'],
        ],
    ];

    $sellerMenus = [
        'Toko' => [
            ['route' => 'seller.products.index',         'pattern' => 'seller.products.*',         'label' => 'Produk Saya',       'icon' => 'tag'],
            ['route' => 'seller.orders.index',           'pattern' => 'seller.orders.*',           'label' => 'Pesanan',           'icon' => 'cart'],
            ['route' => 'seller.payments.index',         'pattern' => 'seller.payments.*',         'label' => 'Pembayaran',        'icon' => 'money'],
            ['route' => 'seller.pickup-schedules.index', 'pattern' => 'seller.pickup-schedules.*', 'label' => 'Jadwal Pengambilan','icon' => 'calendar'],
        ],
        'Komunikasi' => [
            ['route' => 'seller.chats.index',            'pattern' => 'seller.chats.*',            'label' => 'Chat',              'icon' => 'chat'],
        ],
        'Akun' => [
            ['route' => 'profile.index',                 'pattern' => 'profile.*',                 'label' => 'Profil',            'icon' => 'account'],
        ],
    ];

    $menus = $isAdmin ? $adminMenus : $sellerMenus;

    // SVG icon map
    $icons = [
        'users'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path stroke-linecap="round" stroke-linejoin="round" d="M22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>',
        'store'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-6 9 6v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 22V12h6v10"/>',
        'tag'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M3 3h8l9 9a2 2 0 010 2.83l-5.17 5.17a2 2 0 01-2.83 0L3 11V3z"/>',
        'cart'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l2 13h10l3-9H6"/><circle cx="10" cy="20" r="1"/><circle cx="18" cy="20" r="1"/>',
        'money'    => '<rect x="2" y="5" width="20" height="14" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M2 10h20"/>',
        'settings' => '<circle cx="12" cy="12" r="3"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/>',
        'account'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
        'chat'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>',
    ];
@endphp

{{-- Desktop Sidebar --}}
<aside class="fixed inset-y-0 left-0 z-50 hidden w-56 flex-col border-r border-slate-200 bg-white lg:flex">

    {{-- Logo --}}
    <div class="flex h-20 items-center border-b border-slate-100 px-5">
        <a href="{{ route($dashboardRoute) }}" class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $accentBg }} text-white">
                @if ($isAdmin)
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7l8-4z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/>
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-6 9 6v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 22V12h6v10"/>
                    </svg>
                @endif
            </div>
            <div>
                <p class="text-sm font-bold text-slate-900">ESKASABA</p>
                <p class="text-[10px] text-slate-400">{{ $isAdmin ? 'Marketplace Admin' : 'Marketplace Seller' }}</p>
            </div>
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-3 py-5">

        {{-- Dashboard --}}
        <a
            href="{{ route($dashboardRoute) }}"
            class="mb-2 flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold transition
                {{ request()->routeIs($dashboardRoute) ? $accentBg . ' text-white shadow-sm' : 'text-slate-600 ' . $accentHover }}"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5L12 3l9 7.5V21a1 1 0 01-1 1H4a1 1 0 01-1-1V10.5z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 22v-7h6v7"/>
            </svg>
            <span>Dashboard</span>
        </a>

        {{-- Grouped Menu Items --}}
        @foreach ($menus as $groupLabel => $items)

            <p class="mb-2 mt-7 px-4 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                {{ $groupLabel }}
            </p>

            @foreach ($items as $item)
                <a
                    href="{{ route($item['route']) }}"
                    class="mb-1 flex items-center justify-between rounded-lg px-4 py-3 text-sm transition
                        {{ request()->routeIs($item['pattern']) ? $accentActive : 'text-slate-600 ' . $accentHover }}"
                >
                    <span class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            {!! $icons[$item['icon']] ?? '' !!}
                        </svg>
                        <span>{{ $item['label'] }}</span>
                    </span>
                    <span class="text-slate-300">›</span>
                </a>
            @endforeach

        @endforeach

    </nav>

    {{-- Badge Panel --}}
    <div class="p-3">
        <div class="rounded-xl {{ $accentBg }} p-4 text-white">
            <p class="text-sm font-bold">{{ $badgeText }}</p>
            <p class="mt-1 text-xs leading-relaxed opacity-80">{{ $badgeDesc }}</p>
        </div>
    </div>

    {{-- Logout --}}
    <div class="border-t border-slate-100 p-3">
        <form method="POST" action="{{ route($logoutRoute) }}">
            @csrf
            <button
                type="submit"
                class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-red-600 transition hover:bg-red-50"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 17l5-5-5-5"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H3"/>
                </svg>
                <span>Logout</span>
            </button>
        </form>
    </div>

</aside>
