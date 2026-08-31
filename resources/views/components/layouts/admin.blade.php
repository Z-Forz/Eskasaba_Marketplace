<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Panel Admin' }} - {{ $settings->website_name ?? config('app.name', 'Marketplace') }}</title>

    @if(!empty($settings->logo))
        <link rel="icon" href="{{ asset('storage/' . $settings->logo) }}">
    @elseif(!empty($settings->favicon))
        <link rel="icon" href="{{ asset('storage/' . $settings->favicon) }}">
    @endif

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

    <x-sidebar-admin />

    <div class="flex flex-1 flex-col lg:pl-56">

        <header class="sticky top-0 z-40 flex h-16 items-center justify-between border-b border-slate-200/80 bg-white/80 px-4 backdrop-blur-md dark:border-slate-800 dark:bg-slate-900/80 sm:px-6">
            <div class="flex items-center gap-3">
                {{-- Mobile Hamburger Toggle Button Admin --}}
                <button
                    type="button"
                    class="admin-mobile-sidebar-toggle-btn inline-flex items-center justify-center rounded-xl border border-slate-200 bg-slate-100 p-2 text-slate-700 hover:bg-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 lg:hidden cursor-pointer"
                    aria-label="Buka Menu Admin"
                >
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>

                <h1 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-emerald-600"></i> {{ $title ?? 'Panel Admin' }}
                </h1>
            </div>

            <div class="flex items-center gap-3">
                <span class="rounded-full bg-emerald-100 px-3.5 py-1 text-xs font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                    <i class="fa-solid fa-user-shield mr-1"></i> Administrator
                </span>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            {{ $slot }}
        </main>

    </div>

    {{-- Flash Toast Messages --}}
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