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
        Schema::table('messages', function (Blueprint $table) {
            // Add after order_id if material_request_id doesn't exist
            if (Schema::hasColumn('messages', 'material_request_id')) {
                $table->uuid('factory_request_id')->nullable()->after('material_request_id');
            } else {
                $table->uuid('factory_request_id')->nullable()->after('order_id');
            }
            $table->foreign('factory_request_id')->references('uuid')->on('factory_requests')->onDelete('cascade');
            $table->index('factory_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['factory_request_id']);
            $table->dropIndex(['factory_request_id']);
            $table->dropColumn('factory_request_id');
        });
    }
};
