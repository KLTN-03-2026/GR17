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
        Schema::create('danh_sach_yeu_thich', function (Blueprint $table) {
            $table->string('ma_danh_sach_ua_thich', 10)->primary();
            $table->string('ma_khach_hang');
            $table->string('ma_dia_diem');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danh_sach_yeu_thich');
    }
};
