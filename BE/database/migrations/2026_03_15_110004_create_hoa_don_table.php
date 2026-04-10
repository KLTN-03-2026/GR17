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
            $table->string('ma_hoa_don', 10)->primary();
            $table->string('ma_nhom', 20);
            $table->tinyInteger('loai_hoa_don')->comment('0: tu tour, 1: tu ke hoach');
            $table->string('ma_doi_tuong')->comment('ma tour hoac ma ke hoach');
            $table->decimal('tong_tien', 18, 2)->default(0);
            $table->tinyInteger('trang_thai_thanh_toan')->default(0)
                  ->comment('0: chua thanh toan, 1: da thanh toan, 2: ke hoach chua duoc admin dong y');
            $table->string('ma_giao_dich')->nullable();
            $table->timestamp('ngay_tao')->useCurrent();
            $table->timestamps();

            $table->foreign('ma_nhom')
                  ->references('Ma_nhom')->on('nhom')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hoa_don');
    }
};
