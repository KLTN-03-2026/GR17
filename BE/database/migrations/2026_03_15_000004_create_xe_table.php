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
        Schema::create('xe', function (Blueprint $table) {
            $table->string('ma_xe', 10)->primary();
            $table->string('ten_xe', 100);
            $table->string('loai_xe', 50);
            $table->unsignedInteger('so_cho')->default(1);
            $table->decimal('gia_theo_ngay', 12, 2);
            $table->unsignedInteger('tong_so_xe')->default(0);
            $table->text('mo_ta')->nullable();
            $table->tinyInteger('trang_thai')->default(1)->comment('1: hoạt động, 0: ngừng hoạt động');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('xe');
    }
};
