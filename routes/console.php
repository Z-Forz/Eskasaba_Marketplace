<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes & Scheduled Tasks
|--------------------------------------------------------------------------
|
| Di sini Anda dapat mendaftarkan semua perintah konsol closure dan
| penjadwalan tugas (scheduled tasks) untuk aplikasi.
|
*/

// Menjalankan pembersihan notifikasi lama (lebih dari 7 hari) setiap hari pukul 00:00 WIB
Schedule::command('notifications:clean')->dailyAt('00:00')->timezone('Asia/Jakarta');

// Menjalankan sinkronisasi pengguna SiPintu Gateway setiap hari pukul 00:00 WIB
Schedule::command('sipintu:sync')->dailyAt('00:00')->timezone('Asia/Jakarta');

// Otomatis mengonfirmasi pesanan yang telah 3 hari menjadi Selesai setiap jam
Schedule::command('orders:auto-complete')->hourly();

