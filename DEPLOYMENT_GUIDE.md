# Panduan Deployment & Manajemen Server - Eskasaba Marketplace

Dokumentasi ini berisi panduan deployment, pengelolaan process manager (PM2), penjelasan variabel lingkungan (.env), serta perintah-perintah penting di server Linux.

---

## 1. Pengelolaan PM2 (Process Manager)

PM2 digunakan di server untuk menjalankan Laravel Queue Worker dan service pendukung (seperti WhatsApp Bot) secara background agar terus berjalan.

### Perintah Dasar PM2
```bash
# Cek status proses yang sedang berjalan
npx pm2 status

# Menjalankan Laravel Scheduler (Otomatisasi Broadcast & Cron)
# 1. Buat script helper scheduler.sh:
#    cat << 'EOF' > scheduler.sh
#    #!/bin/bash
#    cd "$(dirname "$0")"
#    while true; do
#      php84 artisan schedule:run
#      sleep 60
#    done
#    EOF
#    chmod +x scheduler.sh
# 2. Jalankan di PM2:
npx pm2 start scheduler.sh --name "laravel-scheduler" --interpreter bash

# Menjalankan WhatsApp Bot (jika ada)
npx pm2 start "node whatsapp-bot/index.js" --name "whatsapp-bot"

# Restart worker/scheduler setelah update kode
npx pm2 restart laravel-scheduler
npx pm2 restart laravel-worker

# Menyimpan state/daftar proses PM2 ke disk (SANGAT PENTING!)
npx pm2 save

# Memulihkan/mengembalikan proses dari simpanan terakhir (setelah restart server/daemon)
npx pm2 resurrect
```

---

## 2. Penyebab Daftar PM2 Hilang / Kosong

Jika saat menjalankan `npx pm2 status` muncul pesan `[PM2] Spawning PM2 daemon...` dan daftar proses menjadi kosong (`0` processes), hal itu disebabkan oleh beberapa perintah atau kejadian berikut:

| Perintah / Kejadian | Efek pada PM2 | Penjelasan & Pencegahan |
| :--- | :--- | :--- |
| `npx pm2 kill` / `pm2 kill` | Daemon Mati & Memory Clear | Mematikan daemon PM2. Semua proses yang berjalan di memori akan terhenti. |
| `npx pm2 delete all` / `pm2 delete <id>` | Menghapus Proses | Menghapus proses secara eksplisit dari daftar PM2. |
| `reboot` / `sudo reboot` (Server Restart) | Reset RAM | PM2 berjalan di RAM. Saat server reboot, daemon PM2 mati. Gunakan `npx pm2 resurrect` untuk mengembalikannya jika sebelumnya sudah di-`npx pm2 save`. |
| `pkill node` / `killall node` | Daemon Mati | Mematikan semua node process di OS, termasuk daemon PM2. |
| OOM (Out Of Memory) / Crash | Daemon Ter-kill OS | Jika RAM server habis saat `npm run build` atau `php artisan optimize`, OS akan mematikan daemon PM2 secara paksa. |
| Lupa menjalankan `npx pm2 save` | State Tidak Tersimpan | Menjalankan `npx pm2 start` tanpa `npx pm2 save` membuat daftar PM2 hilang total begitu daemon mati/reboot. |

> **Tips Pencegahan:** Setiap kali membuat atau mengubah proses PM2, **wajib** jalankan `npx pm2 save`. Jika server sempat mati/reboot, jalankan `npx pm2 resurrect`.

---

## 3. SOP Deployment di Server (Production / TEFA Server)

Setiap kali melakukan deployment kode baru di server, ikuti urutan perintah berikut:

```bash
# 1. Masuk ke direktori proyek
cd ~/public_html

# 2. Tarik kode terbaru dari Git
git pull origin main

# 3. Jalankan migrasi database (jika ada)
php84 artisan migrate --force

# 4. Build asset frontend
npm run build

# 5. Optimize & clear cache Laravel
php84 artisan optimize:clear
php84 artisan optimize

# 6. Restart Queue Worker di PM2 (Agar worker membaca kode terbaru)
npx pm2 restart laravel-worker || npx pm2 start "php84 artisan queue:work --tries=3" --name "laravel-worker"

# 7. Simpan state PM2
npx pm2 save
```

---

## 4. Konfigurasi Session di File `.env` (Laravel)

Pengaturan Session Lifetime di file `.env` Laravel dihitung dalam satuan **MENIT**, **bukan detik**!

```env
SESSION_DRIVER=file
SESSION_LIFETIME=60
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
```

### Panduan Penulisan `SESSION_LIFETIME`:
* **1 Jam** = `SESSION_LIFETIME=60` *(60 Menit)* **(Rekomendasi)**
* **2 Jam** = `SESSION_LIFETIME=120` *(120 Menit - Default Laravel)*
* **24 Jam** = `SESSION_LIFETIME=1440` *(1440 Menit)*
* **❌ JANGAN TULIS `3600`** jika maksudnya 1 jam! `3600` di Laravel artinya **3.600 Menit = 60 Jam (2,5 Hari)**.

Setelah mengubah file `.env`, pastikan jalankan:
```bash
php84 artisan config:clear
php84 artisan optimize
```
