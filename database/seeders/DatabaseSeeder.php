<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles and permissions
        $this->call(RoleSeeder::class);

        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@sipilku.com',
            'is_seller' => false,
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // Create seller user
        $seller = User::factory()->create([
            'name' => 'Seller',
            'email' => 'seller@sipilku.com',
            'is_seller' => true,
            'is_active' => true, 
        ]);
        $seller->assignRole('seller');

        // Create buyer user
        $buyer = User::factory()->create([
            'name' => 'Buyer',
            'email' => 'buyer@sipilku.com',
            'is_seller' => false,
            'is_active' => true,
        ]);
        $buyer->assignRole('buyer');
    }
}
