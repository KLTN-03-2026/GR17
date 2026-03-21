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
        Schema::create('chuc_nang', function (Blueprint $table) {
            $table->string('ma_chuc_nang', 10)->primary();
            $table->string('ten_chuc_nang', 100)->unique();
            $table->string('mo_ta', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chuc_nang');
    }
};
