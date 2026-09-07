<x-layouts.app>
    <div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-lg text-center">

            <div class="mb-6 flex justify-center">
                <div class="flex h-24 w-24 items-center justify-center rounded-full bg-gray-100 text-gray-500">
                    <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M9 9h.01M15 9h.01M9.5 15a4.5 4.5 0 005 0M12 3a9 9 0 100 18 9 9 0 000-18z"/>
                    </svg>
                </div>
            </div>

            <p class="text-5xl font-black tracking-tight text-gray-900 sm:text-6xl">
                404
            </p>

            <h1 class="mt-3 text-2xl font-bold text-gray-900 sm:text-3xl">
                Halaman Tidak Ditemukan
            </h1>

            <p class="mx-auto mt-4 max-w-md text-sm leading-6 text-gray-500 sm:text-base">
                Halaman yang kamu cari mungkin sudah dipindahkan,
                dihapus, atau alamatnya tidak benar.
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
