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
        Schema::create('factory_review_helpful_votes', function (Blueprint $table) {
            $table->id();
            $table->uuid('factory_review_id');
            $table->foreign('factory_review_id')->references('uuid')->on('factory_reviews')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['factory_review_id', 'user_id']); // One vote per user per review
            $table->index('factory_review_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factory_review_helpful_votes');
    }
};
