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
        Schema::create('phan_quyen_admin', function (Blueprint $table) {
            $table->string('ma_phan_quyen', 20)->primary();
            $table->string('ma_chuc_nang', 10);
            $table->string('ma_chuc_vu', 10);
            $table->timestamps();

            $table->foreign('ma_chuc_nang')->references('ma_chuc_nang')->on('chuc_nang')->onDelete('cascade');
            $table->foreign('ma_chuc_vu')->references('ma_chuc_vu')->on('chuc_vu')->onDelete('cascade');

            $table->unique(['ma_chuc_nang', 'ma_chuc_vu']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phan_quyen_admin');
    }
};
