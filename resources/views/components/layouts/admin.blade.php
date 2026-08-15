<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        {{ $title ?? 'Admin - ' . ($settings->website_name ?? 'Eskasaba Market') }}
    </title>

    @if(isset($settings) && $settings->favicon)

        <link
            rel="icon"
            href="{{ asset('storage/' . $settings->favicon) }}"
        >

    @endif

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @stack('head')

</head>


<body class="min-h-screen bg-slate-50 text-slate-800">

    {{-- =====================================================
        ADMIN NAVBAR / SIDEBAR
    ====================================================== --}}

    <x-navbar-admin variant="admin" />


    {{-- =====================================================
        MAIN CONTENT
    ====================================================== --}}

    <main
        class="
            min-h-screen
            pt-20
            lg:ml-56
            lg:pt-20
        "
    >

        <div
            class="
                mx-auto
                w-full
                max-w-[1400px]
                px-4
                py-6
                sm:px-6
                sm:py-8
                lg:px-8
            "
        >

            {{ $slot }}

        </div>

    </main>


    {{-- =====================================================
        TOAST / ALERT
    ====================================================== --}}

    @if(session('success'))

        <x-alert
            type="success"
            :message="session('success')"
        />

    @endif


    @if(session('error'))

        <x-alert
            type="error"
            :message="session('error')"
        />

    @endif


    @if(session('warning'))

        <x-alert
            type="warning"
            :message="session('warning')"
        />

    @endif


    @if(session('info'))

        <x-alert
            type="info"
            :message="session('info')"
        />

    @endif


    @stack('scripts')

</body>

</html>