<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cau_hinh_ngay', function (Blueprint $table) {
            $table->string('ma_cau_hinh_ngay', 10)->primary();
            $table->tinyInteger('loai_ngay_le')->comment('1: le lon, 2: le bth, 3: cuoi tuan');
            $table->string('ten_ngay_le')->nullable();
            $table->date('ngay');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cau_hinh_ngay');
    }
};
