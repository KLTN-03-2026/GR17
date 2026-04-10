<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('nhom');
        Schema::enableForeignKeyConstraints();
        Schema::create('nhom', function (Blueprint $table) {
            $table->string('Ma_nhom', 20)->primary();
            $table->string('ten_nhom', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nhom');
    }
};
