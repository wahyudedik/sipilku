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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('download_token')->nullable()->unique()->after('completed_at');
            $table->timestamp('download_expires_at')->nullable()->after('download_token');
            $table->integer('download_count')->default(0)->after('download_expires_at');
            $table->integer('max_downloads')->default(5)->after('download_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['download_token', 'download_expires_at', 'download_count', 'max_downloads']);
        });
    }
};
