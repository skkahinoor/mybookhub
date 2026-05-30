<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Seeds all missing permissions that protect the admin sidebar menu items.
 * Run with: php artisan db:seed --class=StaffPermissionsSeeder
 */
class StaffPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // New permissions grouped by sidebar module
        $modules = [

            // Settings menu items that had no @can protection
            'Settings' => [
                'manage_settings',           // Logo, Favicon, Coming Soon
                'manage_push_notifications', // Push Notifications
            ],

            // Admin Management sub-items
            'Admins' => [
                'manage_staff',           // Staff management page
                'manage_admin_password',  // Update Admin Password
                'manage_admin_details',   // Update Admin Details
            ],

            // Vendor Management sub-item
            'Vendors' => [
                'manage_vendor_plans',    // Vendor Plan Settings
            ],

            // Delivery Management (entire section was unprotected)
            'Delivery' => [
                'view_delivery_agents',          // Delivery Agents list
                'view_delivery_agent_payouts',   // Delivery Partner Payouts
                'view_delivery_agent_queries',   // Delivery Agent Queries
            ],

            // Old Book Management (entire section was unprotected)
            'Old Books' => [
                'view_old_book_conditions',  // Old Book Condition
                'view_sell_book_requests',   // Sell Old Book Request
                'view_old_book_commissions', // Old Book Commission
                'view_old_book_payouts',     // Old Book Payout
            ],

            // Location Management (State & Country, District were unprotected)
            'Locations' => [
                'view_locations',   // State & Country, District
            ],

            // Order Management sub-items that had no @can
            'Orders' => [
                'view_order_queries',      // Order Queries
                'manage_movs',             // MOV Changes
                'manage_shipping_charges', // Shipping Charges
            ],

            // Other Managements sub-items that had no @can
            'Others' => [
                'manage_dynamic_modals',     // Dynamic Modals
                'manage_commission_settings',// Commission Settings
            ],

            // Catalogue
            'Catalogue' => [
                'view_types', // Book Types (was missing)
            ],
        ];

        foreach ($modules as $module => $perms) {
            foreach ($perms as $perm) {
                Permission::updateOrCreate(
                    ['name' => $perm, 'guard_name' => 'web'],
                    ['module' => $module]
                );
            }
        }

        // Give ALL new permissions to the admin role
        $adminRole = Role::where('name', 'admin')->where('guard_name', 'web')->first();
        if ($adminRole) {
            $allPermissions = Permission::all();
            $adminRole->syncPermissions($allPermissions);
            $this->command->info('Admin role synced with all ' . $allPermissions->count() . ' permissions.');
        }

        $this->command->info('Staff permissions seeded successfully!');
    }
}
