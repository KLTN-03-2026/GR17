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
        Schema::create('tour_khoi_hanhs', function (Blueprint $table) {
            $table->string('ma_thoi_gian_tour', 10)->primary();
            $table->string('ma_tour', 10);
            $table->date('ngay_bat_dau')->nullable();
            $table->date('ngay_ket_thuc')->nullable();
            $table->integer('so_cho')->nullable();
            $table->boolean('tinh_trang')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_khoi_hanhs');
    }
};
