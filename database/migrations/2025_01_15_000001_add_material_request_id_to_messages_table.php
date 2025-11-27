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
            $table->uuid('material_request_id')->nullable()->after('order_id');
            $table->foreign('material_request_id')->references('uuid')->on('material_requests')->onDelete('cascade');
            $table->index('material_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['material_request_id']);
            $table->dropIndex(['material_request_id']);
            $table->dropColumn('material_request_id');
        });
    }
};

