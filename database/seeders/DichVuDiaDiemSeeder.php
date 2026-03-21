<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DichVuDiaDiemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('dich_vu_dia_diem')->insert([
            [
                'ma_dich_vu' => '001',
                'ma_dia_diem' => '001',
                'ten_dich_vu' => 'Đưa đón Cảng',
                'mo_ta' => 'Dịch vụ xe đưa đón từ cảng về khách sạn',
                'so_nguoi_toi_da' => 10,
                'gia' => 500000,
                'trang_thai' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_dich_vu' => '002',
                'ma_dia_diem' => '001',
                'ten_dich_vu' => 'Thuê Hướng Dẫn Viên',
                'mo_ta' => 'Thuê hướng dẫn viên du lịch bản địa',
                'so_nguoi_toi_da' => 5,
                'gia' => 800000,
                'trang_thai' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_dich_vu' => '003',
                'ma_dia_diem' => '002',
                'ten_dich_vu' => 'Chụp Ảnh Kỷ Niệm',
                'mo_ta' => 'Thợ chụp ảnh và cho thuê đồ bơi',
                'so_nguoi_toi_da' => 2,
                'gia' => 300000,
                'trang_thai' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
