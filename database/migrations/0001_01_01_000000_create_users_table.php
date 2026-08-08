<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {

            $table->id();

            // Nama tampilan: siswa/guru = nama asli dari API Sekolah, admin = nama admin
            $table->string('username');

            // Login siswa/guru (nullable karena admin gak punya NIS/NIP)
            $table->string('nis_nip')
                ->nullable()
                ->unique();

            $table->string('email')
                ->nullable()
                ->unique();

            $table->string('password');

            // true selama masih pakai password default "password",
            // dipaksa ganti sebelum bisa akses fitur lain
            $table->boolean('is_default_password')
                ->default(false);

            $table->enum('role', [
                'admin',
                'student',
                'teacher'
            ])->default('student');

            $table->rememberToken();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};