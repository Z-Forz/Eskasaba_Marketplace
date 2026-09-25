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
        // Modify enum for orders.status if MySQL to support return_requested and refund_pending_buyer_confirmation
        try {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status VARCHAR(50) DEFAULT 'pending'");
        } catch (\Throwable $e) {
            // Fallback for drivers that don't support statement
        }

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'return_proof_image')) {
                $table->string('return_proof_image')->nullable()->after('cancellation_reason');
            }
            if (! Schema::hasColumn('orders', 'refund_confirmed_at')) {
                $table->timestamp('refund_confirmed_at')->nullable()->after('cancellation_status');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'buyer_confirmed_refund')) {
                $table->boolean('buyer_confirmed_refund')->default(false)->after('refunded_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'return_proof_image')) {
                $table->dropColumn('return_proof_image');
            }
            if (Schema::hasColumn('orders', 'refund_confirmed_at')) {
                $table->dropColumn('refund_confirmed_at');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'buyer_confirmed_refund')) {
                $table->dropColumn('buyer_confirmed_refund');
            }
        });
    }
};
