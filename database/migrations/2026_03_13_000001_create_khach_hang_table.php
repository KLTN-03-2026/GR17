<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('khach_hang', function (Blueprint $table) {
            $table->string('Ma_khach_hang', 10)->primary();
            $table->string('Ho_va_ten', 40);
            $table->string('Mat_khau');
            $table->string('Email')->unique();
            $table->date('Ngay_sinh');
            $table->boolean('Gioi_tinh');
            $table->string('so_dien_thoai', 10)->unique();
            $table->boolean('is_block')->default(1);
            $table->string('hash_reset')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('khach_hang');
    }
};
