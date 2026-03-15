<?php

namespace Database\Seeders;

use App\Models\XeKeHoach;
use Illuminate\Database\Seeder;

class XeKeHoachSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $xeKeHoachData = [
            [
                'id_ke_hoach' => 1,
                'id_xe' => 1,
                'so_luong' => 2,
                'so_ngay' => 3,
                'tong_tien' => 9000000,
            ],
            [
                'id_ke_hoach' => 1,
                'id_xe' => 4,
                'so_luong' => 1,
                'so_ngay' => 3,
                'tong_tien' => 2700000,
            ],
            [
                'id_ke_hoach' => 2,
                'id_xe' => 3,
                'so_luong' => 5,
                'so_ngay' => 20,
                'tong_tien' => 120000000,
            ],
            [
                'id_ke_hoach' => 3,
                'id_xe' => 2,
                'so_luong' => 1,
                'so_ngay' => 4,
                'tong_tien' => 10000000,
            ],
            [
                'id_ke_hoach' => 5,
                'id_xe' => 1,
                'so_luong' => 3,
                'so_ngay' => 2,
                'tong_tien' => 9000000,
            ],
        ];

        foreach ($xeKeHoachData as $data) {
            XeKeHoach::firstOrCreate(
                ['id_ke_hoach' => $data['id_ke_hoach'], 'id_xe' => $data['id_xe']],
                [
                    'so_luong' => $data['so_luong'],
                    'so_ngay' => $data['so_ngay'],
                    'tong_tien' => $data['tong_tien'],
                ]
            );
        }
    }
}
