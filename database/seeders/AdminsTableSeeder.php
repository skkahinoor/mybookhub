<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // My code: (Check DatabaseSeeder.php page too)
        // Database Seeding    // https://laravel.com/docs/9.x/seeding
        // Note: Check DatabaseSeeder.php
        $adminRecords = [
            [
                'id'        => 1,
                'name'      => 'Ahmed Yahya',
                'type'      => 'admin',
                'phone'    => '9800000000',
                'email'     => 'admin@admin.com',
                'password'  => '$2a$12$xvkjSScUPRexfcJTAy9ATutIeGUuRgJrjDIdL/.xlrddEvRZINpeC',
                'image'     => '',
                'status'    => 1,
            ],


            [
                'id'        => 2,
                'name'      => 'John Singh - Vendor',
                'type'      => 'vendor',
                'phone'    => '9700000000',
                'email'     => 'john@admin.com',
                'password'  => '$2a$12$xvkjSScUPRexfcJTAy9ATutIeGUuRgJrjDIdL/.xlrddEvRZINpeC',
                'image'     => '',
                'status'    => 1,
            ],
        ];
        // Note: Check DatabaseSeeder.php
        foreach ($adminRecords as $record) {
            $user = \App\Models\User::updateOrCreate(
                ['email' => $record['email']],
                [
                    'name'     => $record['name'],
                    'phone'   => $record['phone'],
                    'password' => $record['password'],
                    'status'   => $record['status'],
                    'profile_image' => $record['image'],
                ]
            );

            $role = \Spatie\Permission\Models\Role::where('name', $record['type'])->first();
            if ($role) {
                $user->assignRole($role);
                $user->update(['role_id' => $role->id]);
            }
        }
    }
}
