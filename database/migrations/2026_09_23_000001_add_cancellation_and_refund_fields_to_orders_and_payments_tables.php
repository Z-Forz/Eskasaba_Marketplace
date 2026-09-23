<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify enum for orders.status if MySQL
        try {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'confirmed', 'processing', 'ready_for_pickup', 'completed', 'cancelled', 'cancel_requested') DEFAULT 'pending'");
        } catch (\Throwable $e) {
            // Fallback for drivers that don't support ALTER ENUM directly
        }

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'cancelled_by')) {
                $table->enum('cancelled_by', ['buyer', 'seller', 'admin'])->nullable()->after('status');
            }
            if (! Schema::hasColumn('orders', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('cancelled_by');
            }
            if (! Schema::hasColumn('orders', 'cancellation_status')) {
                $table->enum('cancellation_status', ['none', 'pending', 'approved', 'rejected'])->default('none')->after('cancellation_reason');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'refund_proof')) {
                $table->string('refund_proof')->nullable()->after('verified_at');
            }
            if (! Schema::hasColumn('payments', 'refund_notes')) {
                $table->text('refund_notes')->nullable()->after('refund_proof');
            }
            if (! Schema::hasColumn('payments', 'refunded_at')) {
                $table->timestamp('refunded_at')->nullable()->after('refund_notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['cancelled_by', 'cancellation_reason', 'cancellation_status']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['refund_proof', 'refund_notes', 'refunded_at']);
        });
    }
};
