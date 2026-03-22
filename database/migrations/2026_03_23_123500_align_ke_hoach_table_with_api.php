<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ke_hoach')) {
            return;
        }

        Schema::table('ke_hoach', function (Blueprint $table) {
            if (!Schema::hasColumn('ke_hoach', 'ma_nhom')) {
                $table->string('ma_nhom', 10)->nullable()->after('ma_ke_hoach');
            }

            if (!Schema::hasColumn('ke_hoach', 'so_nguoi')) {
                $table->integer('so_nguoi')->default(1)->after('mo_ta');
            }

            if (!Schema::hasColumn('ke_hoach', 'ngan_sach_du_kien')) {
                $table->decimal('ngan_sach_du_kien', 15, 2)->default(0)->after('ngay_ket_thuc');
            }
        });

        if (Schema::hasColumn('ke_hoach', 'tong_chi_phi') && Schema::hasColumn('ke_hoach', 'ngan_sach_du_kien')) {
            DB::statement('UPDATE ke_hoach SET ngan_sach_du_kien = COALESCE(ngan_sach_du_kien, tong_chi_phi)');
            DB::statement('UPDATE ke_hoach SET ngan_sach_du_kien = tong_chi_phi WHERE ngan_sach_du_kien = 0 AND tong_chi_phi IS NOT NULL');
        }

        if (Schema::hasColumn('ke_hoach', 'ma_nhom')) {
            DB::statement("
                UPDATE ke_hoach
                SET ma_nhom = CASE ma_ke_hoach
                    WHEN '001' THEN '001'
                    WHEN '002' THEN '002'
                    WHEN '003' THEN '003'
                    WHEN '004' THEN '004'
                    WHEN '005' THEN '005'
                    ELSE ma_nhom
                END
                WHERE ma_nhom IS NULL OR ma_nhom = ''
            ");
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('ke_hoach')) {
            return;
        }

        Schema::table('ke_hoach', function (Blueprint $table) {
            if (Schema::hasColumn('ke_hoach', 'ma_nhom')) {
                $table->dropColumn('ma_nhom');
            }

            if (Schema::hasColumn('ke_hoach', 'so_nguoi')) {
                $table->dropColumn('so_nguoi');
            }

            if (Schema::hasColumn('ke_hoach', 'ngan_sach_du_kien')) {
                $table->dropColumn('ngan_sach_du_kien');
            }
        });
    }
};
