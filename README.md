# Eskasaba Marketplace

Sistem Marketplace & E-Commerce Eskasaba yang dikembangkan untuk SMK Negeri 1 Bangsri. Aplikasi ini mengintegrasikan autentikasi SSO sekolah (SiPintu Gateway), manajemen seller/buyer, transaksi produk, preservasi riwayat transaksi alumni, serta microservice bot notifikasi WhatsApp.

---

## ✨ Fitur Utama Sistem

1. **🔐 Autentikasi SSO & Integrasi SiPintu Gateway**
   * Single Sign-On (SSO) menggunakan akun NIS/NIP sekolah via SiPintu Gateway & OAuth Callback.
   * Sinkronisasi password real-time & sinkronisasi otomatis nomor WhatsApp tanpa menimpa nomor HP lokal.

2. **🎓 Siklus Kelulusan Seller & Preservasi Riwayat Pesanan Alumni**
   * **Notifikasi Otomatis Kelulusan (1 April):** Perintah `sellers:notify-graduating` secara otomatis mengirim notifikasi WhatsApp pada awal April ke seller Kelas 12 agar menyelesaikan transaksi dan menonaktifkan produk sebelum berubah status menjadi alumni.
   * **Preservasi Riwayat Transaksi:** Riwayat pesanan pembeli tetap dipertahankan secara utuh. Jika penjual telah lulus/nonaktif, nama penjual pada invoice & riwayat transaksi akan secara eksplisit menampilkan label *(Penjual Nonaktif / Alumni)*.

3. **🏪 Kelola Toko & Pencabutan Seller Beralasan**
   * Pendaftaran toko siswa/guru dengan verifikasi admin (Setujui, Minta Revisi, Tolak/Cabut).
   * Form alasan pencabutan status seller yang dikirimkan secara langsung melalui WhatsApp ke seller bersangkutan.
   * Request penambahan kategori/fitur dari seller ke admin.

4. **📱 Microservice WhatsApp Bot (Baileys Node.js)**
   * Notifikasi otomatis pesanan baru, perubahan status pickup, pengajuan seller, pencabutan toko, serta peringatan kelulusan.
   * Panel manajemen bot di admin (`/admin/whatsapp`) untuk monitoring realtime, scan QR Code, restart PID process, dan uji kirim pesan.

---

## 📚 Indeks Dokumentasi Proyek

Untuk memudahkan pengembang (*developer*) selanjutnya, seluruh panduan teknis dan alur sistem terbagi ke dalam file-file panduan berikut:

1. 📖 **[DEPLOYMENT_GUIDE.md](file:///home/muhammad/Desktop/eskasaba-marketplace/DEPLOYMENT_GUIDE.md)**
   * Panduan deployment ke server TEFA.
   * Perintah & troubleshooting PM2 (Laravel Queue Worker & WhatsApp Bot).
   * Konfigurasi Penjadwalan Tugas (Cron Jobs master & perintah artisan terjadwal `sellers:notify-graduating`, `sipintu:sync`, `orders:auto-complete`).
   * Konfigurasi `.env` & aturan `SESSION_LIFETIME`.

2. 🔀 **[FLOWCHART_USERS _ESKAMART.md](file:///home/muhammad/Desktop/eskasaba-marketplace/FLOWCHART_USERS _ESKAMART.md)**
   * Diagram & alur logika pengguna (Admin, Buyer, Seller, Siswa, Guru).
   * State Diagram siklus pesanan, verifikasi/pencabutan toko beralasan, serta alur pengingat kelulusan kelas 12.
   * Flow autentikasi SSO SiPintu Gateway & OAuth.

3. 💬 **[WHATSAPPBOT_GUIDE.md](file:///home/muhammad/Desktop/eskasaba-marketplace/WHATSAPPBOT_GUIDE.md)**
   * Panduan integrasi bot WhatsApp (Baileys REST API).
   * Format pesan & trigger event (Pesanan Baru, Update Status, Pencabutan Toko Beralasan, & Peringatan Kelulusan Seller Kelas 12).

---

## 🛠️ Tech Stack & Persyaratan Sistem

* **Backend Framework:** Laravel 11.x (PHP 8.4)
* **Frontend:** Blade, Custom CSS UI System, Vite
* **Database:** MySQL
* **Queue / Process Manager:** Database Queue Driver, PM2 (Node.js)
* **Web Server:** Nginx / Apache (Hostinger / Server TEFA)

---

## 🚀 Panduan Lokal Development

### 1. Clone Repository & Install Dependencies
```bash
git clone https://github.com/Z-Forz/Eskasaba_Marketplace.git
cd Eskasaba_Marketplace

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 2. Konfigurasi Environment (`.env`)
```bash
cp .env.example .env
php artisan key:generate
```
Sesuaikan koneksi database MySQL & kredensial SiPintu Gateway di `.env`.

### 3. Migrasi Database & Seeder
```bash
php artisan migrate --seed
```

### 4. Menjalankan Aplikasi Lokal
```bash
# Terminal 1: Laravel Server
php artisan serve

# Terminal 2: Vite Dev Server
npm run dev

# Terminal 3: Queue Worker (opsional jika menguji antrean)
php artisan queue:work
```

---

## 💡 Kontak & Hak Cipta
Dipersembahkan oleh **3R** khusus untuk **SMK N 1 BANGSRI** (Educational Purpose Only).

