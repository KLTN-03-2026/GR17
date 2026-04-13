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
            $table->string('ma_tour', 10)->nullable()->after('ma_dia_diem');
            $table->text('ghi_chu')->nullable()->after('ma_tour');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hoat_dong_chi_tiet', function (Blueprint $table) {
            $table->dropColumn(['ma_tour', 'ghi_chu']);
        });
    }
};
