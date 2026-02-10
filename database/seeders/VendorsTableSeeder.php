<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VendorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Find existing vendor user (created in AdminsTableSeeder)
        $user = \App\Models\User::where('email', 'john@admin.com')->first();

        if ($user) {
            $vendorRecords = [
                [
                    'id'      => 1,
                    'user_id' => $user->id,
                    'status'  => 1,
                    'confirm' => 'Yes',
                ],
            ];

            foreach ($vendorRecords as $record) {
                \App\Models\Vendor::updateOrCreate(
                    ['id' => $record['id']],
                    $record
                );
            }
        }
    }
}
