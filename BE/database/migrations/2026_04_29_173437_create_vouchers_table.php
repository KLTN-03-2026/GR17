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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('ma_voucher')->unique();
            $table->string('ma_doi_tac')->nullable();
            $table->foreign('ma_doi_tac')->references('Ma_doi_tac')->on('doi_tac')->onDelete('cascade');
            $table->string('ten_voucher');
            $table->enum('loai_giam_gia', ['percent', 'fixed']);
            $table->decimal('gia_tri_giam', 15, 2);
            $table->decimal('giam_toi_da', 15, 2)->nullable();
            $table->decimal('don_toi_thieu', 15, 2)->default(0);
            $table->integer('so_luong')->default(0);
            $table->integer('da_su_dung')->default(0);
            $table->dateTime('ngay_bat_dau');
            $table->dateTime('ngay_ket_thuc');
            $table->boolean('trang_thai')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
