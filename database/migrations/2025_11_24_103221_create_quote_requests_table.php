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
        Schema::create('quote_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Buyer
            $table->text('message');
            $table->json('requirements')->nullable(); // Custom requirements
            $table->decimal('budget', 15, 2)->nullable();
            $table->date('deadline')->nullable();
            $table->enum('status', ['pending', 'quoted', 'accepted', 'rejected', 'cancelled'])->default('pending');
            $table->decimal('quoted_price', 15, 2)->nullable();
            $table->text('quote_message')->nullable();
            $table->timestamp('quoted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_requests');
    }
};
