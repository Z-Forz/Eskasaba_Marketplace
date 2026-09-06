@props([
    'transparent' => false,
])

<nav
    id="main-navbar"
    class="sticky top-0 z-50 w-full border-b border-slate-200/80 bg-white/95 backdrop-blur-xl"
>
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">

        {{-- Logo --}}
        <a
            href="{{ route('home') }}"
            class="flex min-w-0 items-center gap-2.5 sm:gap-3"
        >
            @if(isset($settings) && $settings->logo)
                <img
                    src="{{ asset('storage/' . $settings->logo) }}"
                    alt="{{ $settings->website_name ?? 'Eskasaba Market' }}"
                    class="h-9 w-9 shrink-0 rounded-xl object-cover sm:h-10 sm:w-10"
                >
            @else
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-600 to-teal-600 text-sm font-bold text-white sm:h-10 sm:w-10 shadow-md shadow-emerald-600/20">
                    <i class="fa-solid fa-shop"></i>
                </div>
            @endif

            <span class="flex items-center gap-1 shrink-0 font-black tracking-tight">
                <span class="text-sm xs:text-base sm:text-lg text-slate-900 dark:text-white">
                    Eskasaba
                </span>
                <span class="text-sm xs:text-base sm:text-lg text-emerald-600 dark:text-emerald-400">
                    Marketplace
                </span>
            </span>
        </a>

        {{-- Desktop Navigation Links --}}
        <div class="hidden items-center gap-2 md:flex">

            <a
                href="{{ route('home') }}"
                class="px-3.5 py-2 rounded-xl text-sm transition flex items-center gap-2 {{ request()->routeIs('home') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-xs ring-1 ring-emerald-200' : 'text-slate-600 font-semibold hover:bg-emerald-50/60 hover:text-emerald-700' }}"
            >
                <i class="fa-solid fa-house text-xs text-emerald-600"></i> Beranda
            </a>

            <a
                href="{{ route('products.index') }}"
                class="px-3.5 py-2 rounded-xl text-sm transition flex items-center gap-2 {{ request()->routeIs('products.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-xs ring-1 ring-emerald-200' : 'text-slate-600 font-semibold hover:bg-emerald-50/60 hover:text-emerald-700' }}"
            >
                <i class="fa-solid fa-bag-shopping text-xs text-emerald-600"></i> Produk
            </a>

            <a
                href="{{ route('guide') }}"
                class="px-3.5 py-2 rounded-xl text-sm transition flex items-center gap-2 {{ request()->routeIs('guide') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-xs ring-1 ring-emerald-200' : 'text-slate-600 font-semibold hover:bg-emerald-50/60 hover:text-emerald-700' }}"
            >
                <i class="fa-solid fa-book-open text-xs text-emerald-600"></i> Panduan
            </a>

            <a
                href="{{ route('about') }}"
                class="px-3.5 py-2 rounded-xl text-sm transition flex items-center gap-2 {{ request()->routeIs('about') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-xs ring-1 ring-emerald-200' : 'text-slate-600 font-semibold hover:bg-emerald-50/60 hover:text-emerald-700' }}"
            >
                <i class="fa-solid fa-circle-info text-xs text-emerald-600"></i> Tentang
            </a>

        </div>

        {{-- Desktop Right Actions --}}
        <div class="hidden items-center gap-2 md:flex">

            @auth

                {{-- Cart Button --}}
                <a
                    href="{{ route('buyer.cart.index') }}"
                    class="rounded-xl p-2.5 transition flex items-center justify-center {{ request()->routeIs('buyer.cart.*') ? 'bg-emerald-100/80 text-emerald-800 shadow-xs ring-1 ring-emerald-300' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-700' }}"
                    aria-label="Keranjang"
                >
                    <i class="fa-solid fa-cart-shopping text-base"></i>
                </a>

                {{-- Notifications Button --}}
                @php
                    $unreadCount = auth()->user()->notifications()->where('is_read', false)->count();
                @endphp
                <a
                    href="{{ route('buyer.notifications.index') }}"
                    class="relative rounded-xl p-2.5 transition flex items-center justify-center {{ request()->routeIs('buyer.notifications.*') ? 'bg-emerald-100/80 text-emerald-800 shadow-xs ring-1 ring-emerald-300' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-700' }}"
                    aria-label="Notifikasi"
                >
                    <i class="fa-solid fa-bell text-base"></i>
                    @if ($unreadCount > 0)
                        <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white shadow-xs">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </a>

                {{-- Profile Card --}}
                <a
                    href="{{ route('profile.index') }}"
                    class="ml-1 flex items-center gap-2.5 rounded-xl p-1.5 transition hover:bg-slate-50 {{ request()->routeIs('profile.*') ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-300 shadow-xs' : '' }}"
                >
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-emerald-600 to-teal-600 text-sm font-bold text-white shadow-md shadow-emerald-600/20">
                        {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                    </div>

                    <div class="max-w-32">
                        <p class="truncate text-sm font-bold text-slate-800">
                            {{ auth()->user()->username }}
                        </p>

                        <p class="text-xs text-emerald-600 font-bold">
                            @if(auth()->user()->seller?->status === 'approved')
                                Seller Toko
                            @else
                                {{ auth()->user()->role === 'teacher' ? 'Guru' : 'Siswa' }}
                            @endif
                        </p>
                    </div>
                </a>

            @else

                <a
                    href="{{ route('login') }}"
                    class="rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-2 text-sm font-bold text-white shadow-md shadow-emerald-600/20 transition hover:from-emerald-700 hover:to-teal-700 flex items-center gap-2"
                >
                    <i class="fa-solid fa-right-to-bracket"></i> Masuk
                </a>

            @endauth

        </div>

        {{-- Mobile Right Bar Icon Button --}}
        <div class="flex items-center md:hidden">
            <button
                type="button"
                id="mobile-nav-toggle-btn"
                class="inline-flex items-center justify-center rounded-xl p-2.5 text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800 cursor-pointer"
                aria-label="Buka menu navigasi"
            >
                <i class="fa-solid fa-bars text-2xl"></i>
            </button>
        </div>

    </div>

    {{-- Mobile Menu Drawer --}}
    <div
        id="mobile-nav-drawer"
        class="hidden border-t border-slate-200/80 bg-white/98 backdrop-blur-xl dark:border-slate-800 dark:bg-slate-900/98 md:hidden shadow-2xl"
    >
        <div class="space-y-1.5 px-4 py-4 sm:px-6">

            <a
                href="{{ route('home') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition {{ request()->routeIs('home') ? 'bg-emerald-100/80 text-emerald-900 font-bold border-l-4 border-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 dark:border-emerald-500 shadow-xs' : 'text-slate-600 dark:text-slate-300 font-medium hover:bg-slate-50 dark:hover:bg-slate-800' }}"
            >
                <i class="fa-solid fa-house w-5 text-center text-emerald-600"></i> Beranda
            </a>

            <a
                href="{{ route('products.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition {{ request()->routeIs('products.*') ? 'bg-emerald-100/80 text-emerald-900 font-bold border-l-4 border-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 dark:border-emerald-500 shadow-xs' : 'text-slate-600 dark:text-slate-300 font-medium hover:bg-slate-50 dark:hover:bg-slate-800' }}"
            >
                <i class="fa-solid fa-bag-shopping w-5 text-center text-emerald-600"></i> Produk Katalog
            </a>

            <a
                href="{{ route('guide') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition {{ request()->routeIs('guide') ? 'bg-emerald-100/80 text-emerald-900 font-bold border-l-4 border-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 dark:border-emerald-500 shadow-xs' : 'text-slate-600 dark:text-slate-300 font-medium hover:bg-slate-50 dark:hover:bg-slate-800' }}"
            >
                <i class="fa-solid fa-book-open w-5 text-center text-emerald-600"></i> Panduan COD
            </a>

            <a
                href="{{ route('about') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition {{ request()->routeIs('about') ? 'bg-emerald-100/80 text-emerald-900 font-bold border-l-4 border-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 dark:border-emerald-500 shadow-xs' : 'text-slate-600 dark:text-slate-300 font-medium hover:bg-slate-50 dark:hover:bg-slate-800' }}"
            >
                <i class="fa-solid fa-circle-info w-5 text-center text-emerald-600"></i> Tentang Kami
            </a>

            @auth
                @php
                    $cartCount = auth()->user()->cart?->items()->sum('quantity') ?? 0;
                    $unreadNotifCount = auth()->user()->notifications()->where('is_read', false)->count();
                @endphp

                <a
                    href="{{ route('buyer.cart.index') }}"
                    class="flex items-center justify-between px-4 py-3 rounded-xl text-sm transition {{ request()->routeIs('buyer.cart.*') ? 'bg-emerald-100/80 text-emerald-900 font-bold border-l-4 border-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 dark:border-emerald-500 shadow-xs' : 'text-slate-600 dark:text-slate-300 font-medium hover:bg-slate-50 dark:hover:bg-slate-800' }}"
                >
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-cart-shopping w-5 text-center text-emerald-600"></i> Keranjang Belanja
                    </span>
                    @if ($cartCount > 0)
                        <span class="rounded-full bg-emerald-600 px-2.5 py-0.5 text-xs font-bold text-white shadow-xs">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>

                <a
                    href="{{ route('buyer.notifications.index') }}"
                    class="flex items-center justify-between px-4 py-3 rounded-xl text-sm transition {{ request()->routeIs('buyer.notifications.*') ? 'bg-emerald-100/80 text-emerald-900 font-bold border-l-4 border-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 dark:border-emerald-500 shadow-xs' : 'text-slate-600 dark:text-slate-300 font-medium hover:bg-slate-50 dark:hover:bg-slate-800' }}"
                >
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-bell w-5 text-center text-emerald-600"></i> Notifikasi
                    </span>
                    @if ($unreadNotifCount > 0)
                        <span class="rounded-full bg-red-500 px-2.5 py-0.5 text-xs font-bold text-white shadow-xs">
                            {{ $unreadNotifCount > 9 ? '9+' : $unreadNotifCount }}
                        </span>
                    @endif
                </a>

                <a
                    href="{{ route('buyer.orders.index') }}"
                    class="flex items-center justify-between px-4 py-3 rounded-xl text-sm transition {{ request()->routeIs('buyer.orders.*') ? 'bg-emerald-100/80 text-emerald-900 font-bold border-l-4 border-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 dark:border-emerald-500 shadow-xs' : 'text-slate-600 dark:text-slate-300 font-medium hover:bg-slate-50 dark:hover:bg-slate-800' }}"
                >
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-box-open w-5 text-center text-emerald-600"></i> Pesanan Saya
                    </span>
                    <i class="fa-solid fa-chevron-right text-xs text-slate-400"></i>
                </a>

                @if (auth()->user()->role === 'admin')
                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold bg-amber-500/10 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-500/30"
                    >
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-shield-halved w-5 text-center text-amber-600"></i> Dashboard Admin
                        </span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                @endif

                @if (auth()->user()->seller?->isApproved())
                    <a
                        href="{{ route('seller.dashboard') }}"
                        class="flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold bg-emerald-500/10 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-500/30"
                    >
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-store w-5 text-center text-emerald-600"></i> Dashboard Seller Toko
                        </span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                @endif

                {{-- User Profile Nav Section --}}
                <div class="mt-4 border-t border-slate-100 dark:border-slate-800 pt-4">
                    <div class="flex items-center justify-between gap-3 rounded-2xl border p-3.5 {{ request()->routeIs('profile.*') ? 'border-emerald-300 bg-emerald-100/70 ring-2 ring-emerald-600/20 shadow-sm dark:border-emerald-800 dark:bg-emerald-950/60' : 'border-slate-200/80 bg-slate-50 dark:border-slate-800 dark:bg-slate-800/60' }}">
                        <a href="{{ route('profile.index') }}" class="flex min-w-0 flex-1 items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-800 text-sm font-semibold text-white shadow-xs">
                                {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-bold text-slate-900 dark:text-white">
                                    {{ auth()->user()->username }}
                                </p>
                                <p class="truncate text-xs font-semibold text-emerald-800 dark:text-emerald-400 flex items-center gap-1">
                                    {{ auth()->user()->role === 'teacher' ? 'Guru' : 'Siswa' }} • Profil & Pengaturan <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </p>
                            </div>
                        </a>
                    </div>
                </div>

            @else

                <div class="mt-4 border-t border-slate-100 dark:border-slate-800 pt-4">
                    <a
                        href="{{ route('login') }}"
                        class="block w-full rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-4 py-3 text-center text-sm font-bold text-white shadow-md transition hover:from-emerald-700 hover:to-teal-700"
                    >
                        <i class="fa-solid fa-right-to-bracket mr-1"></i> Masuk Akun
                    </a>
                </div>

            @endauth

        </div>
    </div>
</nav>

<script>
    (function () {
        function setupMobileNav() {
            const btn = document.getElementById('mobile-nav-toggle-btn');
            const drawer = document.getElementById('mobile-nav-drawer');

            if (!btn || !drawer) return;

            btn.onclick = function (e) {
                e.stopPropagation();
                drawer.classList.toggle('hidden');
            };

            // Close mobile menu when clicking outside
            document.addEventListener('click', function (e) {
                const nav = document.getElementById('main-navbar');
                if (nav && !nav.contains(e.target) && !drawer.classList.contains('hidden')) {
                    drawer.classList.add('hidden');
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupMobileNav);
        } else {
            setupMobileNav();
        }
    })();
</script>