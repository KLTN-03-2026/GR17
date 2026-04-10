<?php

namespace Database\Seeders;

use App\Models\Nhom;
use Illuminate\Database\Seeder;

class NhomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nhomData = [
            [
                'Ma_nhom' => '001',
                'ten_nhom' => 'Nhóm du lịch Hà Nội - Hạ Long',
            ],
            [
                'Ma_nhom' => '002',
                'ten_nhom' => 'Nhóm du lịch Sapa - Lào Cai',
            ],
            [
                'Ma_nhom' => '003',
                'ten_nhom' => 'Nhóm du lịch Mekong Delta',
            ],
            [
                'Ma_nhom' => '004',
                'ten_nhom' => 'Nhóm du lịch núi Fansipan',
            ],
            [
                'Ma_nhom' => '005',
                'ten_nhom' => 'Nhóm khám phá thành phố Hồ Chí Minh',
            ],
            [
                'Ma_nhom' => '006',
                'ten_nhom' => 'Nhóm tham quan di tích lịch sử',
            ],
        ];

        foreach ($nhomData as $data) {
            Nhom::firstOrCreate(
                ['Ma_nhom' => $data['Ma_nhom']],
                ['ten_nhom' => $data['ten_nhom']]
            );
        }
    }
}
