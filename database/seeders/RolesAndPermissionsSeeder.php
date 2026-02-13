<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $modules = [
            'Admins' => ['view_admins', 'add_admins', 'edit_admins', 'delete_admins', 'update_admins_status'],
            'Vendors' => ['view_vendors', 'add_vendors', 'edit_vendors', 'delete_vendors', 'update_vendor_details', 'update_vendor_commission', 'update_vendors_status'],
            'Products' => ['view_products', 'add_products', 'edit_products', 'delete_products', 'update_product_status'],
            'Catalogue' => [
                'view_sections',
                'add_sections',
                'edit_sections',
                'delete_sections',
                'view_categories',
                'add_categories',
                'edit_categories',
                'delete_categories',
                'view_publishers',
                'add_publishers',
                'edit_publishers',
                'delete_publishers',
                'view_authors',
                'add_authors',
                'edit_authors',
                'delete_authors',
                'view_subjects',
                'add_subjects',
                'edit_subjects',
                'delete_subjects',
                'view_languages',
                'add_languages',
                'edit_languages',
                'delete_languages',
                'view_editions',
                'add_editions',
                'edit_editions',
                'delete_editions',
                'view_coupons',
                'add_coupons',
                'edit_coupons',
                'delete_coupons',
                'view_requested_books',
                'view_sell_old_books'
            ],
            'Orders' => ['view_orders', 'edit_orders', 'delete_orders', 'update_order_status', 'update_order_item_status', 'view_sales_concept'],
            'Ratings' => ['view_ratings', 'update_ratings_status', 'delete_ratings'],
            'Users' => ['view_users', 'update_users_status', 'view_subscribers', 'view_contact_queries'],
            'Institutions' => ['view_institutions', 'add_institutions', 'edit_institutions', 'delete_institutions', 'view_blocks'],
            'Students' => ['view_students', 'add_students', 'edit_students', 'delete_students'],
            'Withdrawals' => ['view_withdrawals', 'update_withdrawals_status'],
            'Banners' => ['view_banners', 'add_banners', 'edit_banners', 'delete_banners'],
            'Shipping' => ['view_shipping', 'edit_shipping'],
            'OTP' => ['view_otp'],
            'Sales' => ['view_sales', 'add_sales', 'edit_sales', 'delete_sales', 'sales_dashboard', 'update_sales_status'],
            'Roles' => ['view_roles', 'add_roles', 'edit_roles', 'delete_roles'],
            'Reports' => ['view_reports', 'export_reports'],
            'Settings' => ['view_settings', 'update_settings'],
        ];

        foreach ($modules as $module => $perms) {
            foreach ($perms as $perm) {
                Permission::updateOrCreate(
                    ['name' => $perm, 'guard_name' => 'web'],
                    ['module' => $module]
                );
            }
        }

        // Create Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'vendor', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'sales', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        // Give permissions to user
        $userRole->syncPermissions(['view_orders', 'view_requested_books', 'view_sell_old_books']);

        // Give all permissions to admin
        $allPermissions = Permission::all();
        $adminRole->syncPermissions($allPermissions);
    }
}
