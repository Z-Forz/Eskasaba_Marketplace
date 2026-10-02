# Eskasaba Marketplace

Sistem Marketplace & E-Commerce Eskasaba yang dikembangkan untuk SMK Negeri 1 Bangsri. Aplikasi ini mengintegrasikan autentikasi SSO sekolah (SiPintu Gateway), manajemen seller/buyer, transaksi produk, serta bot notifikasi WhatsApp.

---

## 📚 Indeks Dokumentasi Proyek

Untuk memudahkan pengembang (developer) selanjutnya, seluruh panduan teknis dan alur sistem terbagi ke dalam file-file panduan berikut:

1. 📖 **[DEPLOYMENT_GUIDE.md](file:///home/muhammad/Desktop/eskasaba-marketplace/DEPLOYMENT_GUIDE.md)**
   * Panduan deployment ke server TEFA.
   * Perintah & troubleshooting PM2 (Laravel Queue Worker).
   * Daftar perintah penyebab hilangnya state PM2.
   * Konfigurasi `.env` & aturan `SESSION_LIFETIME`.

2. 🔀 **[FLOWCHART_USERS _ESKAMART.md](file:///home/muhammad/Desktop/eskasaba-marketplace/FLOWCHART_USERS _ESKAMART.md)**
   * Diagram & alur logika pengguna (Admin, Buyer, Seller, Siswa, Guru).
   * Flow autentikasi SSO SiPintu Gateway & OAuth.

3. 💬 **[WHATSAPPBOT_GUIDE.md](file:///home/muhammad/Desktop/eskasaba-marketplace/WHATSAPPBOT_GUIDE.md)**
   * Panduan integrasi bot WhatsApp.
   * Penggunaan API Baileys/Node.js service untuk notifikasi transaksi & OTP.

---

## 🛠️ Tech Stack & Persyaratan Sistem

* **Backend Framework:** Laravel 11.x (PHP 8.4)
* **Frontend:** Blade, Tailwind CSS, Vite
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
