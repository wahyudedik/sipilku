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
        Schema::create('store_products', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('store_id');
            $table->foreign('store_id')->references('uuid')->on('stores')->onDelete('cascade');
            $table->uuid('store_category_id')->nullable();
            $table->foreign('store_category_id')->references('uuid')->on('store_categories')->onDelete('set null');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('sku')->nullable();
            $table->string('brand')->nullable();
            $table->decimal('price', 15, 2);
            $table->decimal('discount_price', 15, 2)->nullable();
            $table->string('unit')->default('pcs'); // pcs, kg, m, m2, m3, dll
            $table->integer('stock')->default(0);
            $table->integer('min_order')->default(1);
            $table->json('images')->nullable();
            $table->json('specifications')->nullable(); // dimensi, berat, material, dll
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('view_count')->default(0);
            $table->integer('sold_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['store_id', 'is_active']);
            $table->index('store_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_products');
    }
};
