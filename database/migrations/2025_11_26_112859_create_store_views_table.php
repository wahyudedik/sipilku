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
        Schema::create('store_views', function (Blueprint $table) {
            $table->id();
            $table->uuid('store_id');
            $table->foreign('store_id')->references('uuid')->on('stores')->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('referrer')->nullable();
            $table->timestamp('viewed_at');
            $table->timestamps();

            $table->index(['store_id', 'viewed_at']);
            $table->index('user_id');
            $table->index('viewed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_views');
    }
};
