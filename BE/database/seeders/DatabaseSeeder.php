<?php

namespace Database\Seeders;
use Database\Seeders\AdminSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();


$this->call([
            AdminSeeder::class,
            KhachHangSeeder::class,
            ChucVuSeeder::class,
            ChucNangSeeder::class,
            DiaDiemSeeder::class,
            TagSeeder::class,
            TagDiaDiemSeeder::class,
            PhanQuyenAdminSeeder::class,
            XeSeeder::class,
            KeHoachSeeder::class,
            XeKeHoachSeeder::class,
            NhomSeeder::class,
            ThanhVienNhomSeeder::class,
            DanhGiaKeHoachSeeder::class,
            HoaDonSeeder::class,
            HoatDongChiTietSeeder::class,
            TourSeeder::class,
            ChiTietTourSeeder::class,
            TourKhoiHanhSeeder::class,
            CauHinhNgaySeeder::class,
        ]);
    }
}
