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
        Schema::create('store_review_helpful_votes', function (Blueprint $table) {
            $table->id();
            $table->uuid('store_review_id');
            $table->foreign('store_review_id')->references('uuid')->on('store_reviews')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['store_review_id', 'user_id']); // One vote per user per review
            $table->index('store_review_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_review_helpful_votes');
    }
};
