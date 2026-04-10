<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $tables = ['doi_tac', 'tours', 'dia_diem', 'chi_tiet_tours'];

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            DB::statement(
                sprintf(
                    'ALTER TABLE `%s` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
                    $table
                )
            );
        }
    }

    public function down(): void
    {
        // Không rollback collation để tránh rủi ro mất dữ liệu tiếng Việt.
    }
};
