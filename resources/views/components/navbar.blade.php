@props([
    'transparent' => false,
])

<nav
    x-data="{ open: false }"
    class="sticky top-0 z-50 w-full border-b border-emerald-800/80 bg-white/95 backdrop-blur-xl"
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
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-800 text-sm font-bold text-white sm:h-10 sm:w-10">
                    E
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

        {{-- Desktop Navigation --}}
        <div class="hidden items-center gap-7 md:flex">

            <a
                href="{{ route('home') }}"
                class="text-md font-medium text-slate-500 transition hover:text-emerald-800
                {{ request()->routeIs('home') ? 'active text-emerald-800' : '' }}"
            >
                Beranda
            </a>

            <a
                href="{{ route('products.index') }}"
                class="text-md font-medium text-slate-500 transition hover:text-emerald-800
                {{ request()->routeIs('products.index') ? 'active text-emerald-800' : '' }}"
            >
                Produk
            </a>

            <a
                href="{{ route('guide') }}"
                class="text-md font-medium text-slate-500 transition hover:text-emerald-800
                {{ request()->routeIs('guide') ? 'active text-emerald-800' : '' }}"
            >
                Panduan
            </a>

            <a
                href="{{ route('about') }}"
                class="text-md font-medium text-slate-500 transition hover:text-emerald-800
                {{ request()->routeIs('about') ? 'active text-emerald-800' : '' }}"
            >
                Tentang
            </a>

            @auth
                <a
                    href="{{ route('buyer.dashboard') }}"
                    class="text-md font-medium text-slate-500 transition hover:text-emerald-800
                    {{ request()->routeIs('buyer.dashboard') ? 'active text-emerald-800' : '' }}"
                >
                    Dashboard
                </a>
            @endauth

        </div>

        {{-- Desktop Right --}}
        <div class="hidden items-center gap-2 md:flex">

            @auth

                {{-- Cart --}}
                <a
                    href="{{ route('buyer.cart.index') }}"
                    class="rounded-xl p-2.5 text-emerald-800 transition hover:bg-emerald-800 hover:text-emerald-800"
                    aria-label="Keranjang"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 4h13m-8-1a1 1 0 100 2 1 1 0 000-2zm7 0a1 1 0 100 2 1 1 0 000-2z"
                        />
                    </svg>
                </a>

                {{-- Profile --}}
                <a
                    href="{{ route('profile.index') }}"
                    class="ml-1 flex items-center gap-2 rounded-xl px-2 py-1.5 transition hover:bg-emerald-800"
                >
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-800 text-sm font-semibold text-white">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>

                    <div class="max-w-32">
                        <p class="truncate text-sm font-semibold text-emerald-800">
                            {{ auth()->user()->name }}
                        </p>

                        <p class="text-xs text-emerald-800">
                            @if(auth()->user()->seller?->status === 'approved')
                                Seller
                            @else
                                Buyer
                            @endif
                        </p>
                    </div>
                </a>

                <form
                    action="{{ route('logout') }}"
                    method="POST"
                >
                    @csrf

                    <button
                        type="submit"
                        class="rounded-xl px-3 py-2 text-sm font-medium text-emerald-800 transition hover:bg-emerald-800 hover:text-emerald-800"
                    >
                        Keluar
                    </button>
                </form>

            @else

                <a
                    href="{{ route('login') }}"
                    class="rounded-xl bg-emerald-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800"
                >
                    Masuk
                </a>

            @endauth

        </div>

        {{-- Mobile Menu Button --}}
        <button
            type="button"
            @click="open = !open"
            class="inline-flex rounded-xl p-2.5 text-emerald-800 transition hover:bg-emerald-800/10 md:hidden"
            aria-label="Buka menu"
        >
            <svg
                x-show="!open"
                class="h-5 w-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.8"
                    d="M4 6h16M4 12h16M4 18h16"
                />
            </svg>
        </button>

    </div>

    {{-- Mobile Menu --}}
    <div
        x-show="open"
        x-cloak
        x-transition
        class="border-t border-emerald-800 bg-white md:hidden"
    >
        <div class="space-y-1 px-4 py-4 sm:px-6">

            <a
                href="{{ route('home') }}"
                class="block rounded-xl px-4 py-3 text-sm font-medium text-slate-500 hover:text-emerald-800"
            >
                Beranda
            </a>

            <a
                href="{{ route('products.index') }}"
                class="block rounded-xl px-4 py-3 text-sm font-medium text-slate-500 hover:text-emerald-800"
            >
                Produk
            </a>

            <a
                href="{{ route('guide') }}"
                class="block rounded-xl px-4 py-3 text-sm font-medium text-slate-500 transition hover:text-emerald-800
                {{ request()->routeIs('guide') ? 'active text-emerald-800' : '' }}"
            >
                Panduan
            </a>

            <a
                href="{{ route('about') }}"
                class="block rounded-xl px-4 py-3 text-sm font-medium text-slate-500 transition hover:text-emerald-800
                {{ request()->routeIs('about') ? 'active text-emerald-800' : '' }}"
            >
                Tentang
            </a>

            @auth

                <a
                    href="{{ route('buyer.dashboard') }}"
                    class="block rounded-xl px-4 py-3 text-sm font-medium text-emerald-800 hover:bg-emerald-60"
                >
                    Dashboard
                </a>

                <a
                    href="{{ route('buyer.cart.index') }}"
                    class="block rounded-xl px-4 py-3 text-sm font-medium text-emerald-800 hover:bg-emerald-60"
                >
                    Keranjang
                </a>

                <a
                    href="{{ route('profile.index') }}"
                    class="block rounded-xl px-4 py-3 text-sm font-medium text-emerald-800 hover:bg-emerald-60"
                >
                    Profil
                </a>

                <form
                    action="{{ route('logout') }}"
                    method="POST"
                    class="pt-1"
                >
                    @csrf

                    <button
                        type="submit"
                        class="w-full rounded-xl px-4 py-3 text-left text-sm font-medium text-red-800 hover:bg-red-50"
                    >
                        Keluar
                    </button>
                </form>

            @else

                <a
                    href="{{ route('login') }}"
                    class="mt-2 block rounded-xl bg-emerald-800 px-4 py-3 text-center text-sm font-semibold text-white"
                >
                    Masuk
                </a>

            @endauth

        </div>
    </div>
</nav>