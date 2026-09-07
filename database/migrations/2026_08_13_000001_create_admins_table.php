<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel khusus untuk akun admin panel.
     * Terpisah dari tabel users (siswa/guru dari API sekolah).
     */
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {

            $table->id();

            $table->string('username')->unique();

            $table->string('email')->nullable()->unique();

            $table->string('password');

            $table->rememberToken();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
