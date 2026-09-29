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
        Schema::create('whatsapp_broadcasts', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('target_type')->default('all'); // all, teacher, student_10, student_11, student_12
            $table->text('message');
            $table->unsignedInteger('total_recipients')->default(0);
            $table->unsignedInteger('sent_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->unsignedInteger('delay_seconds')->default(3);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])
                ->default('pending');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('whatsapp_broadcast_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('whatsapp_broadcast_id')
                ->constrained('whatsapp_broadcasts')
                ->onDelete('cascade');
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->string('phone');
            $table->string('user_name')->nullable();
            $table->string('recipient_group')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed', 'skipped'])
                ->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_broadcast_logs');
        Schema::dropIfExists('whatsapp_broadcasts');
    }
};
