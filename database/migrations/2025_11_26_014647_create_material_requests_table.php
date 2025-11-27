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
        Schema::create('material_requests', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->uuid('store_id');
            $table->foreign('store_id')->references('uuid')->on('stores')->onDelete('cascade');
            $table->uuid('project_location_id')->nullable();
            $table->foreign('project_location_id')->references('uuid')->on('project_locations')->onDelete('set null');
            $table->json('items'); // Array of requested items
            $table->text('message')->nullable();
            $table->decimal('budget', 15, 2)->nullable();
            $table->date('deadline')->nullable();
            $table->enum('status', ['pending', 'quoted', 'accepted', 'rejected', 'cancelled'])->default('pending');
            $table->decimal('quoted_price', 15, 2)->nullable();
            $table->text('quote_message')->nullable();
            $table->timestamp('quoted_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['store_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_requests');
    }
};
