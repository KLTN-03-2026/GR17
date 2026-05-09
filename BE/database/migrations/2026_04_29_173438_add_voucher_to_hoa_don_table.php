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
        Schema::table('hoa_don', function (Blueprint $table) {
            $table->string('ma_voucher')->nullable()->after('ma_giao_dich');
            $table->decimal('tien_giam_gia', 15, 2)->default(0)->after('ma_voucher');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hoa_don', function (Blueprint $table) {
            $table->dropColumn(['ma_voucher', 'tien_giam_gia']);
        });
    }
};
