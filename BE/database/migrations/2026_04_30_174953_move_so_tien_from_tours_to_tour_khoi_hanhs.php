<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Chép dữ liệu từ tours sang tour_khoi_hanhs nếu cần, tuy nhiên vì drop_column ta làm riêng.
        Schema::table('tour_khoi_hanhs', function (Blueprint $table) {
            $table->decimal('so_tien', 15, 2)->default(0)->after('ma_tour');
        });

        // Sao chép dữ liệu (nếu có tour_khoi_hanh cũ)
        $this->syncTourPricesToDepartures();

        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn('so_tien');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->decimal('so_tien', 15, 2)->default(0)->after('hinh_anh');
        });

        $this->syncDeparturePricesToTours();

        Schema::table('tour_khoi_hanhs', function (Blueprint $table) {
            $table->dropColumn('so_tien');
        });
    }

    private function syncTourPricesToDepartures(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement(
                'UPDATE tour_khoi_hanhs
                 SET so_tien = COALESCE(
                    (SELECT tours.so_tien FROM tours WHERE tours.ma_tour = tour_khoi_hanhs.ma_tour),
                    so_tien
                 )'
            );
            return;
        }

        DB::statement('UPDATE tour_khoi_hanhs tkh JOIN tours t ON tkh.ma_tour = t.ma_tour SET tkh.so_tien = t.so_tien');
    }

    private function syncDeparturePricesToTours(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement(
                'UPDATE tours
                 SET so_tien = COALESCE(
                    (SELECT tour_khoi_hanhs.so_tien FROM tour_khoi_hanhs WHERE tour_khoi_hanhs.ma_tour = tours.ma_tour LIMIT 1),
                    so_tien
                 )'
            );
            return;
        }

        DB::statement('UPDATE tours t JOIN tour_khoi_hanhs tkh ON t.ma_tour = tkh.ma_tour SET t.so_tien = tkh.so_tien');
    }
};
