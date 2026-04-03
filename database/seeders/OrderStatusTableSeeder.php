<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Note: Check DatabaseSeeder.php!
        $orderStatusRecords = [
            ['id' => 1, 'name' => 'New', 'status' => 1],
            ['id' => 2, 'name' => 'Pending', 'status' => 1],
            ['id' => 3, 'name' => 'Canceled', 'status' => 1],
            ['id' => 4, 'name' => 'In Progress', 'status' => 1],
            ['id' => 5, 'name' => 'Shipped', 'status' => 1],
            ['id' => 6, 'name' => 'Partially Shipped', 'status' => 1],
            ['id' => 7, 'name' => 'Delivered', 'status' => 1],
            ['id' => 8, 'name' => 'Partially Delivered', 'status' => 1],
            ['id' => 9, 'name' => 'Paid', 'status' => 1],
            ['id' => 10, 'name' => 'Return Requested', 'status' => 1],
            ['id' => 11, 'name' => 'Return Approved', 'status' => 1],
            ['id' => 12, 'name' => 'Return Rejected', 'status' => 1],
            ['id' => 13, 'name' => 'Returned', 'status' => 1],
        ];

        // Update or insert the ones we want so it's safe to run multiple times
        foreach ($orderStatusRecords as $record) {
            \App\Models\OrderStatus::updateOrInsert(
                ['id' => $record['id']],
                $record
            );
        }
    }
}
