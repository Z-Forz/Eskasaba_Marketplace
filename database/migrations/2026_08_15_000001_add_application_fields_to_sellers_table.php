<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sellers', function (Blueprint $table) {

            // Alasan ingin menjadi seller
            $table->text('reason')->nullable()->after('description');

            // Rencana produk yang akan dijual
            $table->text('products_plan')->nullable()->after('reason');

            // Catatan dari admin (saat reject / minta revisi)
            $table->text('rejection_note')->nullable()->after('products_plan');

            // Ubah enum status: tambah 'revision'
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'revision',
            ])->default('pending')->change();

        });
    }

    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->dropColumn(['reason', 'products_plan', 'rejection_note']);

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
            ])->default('pending')->change();
        });
    }
};
