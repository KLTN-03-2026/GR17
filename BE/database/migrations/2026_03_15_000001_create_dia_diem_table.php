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
        Schema::create('dia_diem', function (Blueprint $table) {
            $table->string('ma_dia_diem')->primary();
            $table->string('ten_dia_diem');
            $table->integer('loai'); // 1: địa điểm du lịch, 2: khách sạn, 3: nhà hàng
            $table->string('dia_chi');
            $table->string('sdt')->nullable();
            $table->decimal('kinh_do', 10, 7);
            $table->decimal('vi_do', 10, 7);
            $table->time('gio_mo_cua')->nullable();
            $table->time('gio_dong_cua')->nullable();
            $table->decimal('gia_giao_dong', 15, 2)->nullable();
            $table->string('hinh_anh')->nullable();
            $table->text('mo_ta')->nullable();
            $table->string('thoi_gian_tham_quan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dia_diem');
    }
};
