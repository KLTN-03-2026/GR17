<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TourKhoiHanhSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tour_khoi_hanhs')->insert([
            [
                'ma_thoi_gian_tour' => '001',
                'ma_tour' => '001',
                'ngay_bat_dau' => '2026-04-10',
                'ngay_ket_thuc' => '2026-04-12',
                'so_cho' => 40,
                'tinh_trang' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_thoi_gian_tour' => '002',
                'ma_tour' => '001',
                'ngay_bat_dau' => '2026-04-15',
                'ngay_ket_thuc' => '2026-04-17',
                'so_cho' => 40,
                'tinh_trang' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_thoi_gian_tour' => '003',
                'ma_tour' => 'T002',
                'ngay_bat_dau' => '2026-05-01',
                'ngay_ket_thuc' => '2026-05-04',
                'so_cho' => 30,
                'tinh_trang' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
