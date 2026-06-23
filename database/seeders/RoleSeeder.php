<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // POS
            'view pos',
            'create orders',
            'process payments',
            'cancel orders',
            'view order history',

            // Catalog
            'view categories',
            'manage categories',
            'view products',
            'manage products',

            // Inventory
            'view inventory',
            'manage inventory',
            'adjust stock',

            // Reports
            'view reports',
            'export reports',

            // Settings
            'view settings',
            'manage settings',

            // Users
            'view users',
            'manage users',
            'manage roles',

            // Branches
            'view branches',
            'manage branches',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions($permissions);

        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $manager->syncPermissions([
            'view pos',
            'create orders',
            'process payments',
            'cancel orders',
            'view order history',
            'view categories',
            'manage categories',
            'view products',
            'manage products',
            'view inventory',
            'manage inventory',
            'adjust stock',
            'view reports',
            'export reports',
            'view settings',
            'view users',
            'view branches',
        ]);

        $cashier = Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
        $cashier->syncPermissions([
            'view pos',
            'create orders',
            'process payments',
            'view order history',
            'view categories',
            'view products',
        ]);

        // Create default admin user if not exists
        if (! User::where('email', 'admin@poscafe.com')->exists()) {
            $adminUser = User::create([
                'name' => 'Admin',
                'email' => 'admin@poscafe.com',
                'password' => bcrypt('password'),
            ]);
            $adminUser->assignRole('admin');
        }
    }
}
