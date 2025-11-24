<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $buyer = Role::firstOrCreate(['name' => 'buyer']);
        $seller = Role::firstOrCreate(['name' => 'seller']);
        $admin = Role::firstOrCreate(['name' => 'admin']);

        // Create permissions (optional, for fine-grained control)
        $permissions = [
            'view products',
            'purchase products',
            'view services',
            'request services',
            'create products',
            'edit own products',
            'delete own products',
            'create services',
            'edit own services',
            'delete own services',
            'manage all products',
            'manage all services',
            'manage users',
            'manage orders',
            'manage transactions',
            'manage withdrawals',
            'manage coupons',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $buyer->givePermissionTo(['view products', 'purchase products', 'view services', 'request services']);
        $seller->givePermissionTo([
            'view products',
            'purchase products',
            'view services',
            'request services',
            'create products',
            'edit own products',
            'delete own products',
            'create services',
            'edit own services',
            'delete own services',
        ]);
        $admin->givePermissionTo(Permission::all());
    }
}
