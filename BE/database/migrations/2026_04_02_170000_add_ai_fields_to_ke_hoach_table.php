<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ke_hoach')) {
            return;
        }

        Schema::table('ke_hoach', function (Blueprint $table) {
            if (! Schema::hasColumn('ke_hoach', 'nguon_tao')) {
                $table->string('nguon_tao', 20)->default('manual')->after('trang_thai');
            }

            if (! Schema::hasColumn('ke_hoach', 'du_lieu_ai')) {
                $table->longText('du_lieu_ai')->nullable()->after('nguon_tao');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('ke_hoach')) {
            return;
        }

        Schema::table('ke_hoach', function (Blueprint $table) {
            if (Schema::hasColumn('ke_hoach', 'du_lieu_ai')) {
                $table->dropColumn('du_lieu_ai');
            }

            if (Schema::hasColumn('ke_hoach', 'nguon_tao')) {
                $table->dropColumn('nguon_tao');
            }
        });
    }
};
