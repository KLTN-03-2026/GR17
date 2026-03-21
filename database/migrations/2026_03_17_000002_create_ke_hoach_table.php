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
        if (!Schema::hasTable('ke_hoach')) {
            Schema::create('ke_hoach', function (Blueprint $table) {
                $table->string('ma_ke_hoach', 10)->primary();
                $table->string('ma_nhom', 10)->nullable();
                $table->string('ten_ke_hoach', 100);
                $table->integer('so_nguoi')->default(1);
                $table->date('ngay_bat_dau');
                $table->date('ngay_ket_thuc');
                $table->decimal('ngan_sach_du_kien', 15, 2)->default(0);
                $table->boolean('trang_thai')->default(1);
                $table->timestamps();

                $table->foreign('ma_nhom')
                    ->references('Ma_nhom')
                    ->on('nhom')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ke_hoach');
    }
};
