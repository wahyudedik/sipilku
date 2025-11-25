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
        Schema::create('factory_products', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('factory_id')->constrained('factories')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('sku')->nullable();
            $table->string('code')->nullable(); // Kode produk pabrik
            $table->decimal('price', 15, 2);
            $table->decimal('discount_price', 15, 2)->nullable();
            $table->string('unit')->default('pcs'); // m3, m2, kg, pcs, ton, dll
            $table->json('available_units')->nullable(); // Unit yang tersedia untuk produk ini
            $table->json('specifications')->nullable(); // Spesifikasi teknis (grade, ukuran, berat, dll)
            $table->json('quality_grade')->nullable(); // Grade kualitas (K-100, K-125, dll untuk beton)
            $table->json('images')->nullable();
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('stock')->nullable(); // null = unlimited
            $table->integer('min_order')->default(1);
            $table->integer('view_count')->default(0);
            $table->integer('sold_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['factory_id', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factory_products');
    }
};
