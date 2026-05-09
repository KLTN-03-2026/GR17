<?php

namespace Database\Seeders;

use App\Models\PhanQuyenAdmin;
use Illuminate\Database\Seeder;

class PhanQuyenAdminSeeder extends Seeder
{
    public function run(): void
    {
        $phanQuyenData = [
            // Admin tổng (CV001) - Có tất cả quyền (101-108)
            ['ma_chuc_nang' => '101', 'ma_chuc_vu' => 'CV001'],
            ['ma_chuc_nang' => '102', 'ma_chuc_vu' => 'CV001'],
            ['ma_chuc_nang' => '103', 'ma_chuc_vu' => 'CV001'],
            ['ma_chuc_nang' => '104', 'ma_chuc_vu' => 'CV001'],
            ['ma_chuc_nang' => '105', 'ma_chuc_vu' => 'CV001'],
            ['ma_chuc_nang' => '106', 'ma_chuc_vu' => 'CV001'],
            ['ma_chuc_nang' => '107', 'ma_chuc_vu' => 'CV001'],
            ['ma_chuc_nang' => '108', 'ma_chuc_vu' => 'CV001'],

            // Nhân viên kinh doanh (CV002) - Tour, Địa điểm, Đối tác
            ['ma_chuc_nang' => '101', 'ma_chuc_vu' => 'CV002'],
            ['ma_chuc_nang' => '103', 'ma_chuc_vu' => 'CV002'],
            ['ma_chuc_nang' => '104', 'ma_chuc_vu' => 'CV002'],

            // Nhân viên CSKH (CV003) - Khách hàng, Phản hồi
            ['ma_chuc_nang' => '102', 'ma_chuc_vu' => 'CV003'],

            // Kế toán (CV004) - Voucher, Thống kê
            ['ma_chuc_nang' => '105', 'ma_chuc_vu' => 'CV004'],
            ['ma_chuc_nang' => '106', 'ma_chuc_vu' => 'CV004'],
        ];

        foreach ($phanQuyenData as $data) {
            PhanQuyenAdmin::firstOrCreate($data);
        }
    }
}
