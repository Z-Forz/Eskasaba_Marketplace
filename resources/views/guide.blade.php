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
                    Panduan Berbelanja, Seller & System Bot WA
                </h1>

                <p class="mt-4 text-sm leading-relaxed text-emerald-100/80 sm:text-base sm:leading-7">
                    Pelajari petunjuk praktis berbelanja dengan login NIS/Email, sistem notifikasi otomatis WhatsApp Bot, transaksi COD sekolah, serta tata cara pendaftaran & pengelolaan toko Seller.
                </p>
            </div>
        </div>
    </section>

    {{-- =========================================================
        MAIN NAVIGATION TABS & GUIDE CONTENT
    ========================================================== --}}
    <section class="bg-slate-50 py-14 sm:py-16 dark:bg-slate-900" x-data="{ activeTab: 'buyer' }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Filter Tabs --}}
            <div class="mb-10 flex flex-wrap justify-center gap-3">
                <button
                    @click="activeTab = 'buyer'"
                    :class="activeTab === 'buyer' ? 'bg-emerald-700 text-white shadow-lg shadow-emerald-900/20 ring-2 ring-emerald-500' : 'bg-white text-slate-700 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300'"
                    class="flex items-center gap-2 rounded-2xl px-5 py-3 text-sm font-bold transition cursor-pointer"
                >
                    <i class="fa-solid fa-shopping-bag"></i> Panduan Pembeli
                </button>

                <button
                    @click="activeTab = 'seller'"
                    :class="activeTab === 'seller' ? 'bg-emerald-700 text-white shadow-lg shadow-emerald-900/20 ring-2 ring-emerald-500' : 'bg-white text-slate-700 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300'"
                    class="flex items-center gap-2 rounded-2xl px-5 py-3 text-sm font-bold transition cursor-pointer"
                >
                    <i class="fa-solid fa-store"></i> Panduan Penjual
                </button>

                <button
                    @click="activeTab = 'apply_seller'"
                    :class="activeTab === 'apply_seller' ? 'bg-emerald-700 text-white shadow-lg shadow-emerald-900/20 ring-2 ring-emerald-500' : 'bg-white text-slate-700 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300'"
                    class="flex items-center gap-2 rounded-2xl px-5 py-3 text-sm font-bold transition cursor-pointer"
                >
                    <i class="fa-solid fa-id-card"></i> Cara Daftar Seller
                </button>

                <button
                    @click="activeTab = 'wabot_feedback'"
                    :class="activeTab === 'wabot_feedback' ? 'bg-emerald-700 text-white shadow-lg shadow-emerald-900/20 ring-2 ring-emerald-500' : 'bg-white text-slate-700 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300'"
                    class="flex items-center gap-2 rounded-2xl px-5 py-3 text-sm font-bold transition cursor-pointer"
                >
                    <i class="fa-brands fa-whatsapp text-emerald-400"></i> Notifikasi Bot WA
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
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Login via NIS / Email Sekolah</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                            Masuk email terdaftar contoh <code>12345@gmail.com (email yang digunakan di sijuna)</code>
                        </p>
                    </div>

                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-800 font-black text-lg mb-4 dark:bg-emerald-950 dark:text-emerald-300">
                            2
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Pilih Produk & Varian Rasa</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                            Jelajahi produk kuliner, alat tulis, atau aksesoris karya sekolah. Pilih varian rasa atau ukuran yang diinginkan lalu klik "Tambah ke Keranjang".
                        </p>
                    </div>

                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-800 font-black text-lg mb-4 dark:bg-emerald-950 dark:text-emerald-300">
                            3
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Titik Temu Pengambilan (COD)</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                            Saat checkout, tentukan lokasi pengambilan di area sekolah (Kantin Utama, Gazebo RPL, Depan Perpus, Lab Komputer, atau Pos Satpam).
                        </p>
                    </div>

                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-800 font-black text-lg mb-4 dark:bg-emerald-950 dark:text-emerald-300">
                            4
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Pembayaran (QRIS / Tunai COD)</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                            Bayar Tunai saat serah terima barang atau scan QRIS Toko non-tunai via E-Wallet / M-Banking dan upload bukti bayar di aplikasi.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-800 font-black text-lg mb-4 dark:bg-emerald-950 dark:text-emerald-300">
                            5
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Notifikasi Otomatis Bot WA</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                            Sistem WA Bot akan secara otomatis menginfokan invoice pesanan, instruksi bayar QRIS, dan kabar perubahan status pesanan Anda.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-800 font-black text-lg mb-4 dark:bg-emerald-950 dark:text-emerald-300">
                            6
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Ambil Barang & Beri Ulasan</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                            Setelah status menjadi "Siap Diambil", ambil pesanan di lokasi titik temu dan berikan rating ulasan bintang untuk penjual.
                        </p>
                    </div>

                </div>
            </div>

            {{-- TAB 2: PANDUAN PENJUAL (SELLER) --}}
            <div x-show="activeTab === 'seller'" x-transition class="space-y-8">
                <div class="text-center max-w-2xl mx-auto mb-8">
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white">Panduan Pengelolaan Toko Seller</h2>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Tata cara mengelola jualan, konfirmasi pesanan, dan verifikasi pembayaran QRIS.</p>
                </div>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">

                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-800 text-white font-black text-lg mb-4">
                            1
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Akses Dashboard Seller</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                            Buka menu profil lalu klik <strong>Dashboard Seller</strong> untuk memantau ringkasan penjualan, pesanan masuk, dan produk Anda.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-800 text-white font-black text-lg mb-4">
                            2
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Tambah Produk & Varian</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                            Unggah foto produk yang menarik, pilih kategori, tentukan harga, jumlah stok, serta opsi varian rasa / ukuran produk.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-800 text-white font-black text-lg mb-4">
                            3
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Unggah Barcode QRIS Toko</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                            Pastikan Anda sudah mengunggah gambar QRIS Toko di pengaturan profil seller agar pembeli dapat membayar secara non-tunai.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-800 text-white font-black text-lg mb-4">
                            4
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Notifikasi Pesanan & QRIS WA Bot</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                            Setiap ada pembeli yang melakukan order atau mengirimkan bukti transfer QRIS, Bot WA akan secara otomatis mengirim pesan notifikasi ke WhatsApp Anda.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-800 text-white font-black text-lg mb-4">
                            5
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Konfirmasi & Update Status Pesanan</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                            Periksa bukti transfer QRIS di kelola pesanan, ubah status pesanan dari <code>Menunggu Konfirmasi</code> menjadi <code>Diproses</code> lalu <code>Siap Diambil</code>.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-800 text-white font-black text-lg mb-4">
                            6
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Serah Terima di Titik Temu</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                            Temui pembeli di lokasi titik temu sekolah yang sudah disepakati untuk penyerahan barang dan penerimaan pembayaran COD tunai.
                        </p>
                    </div>

                </div>
            </div>

            {{-- TAB 3: CARA DAFTAR JADI SELLER --}}
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

            {{-- TAB 4: SYSTEM FEEDBACK NOTIFIKASI BOT WA --}}
            <div x-show="activeTab === 'wabot_feedback'" x-transition class="space-y-8">
                <div class="text-center max-w-2xl mx-auto mb-8">
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white">Sistem Notifikasi Otomatis WhatsApp Bot</h2>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Daftar lengkap alur notifikasi pesan WhatsApp otomatis yang dikirimkan oleh Bot ke Pembeli dan Penjual.</p>
                </div>

                <div class="grid gap-6 md:grid-cols-2">

                    {{-- Card Notifikasi Pembeli --}}
                    <div class="rounded-3xl border border-emerald-200/80 bg-white p-6 shadow-xs dark:border-emerald-900/50 dark:bg-slate-900">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 font-bold dark:bg-emerald-950 dark:text-emerald-300">
                                <i class="fa-solid fa-user text-base"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Notifikasi Untuk Pembeli (Buyer)</h3>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Pesan otomatis yang masuk ke WhatsApp Pembeli</p>
                            </div>
                        </div>

                        <ul class="space-y-3 text-xs text-slate-600 dark:text-slate-300">
                            <li class="flex items-start gap-2.5">
                                <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5"></i>
                                <div>
                                    <strong>Pesanan Baru Dibuat:</strong> Bot mengirim invoice lengkap (Kode Order, Rincian Produk, Total Harga, dan Lokasi Titik Temu COD).
                                </div>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5"></i>
                                <div>
                                    <strong>Instruksi Pembayaran QRIS:</strong> Saat checkout QRIS, Bot menyertakan petunjuk pengunggahan bukti bayar.
                                </div>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5"></i>
                                <div>
                                    <strong>Konfirmasi Penjual:</strong> Notifikasi saat Seller mengonfirmasi pembayaran dan mulai memproses pesanan.
                                </div>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5"></i>
                                <div>
                                    <strong>Pesanan Siap Diambil:</strong> Pemberitahuan bahwa barang sudah bisa diambil di titik temu sekolah.
                                </div>
                            </li>
                        </ul>
                    </div>

                    {{-- Card Notifikasi Penjual --}}
                    <div class="rounded-3xl border border-emerald-200/80 bg-white p-6 shadow-xs dark:border-emerald-900/50 dark:bg-slate-900">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 font-bold dark:bg-emerald-950 dark:text-emerald-300">
                                <i class="fa-solid fa-store text-base"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Notifikasi Untuk Penjual (Seller)</h3>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Pesan otomatis yang masuk ke WhatsApp Penjual</p>
                            </div>
                        </div>

                        <ul class="space-y-3 text-xs text-slate-600 dark:text-slate-300">
                            <li class="flex items-start gap-2.5">
                                <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5"></i>
                                <div>
                                    <strong>Order Masuk Baru:</strong> Info pesanan baru dari siswa/guru beserta nama pembeli dan lokasi serah terima.
                                </div>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5"></i>
                                <div>
                                    <strong>Pembayaran QRIS Diterima:</strong> Notifikasi saat pembeli telah mengunggah bukti bayar QRIS untuk diverifikasi.
                                </div>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5"></i>
                                <div>
                                    <strong>Pengingat Titik Temu COD:</strong> Pengingat waktu dan tempat lokasi pengambilan pesanan di sekolah.
                                </div>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5"></i>
                                <div>
                                    <strong>Pesanan Selesai / Rating:</strong> Info jika pesanan telah selesai dan diterima ulasan positif dari pembeli.
                                </div>
                            </li>
                        </ul>
                    </div>

                </div>
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
                        <span>Bagaimana format login menggunakan NIS atau Email sekolah?</span>
                        <i class="fa-solid" :class="openFaq === 1 ? 'fa-chevron-up text-emerald-600' : 'fa-chevron-down text-slate-400'"></i>
                    </button>

                    <div x-show="openFaq === 1" x-collapse class="px-5 pb-5 text-xs leading-relaxed text-slate-600 dark:text-slate-300 border-t border-slate-200/60 dark:border-slate-800 pt-3">
                        Anda dapat memilih login menggunakan <strong>NIS saja (contoh: 12345)</strong> atau menggunakan format email lengkap. Untuk angkatan atas menggunakan domain <code>@smkn1bangsri.sch.id</code> (contoh: <code>12345@smkn1bangsri.sch.id</code>), sedangkan untuk kelas 10/11 menggunakan domain <code>@sijuna.com</code> (contoh: <code>12345@sijuna.com</code>).
                    </div>
                </div>

                {{-- FAQ 2 --}}
                <div class="rounded-2xl border border-slate-200/80 bg-slate-50 overflow-hidden dark:border-slate-800 dark:bg-slate-900">
                    <button
                        @click="openFaq = (openFaq === 2 ? null : 2)"
                        class="flex w-full items-center justify-between p-5 text-left font-bold text-slate-900 dark:text-white hover:text-emerald-700 transition"
                    >
                        <span>Apakah transaksi di Eskasaba Marketplace aman?</span>
                        <i class="fa-solid" :class="openFaq === 2 ? 'fa-chevron-up text-emerald-600' : 'fa-chevron-down text-slate-400'"></i>
                    </button>

                    <div x-show="openFaq === 2" x-collapse class="px-5 pb-5 text-xs leading-relaxed text-slate-600 dark:text-slate-300 border-t border-slate-200/60 dark:border-slate-800 pt-3">
                        Ya, sangat aman. Semua seller di platform ini adalah siswa dan guru SMKN 1 Bangsri yang telah diverifikasi secara resmi oleh tim Admin Sekolah. Pengambilan barang dilakukan secara langsung di area sekolah (COD Sekolah).
                    </div>
                </div>

                {{-- FAQ 3 --}}
                <div class="rounded-2xl border border-slate-200/80 bg-slate-50 overflow-hidden dark:border-slate-800 dark:bg-slate-900">
                    <button
                        @click="openFaq = (openFaq === 3 ? null : 3)"
                        class="flex w-full items-center justify-between p-5 text-left font-bold text-slate-900 dark:text-white hover:text-emerald-700 transition"
                    >
                        <span>Apakah ada notifikasi WhatsApp otomatis saat saya berbelanja?</span>
                        <i class="fa-solid" :class="openFaq === 3 ? 'fa-chevron-up text-emerald-600' : 'fa-chevron-down text-slate-400'"></i>
                    </button>

                    <div x-show="openFaq === 3" x-collapse class="px-5 pb-5 text-xs leading-relaxed text-slate-600 dark:text-slate-300 border-t border-slate-200/60 dark:border-slate-800 pt-3">
                        Ya. Sistem kami terintegrasi dengan Bot WA yang akan mengirimkan rincian invoice saat pesanan dibuat, konfirmasi saat pembayaran QRIS dikirim, hingga pemberitahuan saat pesanan siap diambil di titik temu sekolah.
                    </div>
                </div>

                {{-- FAQ 4 --}}
                <div class="rounded-2xl border border-slate-200/80 bg-slate-50 overflow-hidden dark:border-slate-800 dark:bg-slate-900">
                    <button
                        @click="openFaq = (openFaq === 4 ? null : 4)"
                        class="flex w-full items-center justify-between p-5 text-left font-bold text-slate-900 dark:text-white hover:text-emerald-700 transition"
                    >
                        <span>Di mana lokasi serah terima barang (COD Sekolah)?</span>
                        <i class="fa-solid" :class="openFaq === 4 ? 'fa-chevron-up text-emerald-600' : 'fa-chevron-down text-slate-400'"></i>
                    </button>

                    <div x-show="openFaq === 4" x-collapse class="px-5 pb-5 text-xs leading-relaxed text-slate-600 dark:text-slate-300 border-t border-slate-200/60 dark:border-slate-800 pt-3">
                        Titik pengambilan ditentukan saat checkout berdasarkan lokasi kesepakatan di lingkungan sekolah, seperti Kantin Utama, Gazebo RPL, Depan Perpustakaan, Ruang Lab, atau Pos Satpam Gerbang Sekolah.
                    </div>
                </div>

                {{-- FAQ 5 --}}
                <div class="rounded-2xl border border-slate-200/80 bg-slate-50 overflow-hidden dark:border-slate-800 dark:bg-slate-900">
                    <button
                        @click="openFaq = (openFaq === 5 ? null : 5)"
                        class="flex w-full items-center justify-between p-5 text-left font-bold text-slate-900 dark:text-white hover:text-emerald-700 transition"
                    >
                        <span>Siapa saja yang boleh mendaftar menjadi seller?</span>
                        <i class="fa-solid" :class="openFaq === 5 ? 'fa-chevron-up text-emerald-600' : 'fa-chevron-down text-slate-400'"></i>
                    </button>

                    <div x-show="openFaq === 5" x-collapse class="px-5 pb-5 text-xs leading-relaxed text-slate-600 dark:text-slate-300 border-t border-slate-200/60 dark:border-slate-800 pt-3">
                        Seluruh siswa dan guru SMKN 1 Bangsri yang memiliki produk kewirausahaan (kuliner, barang kerajinan, atau jasa) berhak mengajukan pendaftaran toko seller melalui menu <strong>Daftar Jadi Seller</strong>.
                    </div>
                </div>

            </div>

        </div>
    </section>

</x-layouts.app>
