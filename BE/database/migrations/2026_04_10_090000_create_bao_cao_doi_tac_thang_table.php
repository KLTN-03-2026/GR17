<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bao_cao_doi_tac_thang', function (Blueprint $table) {
            $table->id();
            $table->string('ma_doi_tac', 20);
            $table->date('thang_bao_cao');
            $table->string('trang_thai', 20)->default('queued')->comment('queued, processing, sent, failed');
            $table->unsignedInteger('so_lan_thu')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->unique(['ma_doi_tac', 'thang_bao_cao'], 'bao_cao_doi_tac_thang_unique');
            $table->foreign('ma_doi_tac')
                ->references('ma_doi_tac')
                ->on('doi_tac')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bao_cao_doi_tac_thang');
    }
};
