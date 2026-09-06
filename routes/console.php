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

// Menjalankan pembersihan notifikasi lama (lebih dari 7 hari) setiap hari pukul 00:00
Schedule::command('notifications:clean')->daily();

// Menjalankan sinkronisasi otomatis data pengguna SiPintu Gateway setiap hari pukul 00:00 WIB
Schedule::command('sipintu:sync')
    ->dailyAt('00:00')
    ->timezone('Asia/Jakarta');

