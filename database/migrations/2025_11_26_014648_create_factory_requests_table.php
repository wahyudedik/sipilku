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
        Schema::create('factory_requests', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('request_group_id')->nullable(); // Group multiple requests for comparison
            $table->uuid('factory_id');
            $table->foreign('factory_id')->references('uuid')->on('factories')->onDelete('cascade');
            $table->uuid('factory_type_id')->nullable();
            $table->foreign('factory_type_id')->references('uuid')->on('factory_types')->onDelete('set null');
            $table->uuid('project_location_id')->nullable();
            $table->foreign('project_location_id')->references('uuid')->on('project_locations')->onDelete('set null');
            $table->json('items'); // Array of requested items
            $table->text('message')->nullable();
            $table->decimal('budget', 15, 2)->nullable();
            $table->date('deadline')->nullable();
            $table->enum('status', ['pending', 'quoted', 'accepted', 'rejected', 'cancelled'])->default('pending');
            $table->uuid('order_id')->nullable();
            $table->string('tracking_number')->nullable();
            $table->enum('delivery_status', ['pending', 'preparing', 'ready', 'in_transit', 'delivered', 'cancelled'])->nullable();
            $table->timestamp('preparing_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('in_transit_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->decimal('quoted_price', 15, 2)->nullable();
            $table->decimal('delivery_cost', 15, 2)->nullable();
            $table->json('additional_fees')->nullable(); // Additional fees (handling, etc.)
            $table->decimal('total_cost', 15, 2)->nullable();
            $table->text('quote_message')->nullable();
            $table->timestamp('quoted_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['factory_id', 'status']);
            $table->index('factory_type_id');
            $table->index('request_group_id');
            $table->index(['order_id', 'delivery_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factory_requests');
    }
};
