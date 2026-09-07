<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Login' }} - {{ $settings->website_name ?? config('app.name', 'Eskasaba Market') }}</title>

    @if(!empty($settings->logo))
        <link rel="icon" href="{{ asset('storage/' . $settings->logo) }}">
    @elseif(!empty($settings->favicon))
        <link rel="icon" href="{{ asset('storage/' . $settings->favicon) }}">
    @endif

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>

<body class="flex min-h-screen flex-col bg-emerald-800 font-['Plus_Jakarta_Sans',sans-serif] text-slate-800 antialiased dark:bg-slate-950 dark:text-slate-200 selection:bg-emerald-500 selection:text-white">

    <main class="relative flex min-h-screen w-full flex-col items-center justify-center overflow-hidden bg-gradient-to-br from-emerald-600 via-teal-700 to-emerald-900 px-4 py-8 sm:px-6 sm:py-12 dark:from-slate-950 dark:via-emerald-950/80 dark:to-slate-950">
        <!-- Rich background glowing ambient emerald orbs -->
        <div class="pointer-events-none absolute -top-24 -left-24 h-[28rem] w-[28rem] rounded-full bg-emerald-400/30 blur-3xl dark:bg-emerald-500/15"></div>
        <div class="pointer-events-none absolute -bottom-24 -right-24 h-[28rem] w-[28rem] rounded-full bg-teal-300/25 blur-3xl dark:bg-teal-500/15"></div>
        <div class="pointer-events-none absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 h-[22rem] w-[22rem] rounded-full bg-emerald-300/20 blur-2xl dark:bg-emerald-600/10"></div>

        <div class="relative z-10 flex w-full flex-col items-center justify-center">
            {{ $slot }}
        </div>
    </main>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    @if(session('warning'))
        <x-alert type="warning" :message="session('warning')" />
    @endif

    @if(session('info'))
        <x-alert type="info" :message="session('info')" />
    @endif

    @stack('scripts')

</body>
</html>