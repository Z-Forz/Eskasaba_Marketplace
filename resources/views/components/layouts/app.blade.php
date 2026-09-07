<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Marketplace' }} - {{ $settings->website_name ?? config('app.name', 'Marketplace') }}</title>

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
</head>

<body class="flex min-h-full flex-col bg-slate-50 font-['Plus_Jakarta_Sans',sans-serif] text-slate-800 antialiased dark:bg-slate-950 dark:text-slate-200">

    <x-navbar />

    <main class="min-h-[calc(100vh-4rem)] flex-1">
        {{ $slot }}
    </main>

    <x-footer />

    {{-- Toast Flash Messages --}}
    @if(session('success') || session('error') || session('warning') || session('info'))
        <div class="fixed bottom-5 right-5 z-50 max-w-md w-full px-4 space-y-2 pointer-events-none">
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

    @stack('scripts')

</body>
</html>