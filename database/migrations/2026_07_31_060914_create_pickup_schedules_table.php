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
        Schema::create('pickup_schedules', function (Blueprint $table) {

            $table->id();


            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();


            $table->date('pickup_date');


            $table->time('pickup_time');


            $table->boolean('is_picked_up')
                ->default(false);


            $table->timestamp('picked_up_at')
                ->nullable();


            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickup_schedules');
    }
};
