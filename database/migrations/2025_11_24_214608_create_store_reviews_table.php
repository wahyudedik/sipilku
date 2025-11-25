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
        Schema::create('store_reviews', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('store_id');
            $table->foreign('store_id')->references('uuid')->on('stores')->onDelete('cascade');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('uuid')->on('users')->onDelete('cascade');
            $table->integer('rating')->default(5); // 1-5
            $table->text('comment')->nullable();
            $table->json('ratings_breakdown')->nullable(); // kualitas produk, pelayanan, harga, dll
            $table->boolean('is_verified_purchase')->default(false);
            $table->boolean('is_approved')->default(true);
            $table->integer('helpful_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['store_id', 'is_approved']);
            $table->index('user_id');
            $table->unique(['store_id', 'user_id']); // Satu user hanya bisa review sekali per store
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_reviews');
    }
};
