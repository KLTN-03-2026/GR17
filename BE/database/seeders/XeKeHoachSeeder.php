<?php

namespace Database\Seeders;

use App\Models\XeKeHoach;
use Illuminate\Database\Seeder;

class XeKeHoachSeeder extends Seeder
{
    public function run(): void
    {
        $xeKeHoachData = [
            [
                'ma_xe_ke_hoach' => '001',
                'ma_ke_hoach' => '001',
                'ma_xe' => '001',
                'so_luong' => 2,
                'so_ngay' => 3,
                'tong_tien' => 9000000,
            ],
            [
                'ma_xe_ke_hoach' => '002',
                'ma_ke_hoach' => '001',
                'ma_xe' => '004',
                'so_luong' => 1,
                'so_ngay' => 3,
                'tong_tien' => 2700000,
            ],
            [
                'ma_xe_ke_hoach' => '003',
                'ma_ke_hoach' => '002',
                'ma_xe' => '003',
                'so_luong' => 5,
                'so_ngay' => 20,
                'tong_tien' => 120000000,
            ],
            [
                'ma_xe_ke_hoach' => '004',
                'ma_ke_hoach' => '003',
                'ma_xe' => '002',
                'so_luong' => 1,
                'so_ngay' => 4,
                'tong_tien' => 10000000,
            ],
            [
                'ma_xe_ke_hoach' => '005',
                'ma_ke_hoach' => '005',
                'ma_xe' => '001',
                'so_luong' => 3,
                'so_ngay' => 2,
                'tong_tien' => 9000000,
            ],
        ];

        foreach ($xeKeHoachData as $data) {
            XeKeHoach::firstOrCreate(
                ['ma_xe_ke_hoach' => $data['ma_xe_ke_hoach']],
                [
                    'ma_ke_hoach' => $data['ma_ke_hoach'],
                    'ma_xe' => $data['ma_xe'],
                    'so_luong' => $data['so_luong'],
                    'so_ngay' => $data['so_ngay'],
                    'tong_tien' => $data['tong_tien'],
                ]
            );
        }
    }
}
