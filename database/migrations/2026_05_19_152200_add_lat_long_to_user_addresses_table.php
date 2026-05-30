<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('location');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });

        try {
            // Populate existing records from 'location' string
            $addresses = DB::table('user_addresses')->whereNotNull('location')->get();
            foreach ($addresses as $addr) {
                if (!empty($addr->location)) {
                    $parts = explode(',', $addr->location);
                    if (count($parts) === 2) {
                        DB::table('user_addresses')->where('id', $addr->id)->update([
                            'latitude' => (float) trim($parts[0]),
                            'longitude' => (float) trim($parts[1]),
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Silence any errors during migration data seeding
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};

