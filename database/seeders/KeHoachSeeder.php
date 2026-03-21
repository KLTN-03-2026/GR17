<?php

namespace Database\Seeders;

use App\Models\KeHoach;
use Illuminate\Database\Seeder;

class KeHoachSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $keHoachData = [
            [
                'ma_ke_hoach' => '001',
                'ten_ke_hoach' => 'Chuyên du lịch Hà Nội - Hạ Long',
                'mo_ta' => 'Kế hoạch du lịch 3 ngày 2 đêm đến Vịnh Hạ Long',
                'ngay_bat_dau' => '2026-03-20',
                'ngay_ket_thuc' => '2026-03-22',
                'tong_chi_phi' => 50000000,
                'trang_thai' => 1,
            ],
            [
                'ma_ke_hoach' => '002',
                'ten_ke_hoach' => 'Chuyên cử công sở tháng 3',
                'mo_ta' => 'Vận chuyển nhân viên công sở hàng ngày',
                'ngay_bat_dau' => '2026-03-01',
                'ngay_ket_thuc' => '2026-03-31',
                'tong_chi_phi' => 100000000,
                'trang_thai' => 2,
            ],
            [
                'ma_ke_hoach' => '003',
                'ten_ke_hoach' => 'Du lịch Sapa - Lào Cai',
                'mo_ta' => 'Kế hoạch du lịch 4 ngày 3 đêm tới Sapa',
                'ngay_bat_dau' => '2026-04-10',
                'ngay_ket_thuc' => '2026-04-13',
                'tong_chi_phi' => 75000000,
                'trang_thai' => 1,
            ],
            [
                'ma_ke_hoach' => '004',
                'ten_ke_hoach' => 'Chuyến di tích lịch sử miền Bắc',
                'mo_ta' => 'Tham quan các di tích lịch sử tuần Tây Bắc',
                'ngay_bat_dau' => '2026-05-01',
                'ngay_ket_thuc' => '2026-05-05',
                'tong_chi_phi' => 80000000,
                'trang_thai' => 1,
            ],
            [
                'ma_ke_hoach' => '005',
                'ten_ke_hoach' => 'Team building công ty quý 2',
                'mo_ta' => 'Hoạt động gắn kết nhân viên toàn công ty',
                'ngay_bat_dau' => '2026-04-25',
                'ngay_ket_thuc' => '2026-04-26',
                'tong_chi_phi' => 120000000,
                'trang_thai' => 1,
            ],
        ];

        foreach ($keHoachData as $data) {
            KeHoach::firstOrCreate(
                ['ma_ke_hoach' => $data['ma_ke_hoach']],
                [
                    'ten_ke_hoach' => $data['ten_ke_hoach'],
                    'mo_ta' => $data['mo_ta'],
                    'ngay_bat_dau' => $data['ngay_bat_dau'],
                    'ngay_ket_thuc' => $data['ngay_ket_thuc'],
                    'tong_chi_phi' => $data['tong_chi_phi'],
                    'trang_thai' => $data['trang_thai'],
                ]
            );
        }
    }
}
