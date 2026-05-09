<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DanhSachYeuThichSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('danh_sach_yeu_thich')->insert([
            [
                'ma_danh_sach_ua_thich' => '001',
                'ma_khach_hang' => '1',
                'ma_dia_diem' => 'DN_TQ_001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_danh_sach_ua_thich' => '002',
                'ma_khach_hang' => '1',
                'ma_dia_diem' => 'DN_TQ_002',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_danh_sach_ua_thich' => '003',
                'ma_khach_hang' => '2',
                'ma_dia_diem' => 'DN_TQ_001',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
