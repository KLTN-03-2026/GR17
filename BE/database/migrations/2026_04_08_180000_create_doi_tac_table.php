<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('doi_tac')) {
            return;
        }

        Schema::create('doi_tac', function (Blueprint $table) {
            $table->string('ma_doi_tac', 10)->primary();
            $table->string('ten_doi_tac');
            $table->string('ten_nguoi_dai_dien');
            $table->string('email')->unique();
            $table->string('mat_khau');
            $table->string('so_dien_thoai', 20)->nullable();
            $table->string('dia_chi')->nullable();
            $table->boolean('is_block')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doi_tac');
    }
};
