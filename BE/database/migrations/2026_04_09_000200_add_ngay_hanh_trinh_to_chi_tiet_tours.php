<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('chi_tiet_tours')) {
            return;
        }

        if (!Schema::hasColumn('chi_tiet_tours', 'ngay_hanh_trinh')) {
            Schema::table('chi_tiet_tours', function (Blueprint $table): void {
                $table->unsignedInteger('ngay_hanh_trinh')->nullable()->after('ma_dia_diem');
                $table->index(['ma_tour', 'ngay_hanh_trinh', 'thu_tu_hanh_trinh'], 'chi_tiet_tour_day_order_idx');
            });
        }

        DB::table('chi_tiet_tours')
            ->whereNull('ngay_hanh_trinh')
            ->update(['ngay_hanh_trinh' => 1]);
    }

    public function down(): void
    {
        if (!Schema::hasTable('chi_tiet_tours')) {
            return;
        }

        if (!Schema::hasColumn('chi_tiet_tours', 'ngay_hanh_trinh')) {
            return;
        }

        Schema::table('chi_tiet_tours', function (Blueprint $table): void {
            $table->dropIndex('chi_tiet_tour_day_order_idx');
            $table->dropColumn('ngay_hanh_trinh');
        });
    }
};

