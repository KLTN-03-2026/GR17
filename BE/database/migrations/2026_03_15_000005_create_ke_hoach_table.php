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
        Schema::create('ke_hoach', function (Blueprint $table) {
            $table->string('ma_ke_hoach', 20)->primary();
            $table->string('ten_ke_hoach', 150);
            $table->text('mo_ta')->nullable();
            $table->date('ngay_bat_dau');
            $table->date('ngay_ket_thuc');
            $table->decimal('tong_chi_phi', 15, 2)->default(0);
            $table->tinyInteger('trang_thai')->default(1)->comment('1: chưa bắt đầu, 2: đang diễn ra, 3: đã hoàn thành');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ke_hoach');
    }
};
