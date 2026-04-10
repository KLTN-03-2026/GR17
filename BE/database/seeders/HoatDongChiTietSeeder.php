<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HoatDongChiTietSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('hoat_dong_chi_tiet')->insert([
            [
                'ma_hoat_dong_chi_tiet' => '01',
                'ma_ke_hoach' => '001',
                'ma_nhom' => '001',
                'ma_dia_diem' => '001',
                'gio_bat_dau' => '08:00',
                'gio_ket_thuc' => '10:00',
                'ngay_cu_the' => '2026-03-20',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_hoat_dong_chi_tiet' => '02',
                'ma_ke_hoach' => '001',
                'ma_nhom' => '001',
                'ma_dia_diem' => '002',
                'gio_bat_dau' => '10:30',
                'gio_ket_thuc' => '12:00',
                'ngay_cu_the' => '2026-03-20',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_hoat_dong_chi_tiet' => '03',
                'ma_ke_hoach' => '002',
                'ma_nhom' => '002',
                'ma_dia_diem' => '003',
                'gio_bat_dau' => '14:00',
                'gio_ket_thuc' => '17:00',
                'ngay_cu_the' => '2026-03-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
