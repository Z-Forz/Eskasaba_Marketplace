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
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'pickup_location')) {
                $table->string('pickup_location')->nullable()->after('total_price');
            }
            if (! Schema::hasColumn('orders', 'note')) {
                $table->text('note')->nullable()->after('pickup_location');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'pickup_location')) {
                $table->dropColumn('pickup_location');
            }
            if (Schema::hasColumn('orders', 'note')) {
                $table->dropColumn('note');
            }
        });
    }
};
