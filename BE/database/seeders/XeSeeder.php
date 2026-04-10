<?php

namespace Database\Seeders;

use App\Models\Xe;
use Illuminate\Database\Seeder;

class XeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $xeData = [
            [
                'ma_xe' => '001',
                'ten_xe' => 'Toyota Hiace Standard',
                'loai_xe' => 'Xe 16 chỗ',
                'so_cho' => 16,
                'gia_theo_ngay' => 1500000,
                'tong_so_xe' => 5,
                'mo_ta' => 'Xe khách tiêu chuẩn, phù hợp cho chuyên du lịch, thoải mái và an toàn',
                'trang_thai' => 1,
            ],
            [
                'ma_xe' => '002',
                'ten_xe' => 'Hyundai County Deluxe',
                'loai_xe' => 'Xe 29 chỗ',
                'so_cho' => 29,
                'gia_theo_ngay' => 2500000,
                'tong_so_xe' => 3,
                'mo_ta' => 'Xe khách hạng sang, thiết bị hiện đại, phù hợp cho các chuyến đi xa',
                'trang_thai' => 1,
            ],
            [
                'ma_xe' => '003',
                'ten_xe' => 'Ford Transit 12 chỗ',
                'loai_xe' => 'Xe 12 chỗ',
                'so_cho' => 12,
                'gia_theo_ngay' => 1200000,
                'tong_so_xe' => 7,
                'mo_ta' => 'Xe vận chuyển nhân viên, phù hợp cho chuyên cử công sở',
                'trang_thai' => 1,
            ],
            [
                'ma_xe' => '004',
                'ten_xe' => 'Kia Sorento 7 chỗ',
                'loai_xe' => 'Xe 7 chỗ',
                'so_cho' => 7,
                'gia_theo_ngay' => 900000,
                'tong_so_xe' => 10,
                'mo_ta' => 'SUV gia đình, nội thất thoải mái, tiêu hao xăng thấp',
                'trang_thai' => 1,
            ],
            [
                'ma_xe' => '005',
                'ten_xe' => 'Honda Accord 4 chỗ',
                'loai_xe' => 'Xe 4 chỗ',
                'so_cho' => 4,
                'gia_theo_ngay' => 600000,
                'tong_so_xe' => 8,
                'mo_ta' => 'Xe sedan cao cấp, thích hợp cho công tác, du lịch cá nhân',
                'trang_thai' => 1,
            ],
        ];

        foreach ($xeData as $data) {
            Xe::firstOrCreate(
                ['ma_xe' => $data['ma_xe']],
                [
                    'ten_xe' => $data['ten_xe'],
                    'loai_xe' => $data['loai_xe'],
                    'so_cho' => $data['so_cho'],
                    'gia_theo_ngay' => $data['gia_theo_ngay'],
                    'tong_so_xe' => $data['tong_so_xe'],
                    'mo_ta' => $data['mo_ta'],
                    'trang_thai' => $data['trang_thai'],
                ]
            );
        }
    }
}
