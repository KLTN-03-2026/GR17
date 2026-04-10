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
        Schema::table('doi_tac', function (Blueprint $table) {
            $table->string('trang_thai_duyet', 30)->default('pending')->after('is_block');
            $table->text('ly_do_tu_choi')->nullable()->after('trang_thai_duyet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doi_tac', function (Blueprint $table) {
            $table->dropColumn(['trang_thai_duyet', 'ly_do_tu_choi']);
        });
    }
};
