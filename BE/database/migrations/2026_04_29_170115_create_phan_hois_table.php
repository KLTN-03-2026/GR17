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
        Schema::create('phan_hois', function (Blueprint $table) {
            $table->id();
            $table->string('ma_khach_hang')->nullable();
            $table->foreign('ma_khach_hang')->references('Ma_khach_hang')->on('khach_hang')->onDelete('set null');
            $table->string('tieu_de');
            $table->string('loai'); // Lỗi, Góp ý, Khác
            $table->text('mo_ta');
            $table->string('url_trang_loi')->nullable();
            $table->string('trang_thai')->default('Mới'); // Mới, Đang xử lý, Đã giải quyết
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phan_hois');
    }
};
