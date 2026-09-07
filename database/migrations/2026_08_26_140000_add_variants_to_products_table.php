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
        Schema::table('products', function (Blueprint $table) {
            $table->json('variants')->nullable()->after('price');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->string('variant_name')->nullable()->after('product_id');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('variant_name')->nullable()->after('product_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('variants');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn('variant_name');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('variant_name');
        });
    }
};
