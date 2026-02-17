<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderItemStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Note: Check DatabaseSeeder.php!
        $orderItemStatusRecords = [
            [
                'id'     => 1,
                'name'   => 'Out of Stock',
                'status' => 1
            ],
            [
                'id'     => 2,
                'name'   => 'Available',
                'status' => 1
            ],
        ];

       
    // Delete records not in this list
    \App\Models\OrderItemStatus::whereNotIn(
        'id',
        collect($orderItemStatusRecords)->pluck('id')
    )->delete();

    // Update or insert the ones we want
    foreach ($orderItemStatusRecords as $record) {
        \App\Models\OrderItemStatus::updateOrInsert(
            ['id' => $record['id']],
            $record
        );
    }
    }
}
