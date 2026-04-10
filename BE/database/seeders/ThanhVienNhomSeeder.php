<?php

namespace Database\Seeders;

use App\Models\ThanhVienNhom;
use Illuminate\Database\Seeder;

class ThanhVienNhomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $thanhVienData = [
            // Nhóm NH001 - du lịch Hà Nội - Hạ Long
            [
                'Ma_thanh_vien' => '001',
                'Ma_nhom' => '001',
                'Ma_khach_hang' => '1',
                'vai_tro' => 1, // nhóm trưởng
            ],
            [
                'Ma_thanh_vien' => '002',
                'Ma_nhom' => '001',
                'Ma_khach_hang' => '2',
                'vai_tro' => 0, // thành viên
            ],
            [
                'Ma_thanh_vien' => '003',
                'Ma_nhom' => '001',
                'Ma_khach_hang' => '3',
                'vai_tro' => 0, // thành viên
            ],
            [
                'Ma_thanh_vien' => '004',
                'Ma_nhom' => '001',
                'Ma_khach_hang' => '4',
                'vai_tro' => 0, // thành viên
            ],

            // Nhóm 002 - du lịch Sapa - Lào Cai
            [
                'Ma_thanh_vien' => '005',
                'Ma_nhom' => '002',
                'Ma_khach_hang' => '2',
                'vai_tro' => 1, // nhóm trưởng
            ],
            [
                'Ma_thanh_vien' => '006',
                'Ma_nhom' => '002',
                'Ma_khach_hang' => '5',
                'vai_tro' => 0, // thành viên
            ],
            [
                'Ma_thanh_vien' => '007',
                'Ma_nhom' => '002',
                'Ma_khach_hang' => '6',
                'vai_tro' => 0, // thành viên
            ],

            // Nhóm 003 - du lịch Mekong Delta
            [
                'Ma_thanh_vien' => '008',
                'Ma_nhom' => '003',
                'Ma_khach_hang' => '3',
                'vai_tro' => 1, // nhóm trưởng
            ],
            [
                'Ma_thanh_vien' => '009',
                'Ma_nhom' => '003',
                'Ma_khach_hang' => '7',
                'vai_tro' => 0, // thành viên
            ],
            [
                'Ma_thanh_vien' => '010',
                'Ma_nhom' => '003',
                'Ma_khach_hang' => '8',
                'vai_tro' => 0, // thành viên
            ],

            // Nhóm 004 - du lịch núi Fansipan
            [
                'Ma_thanh_vien' => '011',
                'Ma_nhom' => '004',
                'Ma_khach_hang' => '4',
                'vai_tro' => 1, // nhóm trưởng
            ],
            [
                'Ma_thanh_vien' => '012',
                'Ma_nhom' => '004',
                'Ma_khach_hang' => '1',
                'vai_tro' => 0, // thành viên
            ],

            // Nhóm 005 - khám phá TP Hồ Chí Minh
            [
                'Ma_thanh_vien' => '013',
                'Ma_nhom' => '005',
                'Ma_khach_hang' => '5',
                'vai_tro' => 1, // nhóm trưởng
            ],
            [
                'Ma_thanh_vien' => '014',
                'Ma_nhom' => '005',
                'Ma_khach_hang' => '6',
                'vai_tro' => 0, // thành viên
            ],
            [
                'Ma_thanh_vien' => '015',
                'Ma_nhom' => '005',
                'Ma_khach_hang' => '9',
                'vai_tro' => 0, // thành viên
            ],

            // Nhóm 006 - tham quan di tích lịch sử
            [
                'Ma_thanh_vien' => '016',
                'Ma_nhom' => '006',
                'Ma_khach_hang' => '7',
                'vai_tro' => 1, // nhóm trưởng
            ],
            [
                'Ma_thanh_vien' => '017',
                'Ma_nhom' => '006',
                'Ma_khach_hang' => '8',
                'vai_tro' => 0, // thành viên
            ],
        ];

        foreach ($thanhVienData as $data) {
            ThanhVienNhom::firstOrCreate(
                ['Ma_thanh_vien' => $data['Ma_thanh_vien']],
                [
                    'Ma_nhom' => $data['Ma_nhom'],
                    'Ma_khach_hang' => $data['Ma_khach_hang'],
                    'vai_tro' => $data['vai_tro'],
                ]
            );
        }
    }
}
