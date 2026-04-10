<?php

namespace Database\Seeders;

use App\Models\ChucVu;
use Illuminate\Database\Seeder;

class ChucVuSeeder extends Seeder
{
    public function run(): void
    {
        $chucVuData = [
            ['ma_chuc_vu' => '001', 'ten_chuc_vu' => 'Giám đốc', 'tinh_trang' => 1],
            ['ma_chuc_vu' => '002', 'ten_chuc_vu' => 'Quản lý', 'tinh_trang' => 1],
            ['ma_chuc_vu' => '003', 'ten_chuc_vu' => 'Kỹ sư', 'tinh_trang' => 1],
            ['ma_chuc_vu' => '004', 'ten_chuc_vu' => 'Thiết kế viên', 'tinh_trang' => 1],
            ['ma_chuc_vu' => '005', 'ten_chuc_vu' => 'Hỗ trợ khách hàng', 'tinh_trang' => 1],
        ];

        foreach ($chucVuData as $data) {
            ChucVu::firstOrCreate(
                ['ma_chuc_vu' => $data['ma_chuc_vu']],
                ['ten_chuc_vu' => $data['ten_chuc_vu'], 'tinh_trang' => $data['tinh_trang']]
            );
        }
    }
}
