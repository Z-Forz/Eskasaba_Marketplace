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
                'student',
                'teacher',
            ])->default('student');

            // Data dari API Sekolah (wajib diisi — user sekolah selalu punya data ini)
            $table->unsignedBigInteger('api_id');
            $table->string('school_number');
            $table->string('name');
            $table->string('type');        // siswa / guru
            $table->date('birth_date');
            $table->string('class')->nullable();  // nullable: guru tidak punya kelas
            $table->string('major')->nullable();  // nullable: guru tidak punya jurusan
            $table->string('phone')->nullable();
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