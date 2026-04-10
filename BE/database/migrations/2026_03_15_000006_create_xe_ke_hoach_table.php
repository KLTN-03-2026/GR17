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
        Schema::create('xe_ke_hoach', function (Blueprint $table) {
            $table->string('ma_xe_ke_hoach', 20)->primary();
            $table->string('ma_ke_hoach', 20);
            $table->string('ma_xe', 10);
            $table->unsignedInteger('so_luong')->default(1);
            $table->unsignedInteger('so_ngay')->default(1);
            $table->decimal('tong_tien', 15, 2);
            $table->timestamps();

            $table->foreign('ma_ke_hoach')->references('ma_ke_hoach')->on('ke_hoach')->onDelete('cascade');
            $table->foreign('ma_xe')->references('ma_xe')->on('xe')->onDelete('cascade');

            $table->unique(['ma_ke_hoach', 'ma_xe']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('xe_ke_hoach');
    }
};
