<?php

namespace Database\Seeders;

use App\Models\KeHoach;
use Illuminate\Database\Seeder;

class KeHoachSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'ma_ke_hoach' => '001',
                'ma_nhom' => '001',
                'ten_ke_hoach' => 'Chuyen du lich Ha Noi - Ha Long',
                'mo_ta' => 'Ke hoach du lich 3 ngay 2 dem den Vinh Ha Long',
                'so_nguoi' => 4,
                'ngay_bat_dau' => '2026-03-20',
                'ngay_ket_thuc' => '2026-03-22',
                'tong_chi_phi' => 50000000,
                'ngan_sach_du_kien' => 50000000,
                'trang_thai' => 1,
            ],
            [
                'ma_ke_hoach' => '002',
                'ma_nhom' => '002',
                'ten_ke_hoach' => 'Chuyen cong tac thang 3',
                'mo_ta' => 'Van chuyen nhan vien cong so hang ngay',
                'so_nguoi' => 8,
                'ngay_bat_dau' => '2026-03-01',
                'ngay_ket_thuc' => '2026-03-31',
                'tong_chi_phi' => 100000000,
                'ngan_sach_du_kien' => 100000000,
                'trang_thai' => 2,
            ],
            [
                'ma_ke_hoach' => '003',
                'ma_nhom' => '003',
                'ten_ke_hoach' => 'Du lich Sapa - Lao Cai',
                'mo_ta' => 'Ke hoach du lich 4 ngay 3 dem toi Sapa',
                'so_nguoi' => 3,
                'ngay_bat_dau' => '2026-04-10',
                'ngay_ket_thuc' => '2026-04-13',
                'tong_chi_phi' => 75000000,
                'ngan_sach_du_kien' => 75000000,
                'trang_thai' => 1,
            ],
            [
                'ma_ke_hoach' => '004',
                'ma_nhom' => '004',
                'ten_ke_hoach' => 'Chuyen tham quan mien Bac',
                'mo_ta' => 'Tham quan các địa điểm lịch sử và cảnh quan',
                'so_nguoi' => 2,
                'ngay_bat_dau' => '2026-05-01',
                'ngay_ket_thuc' => '2026-05-05',
                'tong_chi_phi' => 80000000,
                'ngan_sach_du_kien' => 80000000,
                'trang_thai' => 1,
            ],
            [
                'ma_ke_hoach' => '005',
                'ma_nhom' => '005',
                'ten_ke_hoach' => 'Team building cong ty quy 2',
                'mo_ta' => 'Hoat dong gan ket nhan vien toan cong ty',
                'so_nguoi' => 6,
                'ngay_bat_dau' => '2026-04-25',
                'ngay_ket_thuc' => '2026-04-26',
                'tong_chi_phi' => 120000000,
                'ngan_sach_du_kien' => 120000000,
                'trang_thai' => 1,
            ],
        ];

        foreach ($plans as $plan) {
            KeHoach::updateOrCreate(
                ['ma_ke_hoach' => $plan['ma_ke_hoach']],
                $plan,
            );
        }
    }
}
