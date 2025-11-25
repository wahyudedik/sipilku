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
        Schema::create('factory_reviews', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('factory_id')->constrained('factories')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('rating')->default(5); // 1-5 overall
            $table->json('ratings_breakdown')->nullable(); // quality, price, delivery, service
            $table->text('comment')->nullable();
            $table->boolean('is_verified_purchase')->default(false);
            $table->boolean('is_approved')->default(true);
            $table->integer('helpful_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['factory_id', 'is_approved']);
            $table->index('user_id');
            $table->unique(['factory_id', 'user_id']); // Satu user hanya bisa review sekali per factory
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factory_reviews');
    }
};
