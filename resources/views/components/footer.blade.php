<footer class="border-t border-slate-800 bg-slate-950 text-slate-300">

    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">

        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">

            {{-- Brand Column --}}
            <div class="sm:col-span-2 lg:col-span-1">

                <div class="flex items-center gap-3">

                    @if(isset($settings) && $settings->logo)
                        <img
                            src="{{ asset('storage/' . $settings->logo) }}"
                            alt="{{ $settings->website_name ?? 'Eskasaba Market' }}"
                            class="h-10 w-10 rounded-2xl object-cover ring-2 ring-emerald-500 bg-white"
                        >
                    @else
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-700 text-base font-black text-white shadow-lg shadow-emerald-900/40">
                            <i class="fa-solid fa-shop"></i>
                        </div>
                    @endif

                    <div>
                        <span class="text-lg font-black tracking-tight text-white block">
                            Eskasaba <span class="text-emerald-400">Market</span>
                        </span>
                        <span class="text-[10px] font-semibold text-emerald-400/80 uppercase tracking-widest block">
                            SMKN 1 Bangsri
                        </span>
                    </div>

                </div>

                <p class="mt-4 text-xs leading-relaxed text-slate-400">
                    {{ $settings->about ?? 'Marketplace digital internal SMKN 1 Bantul untuk memfasilitasi jual beli karya siswa & guru secara aman, nyaman, dan terpercaya.' }}
                </p>

                {{-- Social Icons Bar --}}
                <div class="mt-5 flex items-center gap-2">
                    @if($settings?->instagram)
                        <a
                            href="{{ $settings->instagram }}"
                            target="_blank"
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-800 text-slate-300 transition hover:bg-pink-600 hover:text-white"
                            title="Instagram"
                        >
                            <i class="fa-brands fa-instagram text-sm"></i>
                        </a>
                    @endif

                    @if($settings?->facebook)
                        <a
                            href="{{ $settings->facebook }}"
                            target="_blank"
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-800 text-slate-300 transition hover:bg-blue-600 hover:text-white"
                            title="Facebook"
                        >
                            <i class="fa-brands fa-facebook-f text-sm"></i>
                        </a>
                    @endif

                    @if($settings?->tiktok)
                        <a
                            href="{{ $settings->tiktok }}"
                            target="_blank"
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-800 text-slate-300 transition hover:bg-slate-700 hover:text-white"
                            title="TikTok"
                        >
                            <i class="fa-brands fa-tiktok text-sm"></i>
                        </a>
                    @endif
                </div>

            </div>

            {{-- Quick Links --}}
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-400">
                    Navigasi Cepat
                </h3>

                <ul class="mt-4 space-y-2.5 text-xs font-semibold">
                    <li>
                        <a href="{{ route('home') }}" class="text-slate-400 transition hover:text-white flex items-center gap-2">
                            <i class="fa-solid fa-chevron-right text-[9px] text-emerald-500"></i> Beranda
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('products.index') }}" class="text-slate-400 transition hover:text-white flex items-center gap-2">
                            <i class="fa-solid fa-chevron-right text-[9px] text-emerald-500"></i> Katalog Produk
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('guide') }}" class="text-slate-400 transition hover:text-white flex items-center gap-2">
                            <i class="fa-solid fa-chevron-right text-[9px] text-emerald-500"></i> Panduan COD Sekolah
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('about') }}" class="text-slate-400 transition hover:text-white flex items-center gap-2">
                            <i class="fa-solid fa-chevron-right text-[9px] text-emerald-500"></i> Tentang Marketplace
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Contact Info --}}
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-400">
                    Kontak Sekolah
                </h3>

                <div class="mt-4 space-y-3 text-xs text-slate-400">

                    @if($settings?->address)
                        <div class="flex items-start gap-2.5">
                            <i class="fa-solid fa-location-dot mt-0.5 text-emerald-500 shrink-0"></i>
                            <span>{{ $settings->address }}</span>
                        </div>
                    @else
                        <div class="flex items-start gap-2.5">
                            <i class="fa-solid fa-location-dot mt-0.5 text-emerald-500 shrink-0"></i>
                            <span>Jl. Parangtritis Km. 11, Sabdodadi, Bantul, Yogyakarta</span>
                        </div>
                    @endif

                    @if($settings?->phone)
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-phone text-emerald-500 shrink-0"></i>
                            <span>{{ $settings->phone }}</span>
                        </div>
                    @endif

                    @if($settings?->email)
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-envelope text-emerald-500 shrink-0"></i>
                            <span class="break-all">{{ $settings->email }}</span>
                        </div>
                    @endif

                </div>
            </div>

            {{-- Security / Operational Badge --}}
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-400">
                    Sistem Pembelian
                </h3>

                <div class="mt-4 rounded-2xl border border-slate-800 bg-slate-900/80 p-4">
                    <div class="flex items-center gap-2 text-xs font-bold text-white mb-1.5">
                        <i class="fa-solid fa-shield-cat text-emerald-400 text-sm"></i>
                        <span>Transaksi Aman (COD)</span>
                    </div>
                    <p class="text-[11px] leading-relaxed text-slate-400">
                        Penjual & Pembeli merupakan warga SMKN 1 Bantul terverifikasi. Transaksi dilakukan secara langsung di area sekolah.
                    </p>
                </div>
            </div>

        </div>

        {{-- Copyright Bar --}}
        <div class="mt-10 border-t border-slate-900 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">

            <p class="text-center text-xs font-medium text-slate-500 sm:text-left">
                {{ $settings->copyright ?? '© ' . date('Y') . ' Eskasaba Market - SMKN 1 Bantul. All Rights Reserved.' }}
            </p>

            <div class="flex items-center gap-4 text-xs font-semibold text-slate-500">
                <span class="inline-flex items-center gap-1.5 text-emerald-400">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span> System Operational
                </span>
            </div>

        </div>

    </div>

</footer>