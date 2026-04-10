<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tours')->insert([
            [
                'ma_tour' => '001',
                'ten_tour' => 'Tour Du Lịch Phú Quốc 3N2Đ',
                'mo_ta' => 'Khám phá ngọc đảo Phú Quốc với nhiều khuyến mãi',
                'hinh_anh' => 'https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&w=1400&q=80',
                'so_tien' => 4500000,
                'so_ngay' => 3,
                'so_nguoi' => 20,
                'ma_tag' => 'TAG01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_tour' => '002',
                'ten_tour' => 'Tour Biển Đà Nẵng 4N3Đ',
                'mo_ta' => 'Nghỉ dưỡng 5 sao tại bãi biển Mỹ Khê',
                'hinh_anh' => 'https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?auto=format&fit=crop&w=1400&q=80',
                'so_tien' => 6000000,
                'so_ngay' => 4,
                'so_nguoi' => 15,
                'ma_tag' => 'TAG02',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
