<x-layouts.app>
    <div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-lg text-center">

            <div class="mb-6 flex justify-center">
                <div class="flex h-24 w-24 items-center justify-center rounded-full bg-red-50 text-red-600">
                    <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M12 9v3.5m0 3h.01M10.29 3.86l-8.1 14a2 2 0 001.73 3h16.16a2 2 0 001.73-3l-8.1-14a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
            </div>

            <p class="text-sm font-semibold uppercase tracking-widest text-red-600">
                Error 403
            </p>

            <h1 class="mt-2 text-3xl font-bold text-gray-900 sm:text-4xl">
                Akses Ditolak
            </h1>

            <p class="mx-auto mt-4 max-w-md text-sm leading-6 text-gray-500 sm:text-base">
                Maaf, kamu tidak memiliki izin untuk mengakses halaman ini.
            </p>

            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ url()->previous() }}"
                   class="inline-flex items-center justify-center rounded-xl border border-gray-200
                          bg-white px-5 py-3 text-sm font-semibold text-gray-700
                          transition hover:bg-gray-50">
                    Kembali
                </a>

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
