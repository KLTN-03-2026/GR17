<?php

namespace Database\Seeders;

use App\Models\ChucVu;
use Illuminate\Database\Seeder;

class ChucVuSeeder extends Seeder
{
    public function run(): void
    {
        $chucVuData = [
            ['ma_chuc_vu' => 'CV001', 'ten_chuc_vu' => 'Admin tổng', 'tinh_trang' => 1],
            ['ma_chuc_vu' => 'CV002', 'ten_chuc_vu' => 'Nhân viên kinh doanh', 'tinh_trang' => 1],
            ['ma_chuc_vu' => 'CV003', 'ten_chuc_vu' => 'Nhân viên chăm sóc khách hàng', 'tinh_trang' => 1],
            ['ma_chuc_vu' => 'CV004', 'ten_chuc_vu' => 'Kế toán', 'tinh_trang' => 1],
        ];

        foreach ($chucVuData as $data) {
            ChucVu::updateOrCreate(
                ['ma_chuc_vu' => $data['ma_chuc_vu']],
                ['ten_chuc_vu' => $data['ten_chuc_vu'], 'tinh_trang' => $data['tinh_trang']]
            );
        }
    }
}
