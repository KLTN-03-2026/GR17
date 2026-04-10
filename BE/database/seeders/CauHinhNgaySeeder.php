<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CauHinhNgaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = date('Y');

        DB::table('cau_hinh_ngay')->insert([
            [
                'ma_cau_hinh_ngay' => '1',
                'loai_ngay_le' => 1,
                'ten_ngay_le' => 'Tết Dương Lịch',
                'ngay' => "{$currentYear}-01-01",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_cau_hinh_ngay' => '2',
                'loai_ngay_le' => 1,
                'ten_ngay_le' => 'Giải Phóng Miền Nam',
                'ngay' => "{$currentYear}-04-30",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_cau_hinh_ngay' => '3',
                'loai_ngay_le' => 1,
                'ten_ngay_le' => 'Quốc Tế Lao Động',
                'ngay' => "{$currentYear}-05-01",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_cau_hinh_ngay' => '4',
                'loai_ngay_le' => 1,
                'ten_ngay_le' => 'Quốc Khánh',
                'ngay' => "{$currentYear}-09-02",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_cau_hinh_ngay' => '5',
                'loai_ngay_le' => 2,
                'ten_ngay_le' => 'Phụ Nữ Việt Nam',
                'ngay' => "{$currentYear}-10-20",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_cau_hinh_ngay' => '6',
                'loai_ngay_le' => 3,
                'ten_ngay_le' => 'Cuối tuần T1',
                'ngay' => "{$currentYear}-01-05", // Example weekend
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
