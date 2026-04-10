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
        Schema::create('dich_vu_dia_diem', function (Blueprint $table) {
            $table->string('ma_dich_vu', 10)->primary();
            $table->string('ma_dia_diem', 10);
            $table->string('ten_dich_vu', 100);
            $table->text('mo_ta')->nullable();
            $table->integer('so_nguoi_toi_da');
            $table->decimal('gia', 12, 2);
            $table->boolean('trang_thai')->default(1);
            $table->timestamps();

            $table->foreign('ma_dia_diem')
                ->references('ma_dia_diem')
                ->on('dia_diem')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dich_vu_dia_diem');
    }
};
