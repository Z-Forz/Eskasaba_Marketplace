{{-- resources/views/components/navbar-admin.blade.php --}}

{{-- =========================================================
    DESKTOP SIDEBAR
========================================================= --}}

<aside
    class="
        fixed inset-y-0 left-0 z-50 hidden
        w-56
        border-r border-slate-200
        bg-white
        lg:flex lg:flex-col
    "
>

    {{-- =====================================================
        LOGO
    ====================================================== --}}

    <div class="flex h-20 items-center border-b border-slate-100 px-5">

        <a
            href="{{ route('admin.dashboard') }}"
            class="flex items-center gap-3"
        >

            {{-- Logo Icon --}}
            <div
                class="
                    flex h-10 w-10 shrink-0
                    items-center justify-center
                    rounded-xl
                    bg-blue-600
                    text-white
                "
            >

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7l8-4z"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9 12l2 2 4-4"
                    />

                </svg>

            </div>


            {{-- Logo Text --}}
            <div>

                <p class="text-sm font-bold text-slate-900">
                    ESKASABA
                </p>

                <p class="text-[10px] text-slate-400">
                    Marketplace Admin
                </p>

            </div>

        </a>

    </div>


    {{-- =====================================================
        SIDEBAR MENU
    ====================================================== --}}

    <nav class="flex-1 overflow-y-auto px-3 py-5">

        {{-- =================================================
            DASHBOARD
        ================================================== --}}

        <a
            href="{{ route('admin.dashboard') }}"
            class="
                mb-2 flex items-center gap-3
                rounded-lg px-4 py-3
                text-sm font-semibold
                transition

                {{ request()->routeIs('admin.dashboard')
                    ? 'bg-blue-600 text-white shadow-sm'
                    : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600'
                }}
            "
        >

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 shrink-0"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="1.8"
            >

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M3 10.5L12 3l9 7.5V21a1 1 0 01-1 1H4a1 1 0 01-1-1V10.5z"
                />

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 22v-7h6v7"
                />

            </svg>

            <span>
                Dashboard
            </span>

        </a>


        {{-- =================================================
            DATA MASTER
        ================================================== --}}

        <p
            class="
                mb-2 mt-7 px-4
                text-[10px]
                font-bold
                uppercase
                tracking-wider
                text-slate-400
            "
        >
            Data Master
        </p>


        {{-- Users --}}

        <a
            href="{{ route('admin.users.index') }}"
            class="
                mb-1 flex items-center justify-between
                rounded-lg px-4 py-3
                text-sm
                transition

                {{ request()->routeIs('admin.users.*')
                    ? 'bg-blue-50 font-semibold text-blue-600'
                    : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600'
                }}
            "
        >

            <span class="flex items-center gap-3">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 shrink-0"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"
                    />

                    <circle
                        cx="9"
                        cy="7"
                        r="4"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"
                    />

                </svg>

                <span>
                    Kelola User
                </span>

            </span>

            <span>
                ›
            </span>

        </a>


        {{-- Sellers --}}

        <a
            href="{{ route('admin.sellers.index') }}"
            class="
                mb-1 flex items-center justify-between
                rounded-lg px-4 py-3
                text-sm
                transition

                {{ request()->routeIs('admin.sellers.*')
                    ? 'bg-blue-50 font-semibold text-blue-600'
                    : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600'
                }}
            "
        >

            <span class="flex items-center gap-3">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 shrink-0"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >

                    <circle
                        cx="12"
                        cy="12"
                        r="9"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M8 12h8M12 8v8"
                    />

                </svg>

                <span>
                    Verifikasi Seller
                </span>

            </span>

            <span>
                ›
            </span>

        </a>


        {{-- Categories --}}

        <a
            href="{{ route('admin.categories.index') }}"
            class="
                mb-1 flex items-center justify-between
                rounded-lg px-4 py-3
                text-sm
                transition

                {{ request()->routeIs('admin.categories.*')
                    ? 'bg-blue-50 font-semibold text-blue-600'
                    : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600'
                }}
            "
        >

            <span class="flex items-center gap-3">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 shrink-0"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M4 6h16M4 12h16M4 18h16"
                    />

                </svg>

                <span>
                    Kelola Kategori
                </span>

            </span>

            <span>
                ›
            </span>

        </a>


        {{-- Orders --}}

        <a
            href="{{ route('admin.orders.index') }}"
            class="
                mb-1 flex items-center justify-between
                rounded-lg px-4 py-3
                text-sm
                transition

                {{ request()->routeIs('admin.orders.*')
                    ? 'bg-blue-50 font-semibold text-blue-600'
                    : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600'
                }}
            "
        >

            <span class="flex items-center gap-3">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 shrink-0"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M3 3h2l2 13h10l3-9H6"
                    />

                    <circle
                        cx="10"
                        cy="20"
                        r="1"
                    />

                    <circle
                        cx="18"
                        cy="20"
                        r="1"
                    />

                </svg>

                <span>
                    Kelola Pesanan
                </span>

            </span>

            <span>
                ›
            </span>

        </a>


        {{-- =================================================
            LAPORAN
        ================================================== --}}

        <p
            class="
                mb-2 mt-7 px-4
                text-[10px]
                font-bold
                uppercase
                tracking-wider
                text-slate-400
            "
        >
            Laporan
        </p>


        {{-- Laporan Penjualan --}}

        <a
            href="{{ route('admin.orders.index') }}"
            class="
                mb-1 flex items-center gap-3
                rounded-lg px-4 py-3
                text-sm text-slate-600
                transition
                hover:bg-slate-50
                hover:text-blue-600
            "
        >

            <span class="text-lg">
                ▣
            </span>

            <span>
                Laporan Penjualan
            </span>

        </a>


        {{-- Laporan Produk --}}

        <a
            href="{{ route('admin.categories.index') }}"
            class="
                mb-1 flex items-center gap-3
                rounded-lg px-4 py-3
                text-sm text-slate-600
                transition
                hover:bg-slate-50
                hover:text-blue-600
            "
        >

            <span class="text-lg">
                ◷
            </span>

            <span>
                Laporan Produk
            </span>

        </a>


        {{-- Seller Aktif --}}

        <a
            href="{{ route('admin.sellers.index') }}"
            class="
                mb-1 flex items-center gap-3
                rounded-lg px-4 py-3
                text-sm text-slate-600
                transition
                hover:bg-slate-50
                hover:text-blue-600
            "
        >

            <span class="text-lg">
                ♙
            </span>

            <span>
                Seller Aktif
            </span>

        </a>


        {{-- =================================================
            PENGATURAN
        ================================================== --}}

        <p
            class="
                mb-2 mt-7 px-4
                text-[10px]
                font-bold
                uppercase
                tracking-wider
                text-slate-400
            "
        >
            Pengaturan
        </p>


        {{-- Website Settings --}}

        <a
            href="{{ route('admin.website-settings.index') }}"
            class="
                mb-1 flex items-center gap-3
                rounded-lg px-4 py-3
                text-sm
                transition

                {{ request()->routeIs('admin.website-settings.*')
                    ? 'bg-blue-50 font-semibold text-blue-600'
                    : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600'
                }}
            "
        >

            <span class="text-lg">
                ⚙
            </span>

            <span>
                Pengaturan Website
            </span>

        </a>


        {{-- Account Settings --}}

        <a
            href="{{ route('profile.index') }}"
            class="
                mb-1 flex items-center gap-3
                rounded-lg px-4 py-3
                text-sm text-slate-600
                transition
                hover:bg-slate-50
                hover:text-blue-600
            "
        >

            <span class="text-lg">
                ♙
            </span>

            <span>
                Pengaturan Akun
            </span>

        </a>

    </nav>


    {{-- =====================================================
        BOTTOM PANEL
    ====================================================== --}}

    <div class="p-3">

        <div
            class="
                rounded-xl
                bg-blue-600
                p-4
                text-white
            "
        >

            <p class="text-sm font-bold">
                Panel Admin
            </p>

            <p
                class="
                    mt-1
                    text-xs
                    leading-relaxed
                    text-blue-100
                "
            >
                Kelola marketplace dengan mudah dan aman.
            </p>

        </div>

    </div>


    {{-- =====================================================
        LOGOUT DESKTOP
    ====================================================== --}}

    <div class="border-t border-slate-100 p-3">

        <form
            method="POST"
            action="{{ route('admin.logout') }}"
        >

            @csrf

            <button
                type="submit"
                class="
                    flex w-full
                    items-center gap-3
                    rounded-lg
                    px-4 py-3
                    text-sm
                    font-medium
                    text-red-600
                    transition
                    hover:bg-red-50
                "
            >

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 shrink-0"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M10 17l5-5-5-5"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15 12H3"
                    />

                </svg>

                <span>
                    Logout
                </span>

            </button>

        </form>

    </div>

</aside>



{{-- =========================================================
    TOPBAR
========================================================= --}}

<header
    class="
        fixed right-0 top-0 z-40
        h-20
        border-b border-slate-200
        bg-white
        lg:left-56
    "
>

    <div
        class="
            flex h-full
            items-center justify-between
            px-4
            sm:px-6
            lg:px-8
        "
    >

        {{-- =================================================
            MOBILE TITLE
        ================================================== --}}

        <div
            class="
                flex items-center gap-3
                lg:hidden
            "
        >

            <button
                type="button"
                id="admin-mobile-menu-button"
                class="
                    flex h-10 w-10
                    items-center justify-center
                    rounded-lg
                    text-slate-600
                    hover:bg-slate-100
                "
            >

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M4 6h16M4 12h16M4 18h16"
                    />

                </svg>

            </button>


            <div>

                <p class="text-sm font-bold text-slate-900">
                    ESKASABA
                </p>

                <p class="text-[10px] text-slate-400">
                    Admin Panel
                </p>

            </div>

        </div>


        {{-- =================================================
            DESKTOP TITLE
        ================================================== --}}

        <div class="hidden lg:block">

            @php
                $pageTitle = match (true) {
                    request()->routeIs('admin.dashboard')
                        => 'Dashboard',

                    request()->routeIs('admin.users.*')
                        => 'Kelola User',

                    request()->routeIs('admin.sellers.*')
                        => 'Verifikasi Seller',

                    request()->routeIs('admin.categories.*')
                        => 'Kelola Kategori',

                    request()->routeIs('admin.orders.*')
                        => 'Kelola Pesanan',

                    request()->routeIs('admin.payments.*')
                        => 'Pembayaran',

                    request()->routeIs('admin.website-settings.*')
                        => 'Pengaturan Website',

                    request()->routeIs('profile.*')
                        => 'Pengaturan Akun',

                    default
                        => 'Admin Panel',
                };
            @endphp

            <p class="text-sm font-semibold text-slate-800">
                {{ $pageTitle }}
            </p>

            <p class="text-xs text-slate-400">
                Eskasaba Market
            </p>

        </div>

        {{-- =================================================
            RIGHT SIDE
        ================================================== --}}

        <div
            class="
                ml-auto
                flex
                items-center
                gap-2
                sm:gap-3
            "
        >

            {{-- Notification --}}

            <button
                type="button"
                class="
                    relative
                    flex h-10 w-10
                    items-center justify-center
                    rounded-lg
                    text-slate-500
                    transition
                    hover:bg-slate-100
                "
            >

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 00-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M10 21h4"
                    />

                </svg>


                {{-- Notification Badge --}}

                <span
                    class="
                        absolute
                        right-1
                        top-1
                        h-2
                        w-2
                        rounded-full
                        bg-red-500
                    "
                ></span>

            </button>


            {{-- Website Settings --}}

            <a
                href="{{ route('admin.website-settings.index') }}"
                class="
                    hidden
                    h-10 w-10
                    items-center
                    justify-center
                    rounded-lg
                    text-slate-500
                    transition
                    hover:bg-slate-100
                    sm:flex
                "
            >

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 15.5a3.5 3.5 0 100-7 3.5 3.5 0 000 7z"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M19.4 15a1.7 1.7 0 00.3 1.9l.1.1-1.8 1.8-.1-.1a1.7 1.7 0 00-1.9-.3 1.7 1.7 0 00-1 1.5v.1h-2.5v-.1a1.7 1.7 0 00-1-1.5 1.7 1.7 0 00-1.9.3l-.1.1-1.8-1.8.1-.1A1.7 1.7 0 008 15a1.7 1.7 0 00-1.5-1H6.4v-2.5h.1A1.7 1.7 0 008 10a1.7 1.7 0 00-.3-1.9l-.1-.1 1.8-1.8.1.1a1.7 1.7 0 001.9.3 1.7 1.7 0 001-1.5V5h2.5v.1a1.7 1.7 0 001 1.5 1.7 1.7 0 001.9-.3l.1-.1 1.8 1.8-.1.1a1.7 1.7 0 00-.3 1.9 1.7 1.7 0 001.5 1h.1v2.5h-.1a1.7 1.7 0 00-1.4 1.5z"
                    />

                </svg>

            </a>


            {{-- =================================================
                ADMIN PROFILE
            ================================================== --}}

            @auth('admin')

                <a
                    href="{{ route('profile.index') }}"
                    class="
                        flex
                        items-center
                        gap-2
                        border-l
                        border-slate-200
                        pl-2
                        sm:gap-3
                        sm:pl-3
                    "
                >

                    {{-- Avatar --}}

                    @if(auth('admin')->user()->avatar ?? false)

                        <img
                            src="{{ asset('storage/' . auth('admin')->user()->avatar) }}"
                            alt="{{ auth('admin')->user()->name ?? auth('admin')->user()->username }}"
                            class="
                                h-9 w-9
                                rounded-full
                                object-cover
                                sm:h-10 sm:w-10
                            "
                        >

                    @else

                        <div
                            class="
                                flex
                                h-9 w-9
                                items-center
                                justify-center
                                rounded-full
                                bg-blue-100
                                text-sm
                                font-bold
                                text-blue-700
                                sm:h-10 sm:w-10
                            "
                        >

                            {{ strtoupper(substr(auth('admin')->user()->username, 0, 1)) }}

                        </div>

                    @endif


                    {{-- User Information --}}

                    <div class="hidden sm:block">

                        <p
                            class="
                                max-w-32
                                truncate
                                text-sm
                                font-semibold
                                text-slate-800
                            "
                        >
                            {{ auth('admin')->user()->username }}
                        </p>

                        <p class="text-[11px] text-slate-400">
                            Administrator
                        </p>

                    </div>

                </a>

            @endauth

        </div>

    </div>

</header>



{{-- =========================================================
    MOBILE SIDEBAR
========================================================= --}}

<div
    id="admin-mobile-menu"
    class="
        fixed inset-0 z-[60]
        hidden
        lg:hidden
    "
>

    {{-- Overlay --}}

    <div
        id="admin-mobile-overlay"
        class="
            absolute inset-0
            bg-slate-900/40
            backdrop-blur-[1px]
        "
    ></div>


    {{-- Drawer --}}

    <aside
        class="
            relative
            flex h-full
            w-[min(18rem,85vw)]
            flex-col
            overflow-y-auto
            bg-white
            shadow-2xl
        "
    >

        {{-- Mobile Logo --}}

        <div
            class="
                flex h-20
                shrink-0
                items-center
                justify-between
                border-b
                border-slate-100
                px-5
            "
        >

            <a
                href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-3"
            >

                <div
                    class="
                        flex h-10 w-10
                        items-center justify-center
                        rounded-xl
                        bg-blue-600
                        text-white
                    "
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7l8-4z"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 12l2 2 4-4"
                        />

                    </svg>

                </div>


                <div>

                    <p class="text-sm font-bold text-slate-900">
                        ESKASABA
                    </p>

                    <p class="text-[10px] text-slate-400">
                        Marketplace Admin
                    </p>

                </div>

            </a>


            {{-- Close Button --}}

            <button
                type="button"
                id="admin-mobile-close"
                class="
                    flex h-9 w-9
                    items-center justify-center
                    rounded-lg
                    text-slate-500
                    transition
                    hover:bg-slate-100
                "
            >

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6 6l12 12M18 6L6 18"
                    />

                </svg>

            </button>

        </div>


        {{-- Mobile Menu --}}

        <nav class="flex-1 space-y-1 overflow-y-auto p-4">

            {{-- Dashboard --}}

            <a
                href="{{ route('admin.dashboard') }}"
                class="
                    flex items-center gap-3
                    rounded-lg
                    px-4 py-3
                    text-sm font-semibold
                    {{ request()->routeIs('admin.dashboard')
                        ? 'bg-blue-600 text-white'
                        : 'text-slate-600 hover:bg-slate-50'
                    }}
                "
            >

                <span>
                    Dashboard
                </span>

            </a>


            {{-- Data Master --}}

            <p
                class="
                    px-4 pb-1 pt-6
                    text-[10px]
                    font-bold
                    uppercase
                    tracking-wider
                    text-slate-400
                "
            >
                Data Master
            </p>


            <a
                href="{{ route('admin.users.index') }}"
                class="
                    block rounded-lg
                    px-4 py-3
                    text-sm
                    {{ request()->routeIs('admin.users.*')
                        ? 'bg-blue-50 font-semibold text-blue-600'
                        : 'text-slate-600 hover:bg-slate-50'
                    }}
                "
            >
                Kelola User
            </a>


            <a
                href="{{ route('admin.sellers.index') }}"
                class="
                    block rounded-lg
                    px-4 py-3
                    text-sm
                    {{ request()->routeIs('admin.sellers.*')
                        ? 'bg-blue-50 font-semibold text-blue-600'
                        : 'text-slate-600 hover:bg-slate-50'
                    }}
                "
            >
                Verifikasi Seller
            </a>


            <a
                href="{{ route('admin.categories.index') }}"
                class="
                    block rounded-lg
                    px-4 py-3
                    text-sm
                    {{ request()->routeIs('admin.categories.*')
                        ? 'bg-blue-50 font-semibold text-blue-600'
                        : 'text-slate-600 hover:bg-slate-50'
                    }}
                "
            >
                Kelola Kategori
            </a>


            <a
                href="{{ route('admin.orders.index') }}"
                class="
                    block rounded-lg
                    px-4 py-3
                    text-sm
                    {{ request()->routeIs('admin.orders.*')
                        ? 'bg-blue-50 font-semibold text-blue-600'
                        : 'text-slate-600 hover:bg-slate-50'
                    }}
                "
            >
                Kelola Pesanan
            </a>

            <a
                href="{{ route('admin.payments.index') }}"
                class="
                    block rounded-lg
                    px-4 py-3
                    text-sm
                    {{ request()->routeIs('admin.payments.*')
                        ? 'bg-blue-50 font-semibold text-blue-600'
                        : 'text-slate-600 hover:bg-slate-50'
                    }}
                "
            >
                Kelola Pembayaran
            </a>


            {{-- Pengaturan --}}

            <p
                class="
                    px-4 pb-1 pt-6
                    text-[10px]
                    font-bold
                    uppercase
                    tracking-wider
                    text-slate-400
                "
            >
                Pengaturan
            </p>


            <a
                href="{{ route('admin.website-settings.index') }}"
                class="
                    block rounded-lg
                    px-4 py-3
                    text-sm
                    {{ request()->routeIs('admin.website-settings.*')
                        ? 'bg-blue-50 font-semibold text-blue-600'
                        : 'text-slate-600 hover:bg-slate-50'
                    }}
                "
            >
                Pengaturan Website
            </a>


            <a
                href="{{ route('profile.index') }}"
                class="
                    block rounded-lg
                    px-4 py-3
                    text-sm
                    text-slate-600
                    hover:bg-slate-50
                "
            >
                Pengaturan Akun
            </a>

        </nav>


        {{-- Mobile Logout --}}

        <div class="shrink-0 border-t border-slate-100 p-4">

            <form
                method="POST"
                action="{{ route('admin.logout') }}"
            >

                @csrf

                <button
                    type="submit"
                    class="
                        flex w-full
                        items-center gap-3
                        rounded-lg
                        px-4 py-3
                        text-sm
                        font-medium
                        text-red-600
                        transition
                        hover:bg-red-50
                    "
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M10 17l5-5-5-5"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15 12H3"
                        />

                    </svg>

                    <span>
                        Logout
                    </span>

                </button>

            </form>

        </div>

    </aside>

</div>



{{-- =========================================================
    MOBILE MENU SCRIPT
========================================================= --}}

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const button = document.getElementById(
        'admin-mobile-menu-button'
    );

    const menu = document.getElementById(
        'admin-mobile-menu'
    );

    const close = document.getElementById(
        'admin-mobile-close'
    );

    const overlay = document.getElementById(
        'admin-mobile-overlay'
    );


    function openMenu() {

        if (!menu) {
            return;
        }

        menu.classList.remove('hidden');

        document.body.classList.add('overflow-hidden');

    }


    function closeMenu() {

        if (!menu) {
            return;
        }

        menu.classList.add('hidden');

        document.body.classList.remove('overflow-hidden');

    }


    button?.addEventListener(
        'click',
        openMenu
    );


    close?.addEventListener(
        'click',
        closeMenu
    );


    overlay?.addEventListener(
        'click',
        closeMenu
    );


    /*
     * Tutup drawer setelah user
     * memilih salah satu menu.
     */
    menu?.querySelectorAll('a').forEach(function (link) {

        link.addEventListener(
            'click',
            closeMenu
        );

    });


    /*
     * Jika layar kembali ke desktop,
     * pastikan drawer mobile ditutup.
     */
    window.addEventListener(
        'resize',
        function () {

            if (
                window.innerWidth >= 1024
                && menu
            ) {

                menu.classList.add('hidden');

                document.body.classList.remove(
                    'overflow-hidden'
                );

            }

        }
    );

});

</script>

@endpush