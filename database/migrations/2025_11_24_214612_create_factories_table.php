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
        Schema::create('factories', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('factory_type_id')->constrained('factory_types')->onDelete('restrict');
            $table->foreignUuid('umkm_id')->nullable()->constrained('umkms')->onDelete('set null');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->enum('category', ['industri', 'umkm'])->default('industri');
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->json('documents')->nullable(); // Izin operasional, NPWP, sertifikat, dll
            $table->string('business_license')->nullable();
            $table->json('certifications')->nullable(); // Sertifikat kualitas, ISO, dll
            $table->integer('rating')->default(0);
            $table->integer('total_reviews')->default(0);
            $table->integer('total_orders')->default(0);
            $table->decimal('delivery_price_per_km', 10, 2)->nullable(); // Harga delivery per km
            $table->integer('max_delivery_distance')->nullable(); // Maksimal jarak delivery (km)
            $table->json('capacity')->nullable(); // Kapasitas produksi per hari/bulan
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'is_active']);
            $table->index(['factory_type_id', 'is_active']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factories');
    }
};
