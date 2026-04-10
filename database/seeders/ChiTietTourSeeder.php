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
            ],
        ];

        $maChiTiet = 4;
        for ($i = 3; $i <= 17; $i++) {
            // Mỗi tour thêm 2-4 địa điểm ngẫu nhiên và không trùng trong cùng tour
            $numLocations = $faker->numberBetween(2, 4);
            $tourId = str_pad($i, 3, '0', STR_PAD_LEFT);
            $availableLocations = range(1, 45);
            shuffle($availableLocations);
            $selectedLocations = array_slice($availableLocations, 0, $numLocations);

            foreach ($selectedLocations as $locationId) {
                $data[] = [
                    'ma_chi_tiet_tour' => str_pad($maChiTiet++, 3, '0', STR_PAD_LEFT),
                    'ma_tour' => $tourId,
                    'ma_dia_diem' => str_pad($locationId, 3, '0', STR_PAD_LEFT),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('chi_tiet_tours')->insert($data);
    }
}
