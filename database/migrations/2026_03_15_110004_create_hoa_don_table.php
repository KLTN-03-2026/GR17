<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('hoa_don');
        Schema::enableForeignKeyConstraints();
        Schema::create('hoa_don', function (Blueprint $table) {
            $table->string('Ma_hoa_don', 20)->primary();
            $table->string('Ma_khach_hang', 10);
            $table->string('Ma_nhom', 20)->nullable();
            $table->string('ma_dia_diem');
            $table->decimal('tong_tien', 18, 2)->default(0);
            $table->tinyInteger('trang_thai')->default(0)
                  ->comment('0: chờ xử lý, 1: đã xác nhận, 2: đã hủy');
            $table->timestamp('ngay_dat')->useCurrent();
            $table->timestamps();

            $table->foreign('Ma_khach_hang')
                  ->references('Ma_khach_hang')->on('khach_hang')
                  ->onDelete('cascade');

            $table->foreign('Ma_nhom')
                  ->references('Ma_nhom')->on('nhom')
                  ->onDelete('set null');

            $table->foreign('ma_dia_diem')
                  ->references('ma_dia_diem')->on('dia_diem')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hoa_don');
    }
};
