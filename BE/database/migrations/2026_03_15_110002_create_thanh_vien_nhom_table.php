<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('thanh_vien_nhom');
        Schema::enableForeignKeyConstraints();
        Schema::create('thanh_vien_nhom', function (Blueprint $table) {
            $table->string('Ma_thanh_vien', 20)->primary();
            $table->string('Ma_nhom', 20);
            $table->string('Ma_khach_hang', 10);
            $table->tinyInteger('vai_tro')->default(0)->comment('0: thành viên, 1: nhóm trưởng');
            $table->timestamps();

            $table->foreign('Ma_nhom')
                  ->references('Ma_nhom')->on('nhom')
                  ->onDelete('cascade');

            $table->foreign('Ma_khach_hang')
                  ->references('Ma_khach_hang')->on('khach_hang')
                  ->onDelete('cascade');

            $table->unique(
                ['Ma_nhom', 'Ma_khach_hang'],
                'thanh_vien_nhom_group_customer_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thanh_vien_nhom');
    }
};
