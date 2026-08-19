<x-layouts.app title="Panduan Penggunaan & Cara Daftar Seller">

    {{-- =========================================================
        PANDUAN HERO
    ========================================================== --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-emerald-950 via-slate-950 to-emerald-900 py-16 sm:py-20 lg:py-24 text-white">

        {{-- Glow Accents --}}
        <div class="pointer-events-none absolute -left-20 -top-20 h-96 w-96 rounded-full bg-emerald-500/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-20 -bottom-20 h-96 w-96 rounded-full bg-emerald-700/20 blur-3xl"></div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-4 py-1.5 text-xs font-bold text-emerald-300 backdrop-blur-md">
                    <i class="fa-solid fa-circle-question text-emerald-400"></i> Pusat Bantuan & Panduan Pengguna
                </span>

                <h1 class="mt-4 text-3xl font-black leading-tight tracking-tight text-white sm:text-4xl lg:text-5xl">
                    Panduan Berbelanja & Cara Daftar Seller
                </h1>

                <p class="mt-4 text-sm leading-relaxed text-emerald-100/80 sm:text-base sm:leading-7">
                    Pelajari petunjuk praktis berbelanja produk sekolah dan tata cara mendaftarkan toko kewirausahaan Anda di Eskasaba Marketplace.
                </p>
            </div>
        </div>
    </section>

    {{-- =========================================================
        PANDUAN PEMBELI & DAFTAR SELLER
    ========================================================== --}}
    <section class="bg-slate-50 py-14 sm:py-16 dark:bg-slate-900" x-data="{ activeTab: 'buyer' }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Filter Tabs --}}
            <div class="mb-10 flex flex-wrap justify-center gap-3">
                <button
                    @click="activeTab = 'buyer'"
                    :class="activeTab === 'buyer' ? 'bg-emerald-700 text-white shadow-lg shadow-emerald-900/20 ring-2 ring-emerald-500' : 'bg-white text-slate-700 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300'"
                    class="flex items-center gap-2 rounded-2xl px-6 py-3.5 text-sm font-bold transition cursor-pointer"
                >
                    <i class="fa-solid fa-shopping-bag"></i> Panduan Pembeli (Buyer)
                </button>

                <button
                    @click="activeTab = 'apply_seller'"
                    :class="activeTab === 'apply_seller' ? 'bg-emerald-700 text-white shadow-lg shadow-emerald-900/20 ring-2 ring-emerald-500' : 'bg-white text-slate-700 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300'"
                    class="flex items-center gap-2 rounded-2xl px-6 py-3.5 text-sm font-bold transition cursor-pointer"
                >
                    <i class="fa-solid fa-store"></i> Cara Daftar Jadi Seller
                </button>
            </div>

            {{-- TAB 1: PANDUAN PEMBELI (BUYER) --}}
            <div x-show="activeTab === 'buyer'" x-transition class="space-y-8">
                <div class="text-center max-w-2xl mx-auto mb-8">
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white">6 Langkah Mudah Berbelanja</h2>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Petunjuk praktis bagi siswa dan guru untuk bertransaksi di lingkungan sekolah.</p>
                </div>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">

                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-800 font-black text-lg mb-4 dark:bg-emerald-950 dark:text-emerald-300">
                            1
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Registrasi & Login Akun</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                            Daftarkan akun Pembeli menggunakan nama pengguna dan email Anda. Ganti kata sandi default Anda demi keamanan akun.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-800 font-black text-lg mb-4 dark:bg-emerald-950 dark:text-emerald-300">
                            2
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Pilih Produk & Varian Rasa</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                            Jelajahi produk karya siswa & guru, pilih opsi varian rasa yang Anda sukai, lalu klik "Tambah ke Keranjang".
                        </p>
                    </div>

                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-800 font-black text-lg mb-4 dark:bg-emerald-950 dark:text-emerald-300">
                            3
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Titik Temu Pengambilan (COD)</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                            Saat checkout, tentukan lokasi pengambilan barang di sekolah (Kantin Utama, Gazebo RPL, Depan Perpus, Lab, atau Pos Satpam).
                        </p>
                    </div>

                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-800 font-black text-lg mb-4 dark:bg-emerald-950 dark:text-emerald-300">
                            4
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Metode Pembayaran (QRIS / COD)</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                            Pilih pembayaran Tunai COD saat serah terima barang atau scan Barcode QRIS Toko non-tunai via E-Wallet/m-Banking.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-800 font-black text-lg mb-4 dark:bg-emerald-950 dark:text-emerald-300">
                            5
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Hubungi Seller via WhatsApp</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                            Klik tombol "Chat WA Seller" di halaman Detail Pesanan untuk mengirim rincian invoice dan rincian produk secara otomatis ke WhatsApp penjual.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-800 font-black text-lg mb-4 dark:bg-emerald-950 dark:text-emerald-300">
                            6
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Ambil Barang & Beri Rating Ulasan</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                            Setelah status pesanan menjadi "Siap Diambil", ambil pesanan di lokasi titik temu dan berikan rating ulasan bintang untuk mendukung seller.
                        </p>
                    </div>

                </div>
            </div>

            {{-- TAB 2: CARA DAFTAR JADI SELLER --}}
            <div x-show="activeTab === 'apply_seller'" x-transition class="space-y-8">
                <div class="text-center max-w-2xl mx-auto mb-8">
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white">Cara Mendaftar Sebagai Seller Sekolah</h2>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Buka toko digital kewirausahaan Anda di Eskasaba Marketplace dengan 4 langkah mudah.</p>
                </div>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">

                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-800 text-white font-black text-lg mb-4">
                            1
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Masuk ke Form Pengajuan</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                            Setelah login ke akun Anda, klik menu nama profil di kanan atas lalu pilih menu <strong>"Daftar Jadi Seller"</strong>.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-800 text-white font-black text-lg mb-4">
                            2
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Isi Data Toko & Nomor WA</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                            Lengkapi nomor WhatsApp aktif toko Anda, nama usaha, serta deskripsi produk yang akan dijual (kuliner, karya kerajinan, atau jasa).
                        </p>
                    </div>

                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-800 text-white font-black text-lg mb-4">
                            3
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Verifikasi Admin Sekolah</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                            Pengajuan Anda akan ditinjau dan diverifikasi oleh Admin Sekolah. Anda dapat memantau status persetujuan di dashboard akun.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-800 text-white font-black text-lg mb-4">
                            4
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Unggah QRIS & Tambah Produk</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                            Setelah disetujui, masuk ke Dashboard Seller, unggah foto Barcode QRIS toko Anda, dan mulai mengunggah katalog produk jualan!
                        </p>
                    </div>

                </div>

                @auth
                    @if(auth()->user()->role === 'buyer' || auth()->user()->role === 'teacher' || auth()->user()->role === 'student')
                        <div class="mt-8 text-center">
                            <a
                                href="{{ route('buyer.apply-seller') }}"
                                class="inline-flex items-center gap-2 rounded-2xl bg-emerald-700 px-8 py-4 text-sm font-bold text-white shadow-lg shadow-emerald-900/30 transition hover:bg-emerald-800"
                            >
                                <i class="fa-solid fa-store text-base"></i>
                                <span>Klik di Sini untuk Mengajukan Pendaftaran Seller</span>
                            </a>
                        </div>
                    @endif
                @else
                    <div class="mt-8 text-center">
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex items-center gap-2 rounded-2xl bg-emerald-700 px-8 py-4 text-sm font-bold text-white shadow-lg shadow-emerald-900/30 transition hover:bg-emerald-800"
                        >
                            <i class="fa-solid fa-right-to-bracket text-base"></i>
                            <span>Login Terlebih Dahulu untuk Mendaftar Seller</span>
                        </a>
                    </div>
                @endauth
            </div>

        </div>
    </section>

    {{-- =========================================================
        PERTANYAAN UMUM (FAQ) ACCORDION
    ========================================================== --}}
    <section class="bg-white py-16 sm:py-20 dark:bg-slate-950">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-12">
                <span class="text-xs font-black uppercase tracking-wider text-emerald-700 dark:text-emerald-400">
                    <i class="fa-solid fa-comments"></i> Pertanyaan Sering Diajukan
                </span>
                <h2 class="mt-2 text-2xl font-black tracking-tight text-slate-900 sm:text-3xl dark:text-white">
                    Pertanyaan Umum (FAQ)
                </h2>
            </div>

            <div class="space-y-4" x-data="{ openFaq: 1 }">

                {{-- FAQ 1 --}}
                <div class="rounded-2xl border border-slate-200/80 bg-slate-50 overflow-hidden dark:border-slate-800 dark:bg-slate-900">
                    <button
                        @click="openFaq = (openFaq === 1 ? null : 1)"
                        class="flex w-full items-center justify-between p-5 text-left font-bold text-slate-900 dark:text-white hover:text-emerald-700 transition"
                    >
                        <span>Apakah transaksi di Eskasaba Marketplace aman?</span>
                        <i class="fa-solid" :class="openFaq === 1 ? 'fa-chevron-up text-emerald-600' : 'fa-chevron-down text-slate-400'"></i>
                    </button>

                    <div x-show="openFaq === 1" x-collapse class="px-5 pb-5 text-xs leading-relaxed text-slate-600 dark:text-slate-300 border-t border-slate-200/60 dark:border-slate-800 pt-3">
                        Ya, sangat aman. Semua seller di platform ini adalah siswa dan guru SMKN 1 Bangsri yang telah diverifikasi secara resmi oleh tim Admin Sekolah. Pengambilan barang dilakukan secara langsung di area sekolah (COD Sekolah).
                    </div>
                </div>

                {{-- FAQ 2 --}}
                <div class="rounded-2xl border border-slate-200/80 bg-slate-50 overflow-hidden dark:border-slate-800 dark:bg-slate-900">
                    <button
                        @click="openFaq = (openFaq === 2 ? null : 2)"
                        class="flex w-full items-center justify-between p-5 text-left font-bold text-slate-900 dark:text-white hover:text-emerald-700 transition"
                    >
                        <span>Siapa saja yang boleh mendaftar menjadi seller?</span>
                        <i class="fa-solid" :class="openFaq === 2 ? 'fa-chevron-up text-emerald-600' : 'fa-chevron-down text-slate-400'"></i>
                    </button>

                    <div x-show="openFaq === 2" x-collapse class="px-5 pb-5 text-xs leading-relaxed text-slate-600 dark:text-slate-300 border-t border-slate-200/60 dark:border-slate-800 pt-3">
                        Seluruh siswa dan guru SMKN 1 Bangsri yang memiliki produk kewirausahaan (kuliner, barang kerajinan, atau jasa) berhak mengajukan pendaftaran toko seller.
                    </div>
                </div>

                {{-- FAQ 3 --}}
                <div class="rounded-2xl border border-slate-200/80 bg-slate-50 overflow-hidden dark:border-slate-800 dark:bg-slate-900">
                    <button
                        @click="openFaq = (openFaq === 3 ? null : 3)"
                        class="flex w-full items-center justify-between p-5 text-left font-bold text-slate-900 dark:text-white hover:text-emerald-700 transition"
                    >
                        <span>Di mana lokasi serah terima barang (COD Sekolah)?</span>
                        <i class="fa-solid" :class="openFaq === 3 ? 'fa-chevron-up text-emerald-600' : 'fa-chevron-down text-slate-400'"></i>
                    </button>

                    <div x-show="openFaq === 3" x-collapse class="px-5 pb-5 text-xs leading-relaxed text-slate-600 dark:text-slate-300 border-t border-slate-200/60 dark:border-slate-800 pt-3">
                        Titik pengambilan ditentukan berdasarkan kesepakatan antara Pembeli dan Penjual di lingkungan sekolah, seperti Kantin Utama, Gazebo RPL, Depan Perpustakaan, Ruang Lab, atau Pos Satpam Gerbang Sekolah.
                    </div>
                </div>

                {{-- FAQ 4 --}}
                <div class="rounded-2xl border border-slate-200/80 bg-slate-50 overflow-hidden dark:border-slate-800 dark:bg-slate-900">
                    <button
                        @click="openFaq = (openFaq === 4 ? null : 4)"
                        class="flex w-full items-center justify-between p-5 text-left font-bold text-slate-900 dark:text-white hover:text-emerald-700 transition"
                    >
                        <span>Bagaimana cara menghubungi seller via WhatsApp?</span>
                        <i class="fa-solid" :class="openFaq === 4 ? 'fa-chevron-up text-emerald-600' : 'fa-chevron-down text-slate-400'"></i>
                    </button>

                    <div x-show="openFaq === 4" x-collapse class="px-5 pb-5 text-xs leading-relaxed text-slate-600 dark:text-slate-300 border-t border-slate-200/60 dark:border-slate-800 pt-3">
                        Pada halaman Detail Pesanan, klik tombol "Hubungi Seller via WA". Sistem akan membuka aplikasi WhatsApp secara otomatis dengan pesan rincian pesanan dan titik temu yang sudah terformat rapi.
                    </div>
                </div>

            </div>

        </div>
    </section>

</x-layouts.app>
