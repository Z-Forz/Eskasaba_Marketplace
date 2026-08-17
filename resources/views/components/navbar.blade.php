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
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-800 text-sm font-bold text-white sm:h-10 sm:w-10 shadow-xs">
                    <i class="fa-solid fa-shop"></i>
                </div>
            @endif

            <span class="max-w-37.5 truncate sm:max-w-none flex gap-1">
                <h1 class="text-xl font-bold tracking-tight sm:text-base">
                    Eskasaba
                </h1>
                <h1 class="text-xl font-bold tracking-tight sm:text-base text-emerald-800">
                    Marketplace 
                </h1>
            </span>
        </a>

        {{-- Desktop Navigation Links --}}
        <div class="hidden items-center gap-2 md:flex">

            <a
                href="{{ route('home') }}"
                class="px-3.5 py-2 rounded-xl text-sm transition flex items-center gap-2 {{ request()->routeIs('home') ? 'bg-emerald-50 text-emerald-800 font-bold shadow-xs ring-1 ring-emerald-200/60' : 'text-slate-600 font-medium hover:bg-slate-100/80 hover:text-slate-900' }}"
            >
                <i class="fa-solid fa-house text-xs"></i> Beranda
            </a>

            <a
                href="{{ route('products.index') }}"
                class="px-3.5 py-2 rounded-xl text-sm transition flex items-center gap-2 {{ request()->routeIs('products.*') ? 'bg-emerald-50 text-emerald-800 font-bold shadow-xs ring-1 ring-emerald-200/60' : 'text-slate-600 font-medium hover:bg-slate-100/80 hover:text-slate-900' }}"
            >
                <i class="fa-solid fa-bag-shopping text-xs"></i> Produk
            </a>

            <a
                href="{{ route('guide') }}"
                class="px-3.5 py-2 rounded-xl text-sm transition flex items-center gap-2 {{ request()->routeIs('guide') ? 'bg-emerald-50 text-emerald-800 font-bold shadow-xs ring-1 ring-emerald-200/60' : 'text-slate-600 font-medium hover:bg-slate-100/80 hover:text-slate-900' }}"
            >
                <i class="fa-solid fa-book-open text-xs"></i> Panduan
            </a>

            <a
                href="{{ route('about') }}"
                class="px-3.5 py-2 rounded-xl text-sm transition flex items-center gap-2 {{ request()->routeIs('about') ? 'bg-emerald-50 text-emerald-800 font-bold shadow-xs ring-1 ring-emerald-200/60' : 'text-slate-600 font-medium hover:bg-slate-100/80 hover:text-slate-900' }}"
            >
                <i class="fa-solid fa-circle-info text-xs"></i> Tentang
            </a>

        </div>

        {{-- Desktop Right Actions --}}
        <div class="hidden items-center gap-2 md:flex">

            @auth

                {{-- Cart Button --}}
                <a
                    href="{{ route('buyer.cart.index') }}"
                    class="rounded-xl p-2.5 transition flex items-center justify-center {{ request()->routeIs('buyer.cart.*') ? 'bg-emerald-100 text-emerald-800 shadow-xs ring-1 ring-emerald-300' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-800' }}"
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
                    class="relative rounded-xl p-2.5 transition flex items-center justify-center {{ request()->routeIs('buyer.notifications.*') ? 'bg-emerald-100 text-emerald-800 shadow-xs ring-1 ring-emerald-300' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-800' }}"
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
                    class="ml-1 flex items-center gap-2.5 rounded-xl p-1.5 {{ request()->routeIs('profile.*') ? 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-300 shadow-xs' : '' }}"
                >
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-800 text-sm font-semibold text-white shadow-xs">
                        {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                    </div>

                    <div class="max-w-32">
                        <p class="truncate text-sm font-bold text-slate-800">
                            {{ auth()->user()->username }}
                        </p>

                        <p class="text-xs text-slate-500 font-medium">
                            @if(auth()->user()->seller?->status === 'approved')
                                Seller
                            @else
                                {{ auth()->user()->role === 'teacher' ? 'Guru' : 'Siswa' }}
                            @endif
                        </p>
                    </div>
                </a>

            @else

                <a
                    href="{{ route('login') }}"
                    class="rounded-xl bg-emerald-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-900 shadow-xs flex items-center gap-2"
                >
                    <i class="fa-solid fa-right-to-bracket"></i> Masuk
                </a>

            @endauth

        </div>

        {{-- Mobile Menu Button --}}
        <button
            type="button"
            id="mobile-nav-toggle-btn"
            class="inline-flex rounded-xl p-2.5 text-slate-700 transition hover:bg-slate-100 md:hidden"
            aria-label="Buka menu"
        >
            <i id="icon-hamburger" class="fa-solid fa-bars text-xl"></i>
            <i id="icon-close" class="fa-solid fa-xmark text-xl hidden"></i>
        </button>

    </div>

    {{-- Mobile Menu Drawer --}}
    <div
        id="mobile-nav-drawer"
        class="hidden border-t border-slate-200/80 bg-white/98 backdrop-blur-xl md:hidden"
    >
        <div class="space-y-1.5 px-4 py-4 sm:px-6">

            <a
                href="{{ route('home') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition {{ request()->routeIs('home') ? 'bg-emerald-100/80 text-emerald-900 font-bold border-l-4 border-emerald-800 shadow-xs' : 'text-slate-600 font-medium hover:bg-slate-50 hover:text-slate-900' }}"
            >
                <i class="fa-solid fa-house w-5 text-center"></i> Beranda
            </a>

            <a
                href="{{ route('products.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition {{ request()->routeIs('products.*') ? 'bg-emerald-100/80 text-emerald-900 font-bold border-l-4 border-emerald-800 shadow-xs' : 'text-slate-600 font-medium hover:bg-slate-50 hover:text-slate-900' }}"
            >
                <i class="fa-solid fa-bag-shopping w-5 text-center"></i> Produk
            </a>

            <a
                href="{{ route('guide') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition {{ request()->routeIs('guide') ? 'bg-emerald-100/80 text-emerald-900 font-bold border-l-4 border-emerald-800 shadow-xs' : 'text-slate-600 font-medium hover:bg-slate-50 hover:text-slate-900' }}"
            >
                <i class="fa-solid fa-book-open w-5 text-center"></i> Panduan
            </a>

            <a
                href="{{ route('about') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition {{ request()->routeIs('about') ? 'bg-emerald-100/80 text-emerald-900 font-bold border-l-4 border-emerald-800 shadow-xs' : 'text-slate-600 font-medium hover:bg-slate-50 hover:text-slate-900' }}"
            >
                <i class="fa-solid fa-circle-info w-5 text-center"></i> Tentang
            </a>

            @auth
                <a
                    href="{{ route('buyer.cart.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition {{ request()->routeIs('buyer.cart.*') ? 'bg-emerald-100/80 text-emerald-900 font-bold border-l-4 border-emerald-800 shadow-xs' : 'text-slate-600 font-medium hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    <i class="fa-solid fa-cart-shopping w-5 text-center"></i> Keranjang Belanja
                </a>

                {{-- User Profile Nav Section --}}
                <div class="mt-4 border-t border-slate-100 pt-4">
                    <div class="flex items-center justify-between gap-3 rounded-2xl border p-3.5 {{ request()->routeIs('profile.*') ? 'border-emerald-300 bg-emerald-100/70 ring-2 ring-emerald-600/20 shadow-sm' : 'border-slate-200/80 bg-slate-50' }}">
                        <a href="{{ route('profile.index') }}" class="flex min-w-0 flex-1 items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-800 text-sm font-semibold text-white shadow-xs">
                                {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-bold text-slate-900">
                                    {{ auth()->user()->username }}
                                </p>
                                <p class="truncate text-xs font-semibold text-emerald-800">
                                    {{ auth()->user()->role === 'teacher' ? 'Guru' : 'Siswa' }} • Profil & Dashboard →
                                </p>
                            </div>
                        </a>
                    </div>
                </div>

            @else

                <div class="mt-4 border-t border-slate-100 pt-4">
                    <a
                        href="{{ route('login') }}"
                        class="block w-full rounded-xl bg-emerald-800 px-4 py-3 text-center text-sm font-semibold text-white shadow-xs transition hover:bg-emerald-900"
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
            const hamburger = document.getElementById('icon-hamburger');
            const closeIcon = document.getElementById('icon-close');

            if (!btn || !drawer) return;

            btn.onclick = function (e) {
                e.stopPropagation();
                const isHidden = drawer.classList.contains('hidden');

                if (isHidden) {
                    drawer.classList.remove('hidden');
                    if (hamburger) hamburger.classList.add('hidden');
                    if (closeIcon) closeIcon.classList.remove('hidden');
                } else {
                    drawer.classList.add('hidden');
                    if (hamburger) hamburger.classList.remove('hidden');
                    if (closeIcon) closeIcon.classList.add('hidden');
                }
            };
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupMobileNav);
        } else {
            setupMobileNav();
        }
    })();
</script>