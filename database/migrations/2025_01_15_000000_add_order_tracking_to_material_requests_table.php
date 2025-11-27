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
        Schema::table('material_requests', function (Blueprint $table) {
            $table->uuid('order_id')->nullable()->after('project_location_id');
            $table->foreign('order_id')->references('uuid')->on('orders')->onDelete('set null');
            $table->string('tracking_number')->nullable()->after('order_id');
            $table->enum('delivery_status', ['pending', 'preparing', 'ready', 'in_transit', 'delivered', 'cancelled'])->nullable()->after('tracking_number');
            $table->timestamp('preparing_at')->nullable()->after('delivery_status');
            $table->timestamp('ready_at')->nullable()->after('preparing_at');
            $table->timestamp('in_transit_at')->nullable()->after('ready_at');
            $table->timestamp('delivered_at')->nullable()->after('in_transit_at');
            $table->text('delivery_notes')->nullable()->after('delivered_at');
            $table->string('request_group_id')->nullable()->after('delivery_notes'); // For grouping multiple store requests
            $table->index('request_group_id');
            $table->index('delivery_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_requests', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropIndex(['request_group_id']);
            $table->dropIndex(['delivery_status']);
            $table->dropColumn([
                'order_id',
                'tracking_number',
                'delivery_status',
                'preparing_at',
                'ready_at',
                'in_transit_at',
                'delivered_at',
                'delivery_notes',
                'request_group_id',
            ]);
        });
    }
};

