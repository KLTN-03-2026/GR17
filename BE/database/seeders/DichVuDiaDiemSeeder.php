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
                'ma_dia_diem' => 'DN_TQ_001',
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
                'ma_dia_diem' => 'DN_TQ_001',
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
                'ma_dia_diem' => 'HUE_TQ_001',
                'ten_dich_vu' => 'Chụp Ảnh Kỷ Niệm',
                'mo_ta' => 'Thợ chụp ảnh chuyên nghiệp và cho thuê trang phục truyền thống',
                'so_nguoi_toi_da' => 2,
                'gia' => 300000,
                'trang_thai' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_dich_vu' => '004',
                'ma_dia_diem' => 'HUE_KS_001',
                'ten_dich_vu' => 'Spa & Massage',
                'mo_ta' => 'Dịch vụ thư giãn cao cấp tại khách sạn',
                'so_nguoi_toi_da' => 1,
                'gia' => 1200000,
                'trang_thai' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_dich_vu' => '005',
                'ma_dia_diem' => 'HA_TQ_001',
                'ten_dich_vu' => 'Ăn Tối Lãng Mạn',
                'mo_ta' => 'Bữa tối dành cho 2 người với thực đơn đặc biệt',
                'so_nguoi_toi_da' => 2,
                'gia' => 2500000,
                'trang_thai' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_dich_vu' => '006',
                'ma_dia_diem' => 'HA_TQ_002',
                'ten_dich_vu' => 'Tour Thuyền Sông',
                'mo_ta' => 'Ngắm cảnh thành phố từ trên sông Sài Gòn',
                'so_nguoi_toi_da' => 15,
                'gia' => 150000,
                'trang_thai' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
