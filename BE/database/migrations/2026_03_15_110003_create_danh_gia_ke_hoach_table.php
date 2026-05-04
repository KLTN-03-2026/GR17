<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('danh_gia');
        Schema::dropIfExists('danh_gia_ke_hoach');
        Schema::enableForeignKeyConstraints();
        Schema::create('danh_gia_ke_hoach', function (Blueprint $table) {
            $table->string('Ma_danh_gia', 20)->primary();
            $table->string('Ma_khach_hang', 10);
            $table->string('ma_dia_diem');
            $table->tinyInteger('so_sao')->comment('Số sao: 1-5');
            $table->text('noi_dung')->nullable();
            $table->timestamps();
            $table->unique(['Ma_khach_hang', 'ma_dia_diem'], 'danh_gia_ke_hoach_customer_place_unique');

            $table->foreign('Ma_khach_hang')
                  ->references('Ma_khach_hang')->on('khach_hang')
                  ->onDelete('cascade');

            $table->foreign('ma_dia_diem')
                  ->references('ma_dia_diem')->on('dia_diem')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('danh_gia_ke_hoach');
    }
};
