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

        $itineraries = [
            // Tour CENT001: Đà Nẵng - Hội An - Huế
            ['ma_tour' => 'CENT001', 'ma_dia_diem' => '011', 'order' => 1], // Cầu Vàng
            ['ma_tour' => 'CENT001', 'ma_dia_diem' => 'HA001', 'order' => 2], // Rừng dừa
            ['ma_tour' => 'CENT001', 'ma_dia_diem' => '012', 'order' => 3], // Phố cổ Hội An
            ['ma_tour' => 'CENT001', 'ma_dia_diem' => '013', 'order' => 4], // Đại Nội Huế
            ['ma_tour' => 'CENT001', 'ma_dia_diem' => 'HU001', 'order' => 5], // Lăng Khải Định
            
            // Tour CENT002: Đà Nẵng
            ['ma_tour' => 'CENT002', 'ma_dia_diem' => 'DN001', 'order' => 1], // Sơn Trà
            ['ma_tour' => 'CENT002', 'ma_dia_diem' => '011', 'order' => 2], // Bà Nà
            ['ma_tour' => 'CENT002', 'ma_dia_diem' => 'DN004', 'order' => 3], // Cầu Rồng
            
            // Tour CENT003: Huế
            ['ma_tour' => 'CENT003', 'ma_dia_diem' => '013', 'order' => 1], // Đại Nội
            ['ma_tour' => 'CENT003', 'ma_dia_diem' => 'HU002', 'order' => 2], // Chùa Thiên Mụ
            ['ma_tour' => 'CENT003', 'ma_dia_diem' => 'HU003', 'order' => 3], // Chợ Đông Ba
            
            // Tour CENT004: Hội An
            ['ma_tour' => 'CENT004', 'ma_dia_diem' => 'HA001', 'order' => 1], // Rừng dừa
            ['ma_tour' => 'CENT004', 'ma_dia_diem' => '012', 'order' => 2], // Phố cổ
            ['ma_tour' => 'CENT004', 'ma_dia_diem' => '022', 'order' => 3], // Quán ăn Hội An
        ];

        $data = [];
        $maChiTiet = 1;
        foreach ($itineraries as $item) {
            $data[] = [
                'ma_chi_tiet_tour' => 'CT' . str_pad($maChiTiet++, 3, '0', STR_PAD_LEFT),
                'ma_tour' => $item['ma_tour'],
                'ma_dia_diem' => $item['ma_dia_diem'],
                'thu_tu_hanh_trinh' => $item['order'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('chi_tiet_tours')->insert($data);
    }
}
