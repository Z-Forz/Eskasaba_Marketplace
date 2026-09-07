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
        Schema::create('payments', function (Blueprint $table) {

            $table->id();

            $table->decimal('amount', 12, 2);

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('method', [
                'cod',
                'qris',
            ]);

            // foto bukti pembayaran
            $table->string('proof')
                ->nullable();

            $table->enum('status', [
                'pending',
                'verified',
                'rejected'
            ])->default('pending');

            $table->timestamp('verified_at')
                ->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};