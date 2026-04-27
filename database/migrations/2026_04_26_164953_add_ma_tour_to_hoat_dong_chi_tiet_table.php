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
        Schema::table('hoat_dong_chi_tiet', function (Blueprint $table) {
            if (!Schema::hasColumn('hoat_dong_chi_tiet', 'ma_tour')) {
                $table->string('ma_tour', 10)->nullable()->after('ma_dia_diem');
                $table->foreign('ma_tour')->references('ma_tour')->on('tours')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hoat_dong_chi_tiet', function (Blueprint $table) {
            if (Schema::hasColumn('hoat_dong_chi_tiet', 'ma_tour')) {
                $table->dropForeign(['ma_tour']);
                $table->dropColumn('ma_tour');
            }
        });
    }
};
