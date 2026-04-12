<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('so_thich', function (Blueprint $table) {
            $table->string('ma_so_thich', 10)->primary();
            $table->string('ma_khach_hang');
            $table->string('ma_tag');
            $table->tinyInteger('muc_do')->comment('Từ 1 đến 5');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('so_thich');
    }
};