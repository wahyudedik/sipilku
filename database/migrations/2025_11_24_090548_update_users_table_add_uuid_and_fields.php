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
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
            $table->string('phone')->nullable()->after('email');
            $table->string('avatar')->nullable()->after('phone');
            $table->decimal('balance', 15, 2)->default(0)->after('avatar');
            $table->boolean('is_seller')->default(false)->after('balance');
            $table->boolean('is_active')->default(true)->after('is_seller');
        });

        // Generate UUID for existing users
        \DB::table('users')->whereNull('uuid')->get()->each(function ($user) {
            \DB::table('users')
                ->where('id', $user->id)
                ->update(['uuid' => \Illuminate\Support\Str::uuid()]);
        });

        // Note: Keep UUID nullable to allow trait to generate it during model creation
        // The trait will always generate UUID before insert, so it's safe to keep nullable
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'phone', 'avatar', 'balance', 'is_seller', 'is_active']);
        });
    }
};
