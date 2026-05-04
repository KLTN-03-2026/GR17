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
        Schema::create('admins', function (Blueprint $table) {
            $table->string('Ma_admin', 10)->primary();
            $table->string('Ho_va_ten', 40);
            $table->string('Mat_khau');
            $table->string('Email')->unique();
            $table->date('Ngay_sinh');
            $table->boolean('Gioi_tinh');
            $table->string('ma_chuc_vu', 10);
            $table->boolean('is_block')->default(1);
            $table->string('hash_reset')->nullable();
            $table->string('so_dien_thoai', 10)->unique();
            $table->timestamps();         // $table->foreign('ma_chuc_vu')->references('ma_chuc_vu')->on('chuc_vu')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
};
