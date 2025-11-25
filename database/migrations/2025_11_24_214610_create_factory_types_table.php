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
        Schema::create('factory_types', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('name'); // Beton, Bata, Genting, Baja, Precast, Keramik, Kayu, dll
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            $table->json('default_units')->nullable(); // ['m3', 'm2', 'kg', 'pcs'] - unit default untuk tipe ini
            $table->json('specifications_template')->nullable(); // Template spesifikasi untuk tipe ini
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factory_types');
    }
};
