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
            $table->increments('id_phan_quyen');
            $table->unsignedInteger('id_chuc_nang');
            $table->unsignedInteger('id_chuc_vu');
            $table->timestamps();

            $table->foreign('id_chuc_nang')->references('id_chuc_nang')->on('chuc_nang')->onDelete('cascade');
            $table->foreign('id_chuc_vu')->references('id_chuc_vu')->on('chuc_vu')->onDelete('cascade');

            $table->unique(['id_chuc_nang', 'id_chuc_vu']);
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
