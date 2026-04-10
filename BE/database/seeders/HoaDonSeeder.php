<?php

namespace Database\Seeders;

use App\Models\HoaDon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HoaDonSeeder extends Seeder
{
    public function run(): void
    {
                $hoaDonData = [
            [
                'ma_hoa_don' => '001',
                'ma_nhom' => '001',
                'loai_hoa_don' => 0,
                'ma_doi_tuong' => '001',
                'tong_tien' => 5000000,
                'trang_thai_thanh_toan' => 1,
                'ma_giao_dich' => 'TX12345',
                'ngay_tao' => now()->subDays(5),
            ],
            [
                'ma_hoa_don' => '002',
                'ma_nhom' => '001',
                'loai_hoa_don' => 1,
                'ma_doi_tuong' => '001', // Example group plan
                'tong_tien' => 800000,
                'trang_thai_thanh_toan' => 0,
                'ma_giao_dich' => null,
                'ngay_tao' => now()->subDays(4),
            ],
            [
                'ma_hoa_don' => '003',
                'ma_nhom' => '002',
                'loai_hoa_don' => 0,
                'ma_doi_tuong' => '002',
                'tong_tien' => 12000000,
                'trang_thai_thanh_toan' => 1,
                'ma_giao_dich' => 'TX98765',
                'ngay_tao' => now()->subDays(3),
            ]
        ];

        $faker = \Faker\Factory::create('vi_VN');
        for ($i = 4; $i <= 10; $i++) {
            $hoaDonData[] = [
                'ma_hoa_don' => str_pad($i, 3, '0', STR_PAD_LEFT),
                'ma_nhom' => str_pad($faker->numberBetween(1, 10), 3, '0', STR_PAD_LEFT),
                'loai_hoa_don' => $faker->numberBetween(0, 1),
                'ma_doi_tuong' => str_pad($faker->numberBetween(1, 10), 3, '0', STR_PAD_LEFT),
                'tong_tien' => $faker->numberBetween(5, 50) * 100000,
                'trang_thai_thanh_toan' => $faker->numberBetween(0, 1),
                'ma_giao_dich' => 'TX' . $faker->numberBetween(10000, 99999),
                'ngay_tao' => now()->subDays($faker->numberBetween(1, 10)),
            ];
        };

        foreach ($hoaDonData as $data) {
            HoaDon::firstOrCreate(
                ['ma_hoa_don' => $data['ma_hoa_don']],
                [
                    'ma_nhom' => $data['ma_nhom'],
                    'loai_hoa_don' => $data['loai_hoa_don'],
                    'ma_doi_tuong' => $data['ma_doi_tuong'],
                    'tong_tien' => $data['tong_tien'],
                    'trang_thai_thanh_toan' => $data['trang_thai_thanh_toan'],
                    'ma_giao_dich' => $data['ma_giao_dich'],
                    'ngay_tao' => $data['ngay_tao'],
                ]
            );
        }
    }
}
