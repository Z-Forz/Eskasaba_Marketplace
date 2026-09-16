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
            $table->string('slug')->nullable()->unique()->after('name');
        });

        // Generate slug for existing products
        $products = \Illuminate\Support\Facades\DB::table('products')->get();
        foreach ($products as $product) {
            $slug = \Illuminate\Support\Str::slug($product->name) . '-' . \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(6));
            \Illuminate\Support\Facades\DB::table('products')
                ->where('id', $product->id)
                ->update(['slug' => $slug]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
