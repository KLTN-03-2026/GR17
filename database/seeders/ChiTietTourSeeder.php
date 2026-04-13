<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChiTietTourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                $faker = \Faker\Factory::create('vi_VN');
        $data = [
            [
                'ma_chi_tiet_tour' => '001',
                'ma_tour' => '001',
                'ma_dia_diem' => '001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_chi_tiet_tour' => '002',
                'ma_tour' => '001',
                'ma_dia_diem' => '002',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_chi_tiet_tour' => '003',
                'ma_tour' => '002',
                'ma_dia_diem' => '003',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        $ma_chi_tiet = 4;
        for ($i = 3; $i <= 17; $i++) {
            // Mỗi tour thêm 2-4 địa điểm ngẫu nhiên
            $numLocations = $faker->numberBetween(2, 4);
            $tour_id = str_pad($i, 3, '0', STR_PAD_LEFT);
            for ($j = 0; $j < $numLocations; $j++) {
                $data[] = [
                    'ma_chi_tiet_tour' => str_pad($ma_chi_tiet++, 3, '0', STR_PAD_LEFT),
                    'ma_tour' => $tour_id,
                    'ma_dia_diem' => str_pad($faker->numberBetween(1, 45), 3, '0', STR_PAD_LEFT),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        DB::table('chi_tiet_tours')->insert($data);
    }
}
