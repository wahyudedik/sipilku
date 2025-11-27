<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factory_analytics_aggregates', function (Blueprint $table) {
            $table->id();
            $table->uuid('factory_id');
            $table->foreign('factory_id')->references('uuid')->on('factories')->onDelete('cascade');
            $table->date('date')->index();
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('orders_count')->default(0);
            $table->decimal('revenue', 14, 2)->default(0);
            $table->decimal('avg_rating', 3, 2)->nullable();
            $table->json('product_popularity')->nullable();
            $table->timestamps();

            $table->unique(['factory_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factory_analytics_aggregates');
    }
};
