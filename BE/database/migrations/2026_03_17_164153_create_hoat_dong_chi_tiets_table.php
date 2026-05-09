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
        Schema::create('hoat_dong_chi_tiet', function (Blueprint $table) {
            $table->string('ma_hoat_dong_chi_tiet', 10)->primary();
            $table->string('ma_ke_hoach', 10);
            $table->string('ma_nhom', 20);
            $table->string('ma_dia_diem');
            $table->string('ma_tour', 10)->nullable();
            $table->string('ma_thoi_gian_tour', 10)->nullable();
            $table->text('ghi_chu')->nullable();
            $table->time('gio_bat_dau');
            $table->time('gio_ket_thuc');
            $table->date('ngay_cu_the');
            $table->timestamps();

            $table->foreign('ma_ke_hoach')->references('ma_ke_hoach')->on('ke_hoach')->onDelete('cascade');
            $table->foreign('ma_nhom')->references('Ma_nhom')->on('nhom')->onDelete('cascade');
            $table->foreign('ma_dia_diem')->references('ma_dia_diem')->on('dia_diem')->onDelete('cascade');
            $table->foreign('ma_tour')->references('ma_tour')->on('tours')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoat_dong_chi_tiet');
    }
};
