<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hoat_dong_chi_tiet', function (Blueprint $table): void {
            if (!Schema::hasColumn('hoat_dong_chi_tiet', 'ma_tour')) {
                $table->string('ma_tour', 10)->nullable()->after('ma_dia_diem');
            }

            if (!Schema::hasColumn('hoat_dong_chi_tiet', 'ma_thoi_gian_tour')) {
                $table->string('ma_thoi_gian_tour', 10)->nullable()->after('ma_tour');
            }

            if (!Schema::hasColumn('hoat_dong_chi_tiet', 'ghi_chu')) {
                $table->text('ghi_chu')->nullable()->after('ma_thoi_gian_tour');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hoat_dong_chi_tiet', function (Blueprint $table): void {
            if (Schema::hasColumn('hoat_dong_chi_tiet', 'ghi_chu')) {
                $table->dropColumn('ghi_chu');
            }

            if (Schema::hasColumn('hoat_dong_chi_tiet', 'ma_thoi_gian_tour')) {
                $table->dropColumn('ma_thoi_gian_tour');
            }

            if (Schema::hasColumn('hoat_dong_chi_tiet', 'ma_tour')) {
                $table->dropColumn('ma_tour');
            }
        });
    }
};
