<x-layouts.app>
    <div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-lg text-center">

            <div class="mb-6 flex justify-center">
                <div class="flex h-24 w-24 items-center justify-center rounded-full bg-amber-50 text-amber-600">
                    <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M12 9v3m0 4h.01M10.34 3.94l-8.1 14A2 2 0 003.97 21h16.06a2 2 0 001.73-3.06l-8.1-14a2 2 0 00-3.32 0z"/>
                    </svg>
                </div>
            </div>

            <p class="text-5xl font-black tracking-tight text-gray-900 sm:text-6xl">
                500
            </p>

            <h1 class="mt-3 text-2xl font-bold text-gray-900 sm:text-3xl">
                Terjadi Kesalahan
            </h1>

            <p class="mx-auto mt-4 max-w-md text-sm leading-6 text-gray-500 sm:text-base">
                Terjadi kesalahan pada server. Silakan coba lagi beberapa saat
                kemudian.
            </p>

            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                <button type="button"
                        onclick="window.location.reload()"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-200
                               bg-white px-5 py-3 text-sm font-semibold text-gray-700
                               transition hover:bg-gray-50">
                    Coba Lagi
                </button>

                <a href="{{ route('home') }}"
                   class="inline-flex items-center justify-center rounded-xl bg-gray-900
                          px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800">
                    Ke Beranda
                </a>
            </div>

        </div>
    </div>
</x-layouts.app>
```
