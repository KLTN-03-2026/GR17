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
            $table->increments('id_xe_ke_hoach');
            $table->unsignedInteger('id_ke_hoach');
            $table->unsignedInteger('id_xe');
            $table->unsignedInteger('so_luong')->default(1);
            $table->unsignedInteger('so_ngay')->default(1);
            $table->decimal('tong_tien', 15, 2);
            $table->timestamps();

            $table->foreign('id_ke_hoach')->references('id_ke_hoach')->on('ke_hoach')->onDelete('cascade');
            $table->foreign('id_xe')->references('id_xe')->on('xe')->onDelete('cascade');

            $table->unique(['id_ke_hoach', 'id_xe']);
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
