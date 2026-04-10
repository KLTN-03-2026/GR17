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
        Schema::create('doi_soat_hoa_hong', function (Blueprint $table) {
            $table->id('ma_doi_soat');
            $table->string('ma_hoa_don', 10);
            $table->string('ma_doi_tac', 20); // The partner who owns the service/tour
            $table->string('loai_giao_dich')->comment('dich_vu or tour'); // e.g. dich_vu or tour
            $table->decimal('tong_tien_giao_dich', 15, 2);
            $table->decimal('phan_tram_hoa_hong', 5, 2)->default(10.00); // Admin takes 10%
            $table->decimal('tien_hoa_hong_admin', 15, 2);
            $table->decimal('tien_doi_tac_thuc_nhan', 15, 2);
            $table->string('trang_thai_thanh_toan', 50)->default('chua_doi_soat')->comment('chua_doi_soat, da_chuyen_khoan');
            $table->text('mo_ta')->nullable();
            $table->timestamps();

            $table->foreign('ma_hoa_don')->references('ma_hoa_don')->on('hoa_don')->onDelete('cascade');
            $table->foreign('ma_doi_tac')->references('ma_doi_tac')->on('doi_tac')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doi_soat_hoa_hong');
    }
};
