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
        Schema::create('sellers', function (Blueprint $table) {

            $table->id();
            
            // Pemilik toko
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('whatsapp_number');

            // Deskripsi toko
            $table->text('description')
                ->nullable();


            // Status verifikasi admin
            $table->enum('status', [
                'pending',
                'approved',
                'rejected'
            ])->default('pending');


            // Waktu disetujui admin
            $table->timestamp('approved_at')
                ->nullable();


            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};
