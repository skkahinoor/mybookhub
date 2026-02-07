<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class SyncUserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Map your existing role_ids to Spatie Role names
        // Ideally, you should fetch this from your `roles` table if the IDs are dynamic.
        // Based on the image you provided:
        // 1 -> admin
        // 2 -> vendor
        // 3 -> sales
        // 4 -> user
        // 5 -> student
        
        $roleMap = [
            1 => 'admin',
            2 => 'vendor',
            3 => 'sales',
            4 => 'user',
            5 => 'student',
        ];

        // Fetch users who have a role_id but might not have the Spatie role assigned yet
        $users = User::whereNotNull('role_id')->get();

        foreach ($users as $user) {
            if (isset($roleMap[$user->role_id])) {
                $roleName = $roleMap[$user->role_id];
                
                // Assign role if not already assigned
                if (!$user->hasRole($roleName)) {
                    $user->assignRole($roleName);
                    $this->command->info("Assigned role {$roleName} to user ID: {$user->id}");
                }
            }
        }
    }
}
