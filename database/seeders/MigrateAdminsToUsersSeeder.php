<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use App\Models\Vendor;
use App\Models\SalesExecutive;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MigrateAdminsToUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Migrate ADMINS (SuperAdmin, Admin, SubAdmin, Vendor)
        $admins = Admin::all();

        foreach ($admins as $admin) {
            // Check if user already exists (by email)
            $existingUser = User::where('email', $admin->email)->first();

            if ($existingUser) {
                $this->command->info("User {$admin->email} already exists. Skipping creation.");
                // Ensure link if vendor
                if ($admin->type === 'vendor' && $admin->vendor_id) {
                    Vendor::where('id', $admin->vendor_id)->update(['user_id' => $existingUser->id]);
                    $existingUser->assignRole('vendor');
                    $existingUser->role_id = 2; // Legacy
                    $existingUser->save();
                    $this->command->info("Linked existing user to vendor {$admin->vendor_id}.");
                } elseif (in_array($admin->type, ['superadmin', 'admin', 'subadmin'])) {
                    $existingUser->assignRole('admin');
                    $existingUser->role_id = 1; // Legacy
                    $existingUser->save();
                }
                continue;
            }

            // Create User
            $roleId = 4; // Default User
            $roleName = 'user';

            if ($admin->type === 'vendor') {
                $roleId = 2;
                $roleName = 'vendor';
            } elseif (in_array($admin->type, ['superadmin', 'admin', 'subadmin'])) {
                $roleId = 1;
                $roleName = 'admin';
            }

            $newUser = User::create([
                'name' => $admin->name,
                'email' => $admin->email,
                'password' => $admin->password, // Password is already hashed in admins table
                'phone' => $admin->mobile,
                'role_id' => $roleId,
                'status' => $admin->status,
                'profile_image' => $admin->image,
            ]);

            // Assign Spatie Role
            $newUser->assignRole($roleName);

            // Link to Vendor Profile if Vendor
            if ($admin->type === 'vendor' && $admin->vendor_id) {
                Vendor::where('id', $admin->vendor_id)->update(['user_id' => $newUser->id]);
                $this->command->info("Migrated Vendor: {$admin->email} and linked to Vendor ID {$admin->vendor_id}");
            } else {
                $this->command->info("Migrated Admin: {$admin->email}");
            }
        }

        // 2. Migrate SALES EXECUTIVES
        $salesExecutives = SalesExecutive::all();
        foreach ($salesExecutives as $sales) {
            $existingUser = User::where('email', $sales->email)->first();
             if ($existingUser) {
                 SalesExecutive::where('id', $sales->id)->update(['user_id' => $existingUser->id]);
                 $existingUser->assignRole('sales');
                 $existingUser->role_id = 3;
                 $existingUser->save();
                 continue;
             }

             $newUser = User::create([
                'name' => $sales->name,
                'email' => $sales->email,
                'password' => $sales->password,
                'phone' => $sales->phone,
                'role_id' => 3, // Sales
                'status' => $sales->status,
            ]);
            $newUser->assignRole('sales');
            
            // Link
            SalesExecutive::where('id', $sales->id)->update(['user_id' => $newUser->id]);
            $this->command->info("Migrated Sales Executive: {$sales->email}");
        }
    }
}
