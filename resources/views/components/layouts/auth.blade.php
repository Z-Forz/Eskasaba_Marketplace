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

<body class="flex min-h-screen flex-col bg-slate-950 font-['Plus_Jakarta_Sans',sans-serif] text-slate-100 antialiased selection:bg-emerald-500 selection:text-white">

    <main class="relative flex min-h-screen w-full flex-col items-center justify-center overflow-hidden bg-slate-950 px-4 py-8 sm:px-6 sm:py-12">
        <!-- Hero Background Image Overlay (Matching Landing Page) -->
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            @if(!empty($settings?->hero_image))
                <img
                    src="{{ asset('storage/' . $settings->hero_image) }}"
                    alt="{{ $settings->hero_title ?? 'Eskasaba Market' }}"
                    class="h-full w-full object-cover blur-sm scale-102 opacity-75"
                >
                <div class="absolute inset-0 bg-gradient-to-r from-slate-950/85 via-slate-900/60 to-slate-950/50"></div>
            @endif
        </div>

        <div class="relative z-10 flex w-full flex-col items-center justify-center">
            {{ $slot }}
        </div>
    </main>

    @if(session('success') || session('error') || session('warning') || session('info'))
        <div class="fixed bottom-5 left-4 right-4 sm:left-auto sm:right-5 sm:w-full sm:max-w-md z-50 space-y-2 pointer-events-none">
            @if(session('success'))
                <x-alert type="success" :message="session('success')" class="pointer-events-auto shadow-lg" />
            @endif
            @if(session('error'))
                <x-alert type="error" :message="session('error')" class="pointer-events-auto shadow-lg" />
            @endif
            @if(session('warning'))
                <x-alert type="warning" :message="session('warning')" class="pointer-events-auto shadow-lg" />
            @endif
            @if(session('info'))
                <x-alert type="info" :message="session('info')" class="pointer-events-auto shadow-lg" />
            @endif
        </div>
    @endif

    <x-confirm-modal />

    @stack('scripts')

</body>
</html>