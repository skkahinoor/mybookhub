<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop index if already exists (safe production approach)
        $this->dropIndexIfExists('vendors', 'idx_vendors_user_id');

        Schema::table('vendors', function (Blueprint $table) {
            $table->index('user_id', 'idx_vendors_user_id');
        });
    }

    public function down(): void
    {
        $this->dropIndexIfExists('vendors', 'idx_vendors_user_id');
    }

    private function dropIndexIfExists($table, $index)
    {
        $exists = DB::select("
            SELECT COUNT(1) as count
            FROM information_schema.statistics
            WHERE table_schema = DATABASE()
            AND table_name = ?
            AND index_name = ?
        ", [$table, $index]);

        if ($exists[0]->count > 0) {
            DB::statement("ALTER TABLE `$table` DROP INDEX `$index`");
        }
    }
};
