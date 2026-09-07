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
