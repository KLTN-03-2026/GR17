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
        Schema::create('tours', function (Blueprint $table) {
            $table->string('ma_tour', 10)->primary();
            $table->string('ten_tour');
            $table->text('mo_ta')->nullable();
            $table->string('hinh_anh')->nullable();
            $table->decimal('so_tien', 15, 2)->nullable();
            $table->integer('so_ngay')->nullable();
            $table->integer('so_nguoi')->nullable();
            $table->string('ma_tag')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
